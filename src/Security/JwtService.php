<?php

namespace App\Security;

use App\Entity\User;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Validation\Validator;

class JwtService
{
    private readonly Configuration $config;
    private readonly string $secret;

    public function __construct(string $appSecret)
    {
        $this->secret = $appSecret;
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secret)
        );
    }

    public function createToken(User $user): string
    {
        $now = new DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedBy('dermatology-platform')
            ->issuedAt($now)
            ->expiresAt($now->modify('+30 days'))
            ->relatedTo($user->getId()->toString())
            ->withClaim('roles', $user->getRoles())
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    public function parseToken(string $jwt): ?Token
    {
        try {
            $token = $this->config->parser()->parse($jwt);
        } catch (\Exception $e) {
            return null;
        }

        $validator = new Validator();
        if (!$validator->validate($token, new SignedWith($this->config->signer(), $this->config->signingKey()))) {
            return null;
        }

        $clock = new SystemClock(new \DateTimeZone('UTC'));
        if (!$validator->validate($token, new ValidAt($clock))) {
            return null;
        }

        return $token;
    }
}
