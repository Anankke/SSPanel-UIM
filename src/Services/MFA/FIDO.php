<?php

namespace App\Services\MFA;

use App\Models\MFADevice;
use App\Models\User;
use App\Services\Cache;
use App\Utils\Tools;
use Exception;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialSource;

class FIDO
{

    public static function RegisterRequest(User $user): array
    {
        $rpEntity = WebAuthn::generateRPEntity();
        $userEntity = WebAuthn::generateUserEntity($user);
        $authenticatorSelectionCriteria = AuthenticatorSelectionCriteria::create();
        $publicKeyCredentialCreationOptions =
            PublicKeyCredentialCreationOptions::create(
                $rpEntity,
                $userEntity,
                random_bytes(32),
                pubKeyCredParams: WebAuthn::getPublicKeyCredentialParametersList(),
                authenticatorSelection: $authenticatorSelectionCriteria,
                attestation: PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
                timeout: WebAuthn::$timeout,
            );
        $serializer = WebAuthn::getSerializer();
        $jsonObject = $serializer->serialize($publicKeyCredentialCreationOptions, 'json');
        $redis = (new Cache())->initRedis();
        $redis->setex('fido_register_' . session_id(), 300, $jsonObject);
        return json_decode($jsonObject, true);
    }

    public static function RegisterHandle(User $user, array $data): array
    {
        $serializer = WebAuthn::getSerializer();
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
            $redis->get('fido_register_' . session_id()),
            PublicKeyCredentialCreationOptions::class,
            'json'
        );

        try {
            $authenticatorAttestationResponseValidator = WebAuthn::getAuthenticatorAttestationResponseValidator();
            $publicKeyCredentialSource = $authenticatorAttestationResponseValidator->check(
                $publicKeyCredential->response,
                $publicKeyCredentialCreationOptions,
                Tools::getSiteDomain()
            );
        } catch (Exception) {
            return ['ret' => 0, 'msg' => '验证失败'];
        }
        $jsonStr = WebAuthn::getSerializer()->serialize($publicKeyCredentialSource, 'json');
        $jsonObject = json_decode($jsonStr);
        $mfaCredential = new MFADevice();
        $mfaCredential->userid = $user->id;
        $mfaCredential->rawid = $jsonObject->publicKeyCredentialId;
        $mfaCredential->body = $jsonStr;
        $mfaCredential->created_at = date('Y-m-d H:i:s');
        $mfaCredential->used_at = null;
        $mfaCredential->name = $data['name'] === '' ? null : $data['name'];
        $mfaCredential->type = 'fido';
        $mfaCredential->save();
        $redis->del('fido_register_' . session_id());
        return ['ret' => 1, 'msg' => '注册成功'];
    }

    public static function AssertRequest(User $user): array
    {
        try {
            $serializer = WebAuthn::getSerializer();
            $userCredentials = (new MFADevice())
                ->where('userid', $user->id)
                ->where('type', 'fido')
                ->select('body')->get();
            $credentials = [];
            foreach ($userCredentials as $credential) {
                $credentials[] = $serializer->deserialize($credential->body, PublicKeyCredentialSource::class, 'json');
            }
            $allowedCredentials = array_map(
                static function (PublicKeyCredentialSource $credential): PublicKeyCredentialDescriptor {
                    return $credential->getPublicKeyCredentialDescriptor();
                },
                $credentials
            );
            $publicKeyCredentialRequestOptions = PublicKeyCredentialRequestOptions::create(
                random_bytes(32),
                rpId: Tools::getSiteDomain(),
                allowCredentials: $allowedCredentials,
                userVerification: 'discouraged',
                timeout: WebAuthn::$timeout,
            );
            $jsonObject = $serializer->serialize($publicKeyCredentialRequestOptions, 'json');
            $redis = (new Cache())->initRedis();
            $redis->setex('fido_assertion_' . session_id(), 300, $jsonObject);
            return json_decode($jsonObject, true);
        } catch (Exception $e) {
            return ['ret' => 0, 'msg' => '请求失败: ' . $e->getMessage()];
        }
    }

    public static function AssertHandle(?User $user, array $data): array
    {
        $serializer = WebAuthn::getSerializer();
        $publicKeyCredential = $serializer->deserialize(json_encode($data), PublicKeyCredential::class, 'json');
        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return ['ret' => 0, 'msg' => '验证失败'];
        }
        $publicKeyCredentialSource = (new MFADevice())
            ->where('rawid', $data['id'])
            ->where('userid', $user->id)
            ->where('type', 'fido')
            ->first();
        if ($publicKeyCredentialSource === null) {
            return ['ret' => 0, 'msg' => '设备未注册'];
        }
        $redis = (new Cache())->initRedis();
        try {
            $publicKeyCredentialRequestOptions = $serializer->deserialize(
                $redis->get('fido_assertion_' . session_id()),
                PublicKeyCredentialRequestOptions::class,
                'json'
            );
            $authenticatorAssertionResponseValidator = WebAuthn::getAuthenticatorAssertionResponseValidator();
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
        $redis->del('fido_assertion_' . session_id());
        return ['ret' => 1, 'msg' => '验证成功', 'userid' => $user->id];
    }
}