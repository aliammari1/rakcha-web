<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\AdminFormType;
use App\Form\RegistrationFormType;
use App\Repository\FriendshipsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UsersController extends AbstractController
{

    #[Route('/usersDash', name: 'app_users_index', methods: ['GET', 'POST'])]
    public function index(UsersRepository $usersRepository, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(AdminFormType::class, new Users());
        $updateForms = array();
        for ($i = 0; $i < count($usersRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(RegistrationFormType::class, $usersRepository->findAll()[$i])->createView();
        }
        $users = $usersRepository->findAll();
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('back/UserTables.html.twig', [
            'pagination' => $pagination,
            'users' => $users,
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }

    #[Route('/users/profile/{id}', name: 'app_profile_index', methods: ['GET', 'POST'])]
    public function userProfile($id, UsersRepository $usersRepository, FriendshipsRepository $friendshipsRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $usersRepository->find($id);
        $statut = null;
        if ($user != $this->getUser()) {
            $statut = $friendshipsRepository->findOneBy(['sender' => $this->getUser(), 'receiver' => $user])?->getStatut();
            if ($statut == null) {
                $statut = $friendshipsRepository->findOneBy(['sender' => $user, 'receiver' => $this->getUser()])?->getStatut();
                if ($statut != null && $statut == 'pending friend request')
                    $statut = 'waiting for response';
                else if ($statut != null && $statut == 'accepted friend request')
                    $statut = 'accepted friend request';
            }
        }
        if ($statut == null)
            $statut = 'no friend request';
        return $this->render('back/profile.html.twig', [
            'user' => $user,
            'statut' => $statut,
        ]);
    }

    #[Route('/users/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository, PaginatorInterface $paginator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Users();
        $updateForms = array();
        for ($i = 0; $i < count($usersRepository->findAll()); $i++) {
            $updateForms[$i] = $this->createForm(RegistrationFormType::class, $usersRepository->findAll()[$i])->createView();
        }
        $pagination = $paginator->paginate(
            $usersRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        $form = $this->createForm(AdminFormType::class, $user);

        $form->handleRequest($request);

        $plainPassword = $form->get('plainPassword')->getData();

        if ($plainPassword !== null && !empty($plainPassword)) {
            $encodedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        $file = $form['photoDeProfil']->getData();

        if ($file) {
            $extension = $file->guessExtension();
            if (!$extension) {
                $extension = 'bin';
            }
            $filename = rand(1, 99999) . '.' . $extension;
            $file->move($this->getParameter('kernel.project_dir') . "/public/img/users", $filename);
            $user->setPhotoDeProfil("/img/users/" . $filename);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setIsVerified(true);
            $role = $user->getRole();
            switch ($role) {
                case 'client':
                    $user->setRoles(['ROLE_CLIENT']);
                    break;
                case 'admin':
                    $user->setRoles(['ROLE_ADMIN']);
                    break;
                case 'responsable de cinema':
                    $user->setRoles(['ROLE_RESPONSABLE_DE_CINEMA']);
                    break;
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('users', 'User Created successfully');
            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }
        $hasErrorsCreate = true;
        return $this->render('back/UserTables.html.twig', [
            'pagination' => $pagination,
            'users' => $usersRepository->findAll(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
            'hasErrorsCreate' => $hasErrorsCreate,
        ]);
    }

    #[Route('/users/{id}', name: 'app_users_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        return new Response("<h1>show</h1>");
    }

    #[Route('/users/{id}/edit/{formUpdateNumber}/', name: 'app_users_edit', methods: ['GET', 'POST'])]
    public function edit($formUpdateNumber, Request $request, Users $user, EntityManagerInterface $entityManager, UsersRepository $usersRepository, PaginatorInterface $paginator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $updateForms = array();
        $users = $usersRepository->findAll();
        for ($i = 0; $i < count($users); $i++) {
            $updateForms[$i] = $this->createForm(RegistrationFormType::class, $users[$i])->createView();
        }
        $pagination = $paginator->paginate(
            $usersRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );


        $form = $this->createForm(AdminFormType::class, new Users());

        $updateform = $this->createForm(RegistrationFormType::class, $user);

        $updateform->handleRequest($request);

        $plainPassword = $updateform->get('plainPassword')->getData();
        if ($plainPassword !== null) {
            $encodedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        $file = $updateform->get('photoDeProfil')->getData();

        if ($file) {
            $extension = $file->guessExtension();
            if (!$extension) {
                $extension = 'bin';
            }
            $filename = rand(1, 99999) . '.' . $extension;
            $file->move($this->getParameter('kernel.project_dir') . "/public/img/users", $filename);
            $user->setPhotoDeProfil("/img/users/" . $filename);
        }

        if ($updateform->isSubmitted() && $updateform->isValid()) {
            if ($user->getRole() == 'client')
                $user->setRoles(['ROLE_CLIENT']);
            else if ($user->getRole() == 'admin')
                $user->setRoles(['ROLE_ADMIN']);
            else if ($user->getRole() == 'responsable de cinema')
                $user->setRoles(['ROLE_RESPONSABLE_DE_CINEMA']);

            $entityManager->flush();
            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        } else {
        }

        $entityManager->refresh($user);
        return $this->render('back/UserTables.html.twig', [
            'pagination' => $pagination,
            'users' => $usersRepository->findAll(),
            "formUpdateNumber" => $formUpdateNumber,
            'updateform' => $updateform->createView(),
            'form' => $form->createView(),
            'updateForms' => $updateForms,
        ]);
    }

    #[Route('/users/{id}', name: 'app_users_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        } else {
        }
        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/account/delete', name: 'app_account_delete', methods: ['POST'])]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, TokenStorageInterface $tokenStorage): Response
    {
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();


            $session->invalidate();
            $tokenStorage->setToken(null);
        } else {
        }

        return $this->redirectToRoute('app_home_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user instanceof Users) {

            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $dateOfBirth = $request->request->get('dob');
            $email = $request->request->get('email');
            $address = $request->request->get('address');
            $phoneNumber = $request->request->get('phone_number');

            $user->setPrenom($firstName);
            $user->setNom($lastName);
            $user->setDateDeNaissance(new \DateTime($dateOfBirth));
            $user->setEmail($email);
            $user->setAdresse($address);
            $user->setNumTelephone($phoneNumber);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }
}
