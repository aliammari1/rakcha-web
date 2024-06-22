<?php

namespace App\Security;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    private UsersRepository $usersRepository;
    private RouterInterface $router;
    public function __construct(UsersRepository $usersRepository, RouterInterface $router)
    {
        $this->usersRepository = $usersRepository;
        $this->router = $router;
    }


    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        return new Passport(new UserBadge($email, function ($userIdentifier) {
            $user = $this->usersRepository->findOneBy(['email' => $userIdentifier]);
            if (!$user) {
                throw new UserNotFoundException();
            }

            return $user;
        }), new PasswordCredentials($password), [
            new CsrfTokenBadge('authenticate', $request->request->get("_csrf_token")),
            new RememberMeBadge()
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $user = $token->getUser();
        if ($user instanceof Users) {
            if ($user->getRole() == 'client')
                return new RedirectResponse($this->router->generate('app_home_index'));
            else if ($user->getRole() == 'responsable de cinema')
                return new RedirectResponse($this->router->generate('app_cinema_index'));
            else if ($user->getRole() == 'admin')
                return new RedirectResponse($this->router->generate('app_users_index'));
        }
        return new RedirectResponse($this->router->generate('app_home_index'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_login');
    }
    
}
