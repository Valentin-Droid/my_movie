<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\EditUserType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/account/{id}', name: 'app_account')]
    public function index(
        User $user,
        Request $request,
        UserPasswordHasherInterface $passwordEncoder
    ): Response
    {

        $userForm = $this->createForm(EditUserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
        }

        $passwordForm = $this->createForm(EditPasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
        }


        return $this->render('user/account.html.twig', [
            'controller_name' => 'UserController',
            'userForm' => $userForm->createView(),
            'passwordForm' => $passwordForm->createView()

        ]);
    }
}
