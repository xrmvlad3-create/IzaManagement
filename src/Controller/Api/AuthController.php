<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Security\CookieJwtAuthenticator;
use App\Security\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    private string $appEnv;

    /**
     * Injectăm ParameterBagInterface pentru a accesa parametrii containerului
     * precum 'kernel.environment'.
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->appEnv = $params->get('kernel.environment');
    }

    #[Route('/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JwtService $jwtService
    ): JsonResponse {
        $data = $request->toArray();
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password fields are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtService->createToken($user);

        $cookie = Cookie::create(CookieJwtAuthenticator::COOKIE_NAME)
            ->withValue($token)
            ->withExpires(new \DateTimeImmutable('+30 days'))
            ->withPath('/')
            ->withSameSite(Cookie::SAMESITE_STRICT)
            ->withHttpOnly(true)
            ->withSecure($this->appEnv === 'prod');

        $response = new JsonResponse([
            'message' => 'Login successful.',
            'user' => ['id' => $user->getId()->toString(), 'email' => $user->getEmail(), 'roles' => $user->getRoles()]
        ]);
        $response->headers->setCookie($cookie);

        return $response;
    }

    #[Route('/logout', name: 'api_auth_logout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Successfully logged out.']);
        // Cea mai fiabilă metodă de a șterge un cookie este să-i spui browser-ului că a expirat.
        $response->headers->clearCookie(CookieJwtAuthenticator::COOKIE_NAME);

        return $response;
    }

    #[Route('/refresh', name: 'api_auth_refresh', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function refresh(JwtService $jwtService): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $token = $jwtService->createToken($user);

        $cookie = Cookie::create(CookieJwtAuthenticator::COOKIE_NAME)
            ->withValue($token)
            ->withExpires(new \DateTimeImmutable('+30 days'))
            ->withPath('/')
            ->withSameSite(Cookie::SAMESITE_STRICT)
            ->withHttpOnly(true)
            ->withSecure($this->appEnv === 'prod');

        $response = new JsonResponse(['message' => 'Token refreshed successfully.']);
        $response->headers->setCookie($cookie);

        return $response;
    }
}
