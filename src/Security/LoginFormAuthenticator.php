<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {   
        // Récupérer info sur le user
        // $token->getUser();
        $user = $token->getUser();

        $roleNames = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roleNames)
        || in_array('ROLE_TEACHER', $roleNames)) {
        // redirection vers la page des promos
        return new RedirectResponse($this->urlGenerator->generate('school_year_index'));
        } elseif (in_array('ROLE_STUDENT', $roleNames)) {
        // redirection vers la page de sa promo
        return new RedirectResponse($this->urlGenerator->generate('school_year_show', [
            'id' => $user->getSchoolYear()->getId(),
        ]));
        } elseif (in_array('ROLE_CLIENT', $roleNames)) {
        // redirection vers la page de ses projets
        return new RedirectResponse($this->urlGenerator->generate('project_index'));
        }


        // VERSION SWITCH
        // switch (true) {
        //     case inArray('ROLE_ADMIN', $roleNames):
        //     case inArray('ROLE_TEACHER', $roleNames):
        //         // REDIRECTION VERS PAGE DES PROMOS
        //         return new RedirectResponse($this->urlGenerator->generate('school_year_index'));
        //     case inArray('ROLE_STUDENT', $roleNames):
        //         /// REDIRECTION VERS PAGE DE SA PROMO
        //         return new RedirectResponse($this->urlGenerator->generate('school_year_show', [
        //             'id' => $user->getSchoolYear()->getId(),
        //         ]));
        //     case inArray('ROLE_CLIENT', $roleNames):
        //         // REDIRECTION VERS PAGE DES PROJETS
        //         return new RedirectResponse($this->urlGenerator->generate('project_index'));
        //     }


        // Redirige après connexion
        // if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
        //     return new RedirectResponse($targetPath);
        // }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}