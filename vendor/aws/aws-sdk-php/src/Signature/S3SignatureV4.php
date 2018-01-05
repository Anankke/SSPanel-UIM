<?php
namespace Aws\Signature;

use Aws\Credentials\CredentialsInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Amazon S3 signature version 4 support.
 */
class S3SignatureV4 extends SignatureV4
{
    const UNSIGNED_PAYLOAD = 'UNSIGNED-PAYLOAD';
    
    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function signRequest(
        RequestInterface $request,
        CredentialsInterface $credentials
    ) {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader(
                'X-Amz-Content-Sha256',
                $this->getPayload($request)
            );
        }

        return parent::signRequest($request, $credentials);
    }

    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function presign(
        RequestInterface $request,
        CredentialsInterface $credentials,
        $expires
    ) {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader(
                'X-Amz-Content-Sha256',
                $this->getPresignedPayload($request)
            );
        }

        return parent::presign($request, $credentials, $expires);
    }

    /**
     * Override used to allow pre-signed URLs to be created for an
     * in-determinate request payload.
     */
    protected function getPresignedPayload(RequestInterface $request)
    {
        return self::UNSIGNED_PAYLOAD;
    }

    /**
     * Amazon S3 does not double-encode the path component in the canonical request
     */
    protected function createCanonicalizedPath($path)
    {
        return '/' . ltrim($path, '/');
    }
}
