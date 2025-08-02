<?php

namespace App\Services\MFA;

use App\Models\MFADevice;
use App\Models\User;
use App\Services\Cache;
use App\Utils\Tools;
use Cose\Algorithm\Manager;
use Cose\Algorithm\Signature\ECDSA;
use Cose\Algorithm\Signature\RSA;
use Cose\Algorithms;
use Exception;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\Serializer\SerializerInterface;
use Webauthn\AttestationStatement\AndroidKeyAttestationStatementSupport;
use Webauthn\AttestationStatement\AppleAttestationStatementSupport;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\FidoU2FAttestationStatementSupport;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AttestationStatement\PackedAttestationStatementSupport;
use Webauthn\AttestationStatement\TPMAttestationStatementSupport;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;

class WebAuthn
{
    public static int $timeout = 30_000;

    public static function RegisterRequest(User $user): array
    {
        $redis = (new Cache())->initRedis();
        try {
            $rpEntity = self::generateRPEntity();
            $userEntity = self::generateUserEntity($user);
            $authenticatorSelectionCriteria = AuthenticatorSelectionCriteria::create(
                userVerification: AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED,
                residentKey: AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED
            );
            $publicKeyCredentialCreationOptions =
                PublicKeyCredentialCreationOptions::create(
                    $rpEntity,
                    $userEntity,
                    random_bytes(32),
                    pubKeyCredParams: self::getPublicKeyCredentialParametersList(),
                    authenticatorSelection: $authenticatorSelectionCriteria,
                    attestation: PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
                    timeout: self::$timeout,
                );
            $serializer = self::getSerializer();
            $jsonObject = $serializer->serialize($publicKeyCredentialCreationOptions, 'json');
            $redis->setex('webauthn_register_' . session_id(), 300, $jsonObject);
            return json_decode($jsonObject, true);
        } catch (Exception $e) {
            return [
                'ret' => 0,
                'msg' => '请求失败: ' . $e->getMessage(),
            ];
        }
    }

    public static function generateRPEntity(): PublicKeyCredentialRpEntity
    {
        return PublicKeyCredentialRpEntity::create($_ENV['appName'], Tools::getSiteDomain());
    }

    public static function generateUserEntity(User $user): PublicKeyCredentialUserEntity
    {
        return PublicKeyCredentialUserEntity::create(
            $user->email,
            $user->uuid,
            $user->email
        );
    }

    public static function getPublicKeyCredentialParametersList(): array
    {
        return [
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ES256K),
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ES256),
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_RS256),
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_PS256),
            PublicKeyCredentialParameters::create('public-key', Algorithms::COSE_ALGORITHM_ED256),
        ];
    }

    public static function getSerializer(): SerializerInterface
    {
        $clock = new NativeClock();
        $coseAlgorithmManager = Manager::create();
        $coseAlgorithmManager->add(ECDSA\ES256::create());
        $coseAlgorithmManager->add(RSA\RS256::create());
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());
        $attestationStatementSupportManager->add(FidoU2FAttestationStatementSupport::create());
        $attestationStatementSupportManager->add(AppleAttestationStatementSupport::create());
        $attestationStatementSupportManager->add(AndroidKeyAttestationStatementSupport::create());
        $attestationStatementSupportManager->add(TPMAttestationStatementSupport::create($clock));
        $attestationStatementSupportManager->add(PackedAttestationStatementSupport::create($coseAlgorithmManager));
        $factory = new WebauthnSerializerFactory($attestationStatementSupportManager);
        return $factory->create();
    }

    public static function AssertRequest(): array
    {
        try {
            $publicKeyCredentialRequestOptions = self::getPublicKeyCredentialRequestOptions();
            $serializer = self::getSerializer();
            $jsonObject = $serializer->serialize($publicKeyCredentialRequestOptions, 'json');
            $redis = (new Cache())->initRedis();
            $redis->setex('webauthn_assertion_' . session_id(), 300, $jsonObject);
            return json_decode($jsonObject, true);
        } catch (Exception $e) {
            return [
                'ret' => 0,
                'msg' => '请求失败: ' . $e->getMessage(),
            ];
        }
    }

    public static function getPublicKeyCredentialRequestOptions(): PublicKeyCredentialRequestOptions
    {
        return PublicKeyCredentialRequestOptions::create(
            random_bytes(32),
            rpId: Tools::getSiteDomain(),
            userVerification: PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED,
            timeout: self::$timeout,
        );
    }

    public static function AssertHandle(array $data): array
    {
        $serializer = self::getSerializer();
        $publicKeyCredential = $serializer->deserialize(json_encode($data), PublicKeyCredential::class, 'json');
        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return ['ret' => 0, 'msg' => '验证失败'];
        }
        $publicKeyCredentialSource = (new MFADevice())
            ->where('rawid', $data['id'])
            ->where('type', 'passkey')
            ->first();
        if ($publicKeyCredentialSource === null) {
            return ['ret' => 0, 'msg' => '设备未注册'];
        }
        $user = (new User())->where('id', $publicKeyCredentialSource->userid)->first();
        if ($user === null) {
            return ['ret' => 0, 'msg' => '用户不存在'];
        }
        $redis = (new Cache())->initRedis();
        try {

            $publicKeyCredentialRequestOptions = $serializer->deserialize(
                $redis->get('webauthn_assertion_' . session_id()),
                PublicKeyCredentialRequestOptions::class,
                'json'
            );
            $authenticatorAssertionResponseValidator = self::getAuthenticatorAssertionResponseValidator();
            $publicKeyCredentialSource_body = $serializer->deserialize($publicKeyCredentialSource->body, PublicKeyCredentialSource::class, 'json');
            $result = $authenticatorAssertionResponseValidator->check(
                $publicKeyCredentialSource_body,
                $publicKeyCredential->response,
                $publicKeyCredentialRequestOptions,
                Tools::getSiteDomain(),
                $user->uuid,
            );
        } catch (Exception $e) {
            return ['ret' => 0, 'msg' => $e->getMessage()];
        }
        $publicKeyCredentialSource->body = $serializer->serialize($result, 'json');
        $publicKeyCredentialSource->used_at = date('Y-m-d H:i:s');
        $publicKeyCredentialSource->save();
        $redis->del('webauthn_assertion_' . session_id());
        return ['ret' => 1, 'msg' => '验证成功', 'user' => $user];
    }

    public static function getAuthenticatorAssertionResponseValidator(): AuthenticatorAssertionResponseValidator
    {
        $csmFactory = new CeremonyStepManagerFactory();
        $requestCSM = $csmFactory->requestCeremony();
        return AuthenticatorAssertionResponseValidator::create(
            ceremonyStepManager: $requestCSM
        );
    }

    public static function RegisterHandle(User $user, array $data): array
    {
        try {
            $serializer = self::getSerializer();
            try {
                $publicKeyCredential = $serializer->deserialize(
                    json_encode($data),
                    PublicKeyCredential::class,
                    'json'
                );
            } catch (Exception $e) {
                return ['ret' => 0, 'msg' => $e->getMessage()];
            }
            if (! isset($publicKeyCredential->response) || ! $publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
                return ['ret' => 0, 'msg' => '密钥类型错误'];
            }
            $redis = (new Cache())->initRedis();
            $publicKeyCredentialCreationOptions = $serializer->deserialize(
                $redis->get('webauthn_register_' . session_id()),
                PublicKeyCredentialCreationOptions::class,
                'json'
            );

            try {
                $authenticatorAttestationResponseValidator = self::getAuthenticatorAttestationResponseValidator();
                $publicKeyCredentialSource = $authenticatorAttestationResponseValidator->check(
                    $publicKeyCredential->response,
                    $publicKeyCredentialCreationOptions,
                    Tools::getSiteDomain(),
                );
            } catch (Exception) {
                return ['ret' => 0, 'msg' => '验证失败'];
            }
            // save public key credential source
            $jsonStr = self::getSerializer()->serialize($publicKeyCredentialSource, 'json');
            $jsonObject = json_decode($jsonStr);
            $webauthn = new MFADevice();
            $webauthn->userid = $user->id;
            $webauthn->rawid = $jsonObject->publicKeyCredentialId;
            $webauthn->body = $jsonStr;
            $webauthn->created_at = date('Y-m-d H:i:s');
            $webauthn->used_at = null;
            $webauthn->name = $data['name'] === '' ? null : $data['name'];
            $webauthn->type = 'passkey';
            $webauthn->save();
            $redis->del('webauthn_register_' . session_id());
            return ['ret' => 1, 'msg' => '注册成功'];
        } catch (Exception $e) {
            return ['ret' => 0, 'msg' => '请求失败: ' . $e->getMessage()];
        }
    }

    public static function getAuthenticatorAttestationResponseValidator(): AuthenticatorAttestationResponseValidator
    {
        $csmFactory = new CeremonyStepManagerFactory();
        $creationCSM = $csmFactory->creationCeremony();
        return AuthenticatorAttestationResponseValidator::create(
            ceremonyStepManager: $creationCSM
        );
    }
}