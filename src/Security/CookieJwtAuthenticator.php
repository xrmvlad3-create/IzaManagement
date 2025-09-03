<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Ramsey\Uuid\Uuid;

class CookieJwtAuthenticator extends AbstractAuthenticator
{
    public const COOKIE_NAME = 'BEARER';

    public function __construct(
        private readonly JwtService $jwtService,
        private readonly UserRepository $userRepository
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // Acest authenticator rulează doar pentru rutele ce încep cu /api/ și doar dacă există cookie-ul.
        return $request->cookies->has(self::COOKIE_NAME) && str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $jwt = $request->cookies->get(self::COOKIE_NAME);
        if (null === $jwt) {
            throw new CustomUserMessageAuthenticationException('Authentication cookie not found.');
        }

        $token = $this->jwtService->parseToken($jwt);
        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('Invalid or expired authentication token.');
        }

        $userId = $token->claims()->get('sub');

        if (!Uuid::isValid($userId)) {
            throw new CustomUserMessageAuthenticationException('Invalid user identifier in token.');
        }

        return new SelfValidatingPassport(new UserBadge($userId, fn() => $this->userRepository->find($userId)));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Se continuă către controller
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = new JsonResponse(
            ['error' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED
        );

        // Se șterge cookie-ul invalid
        $response->headers->setCookie(Cookie::create(self::COOKIE_NAME)->withExpires(new \DateTimeImmutable('-1 day')));

        return $response;
    }
}
