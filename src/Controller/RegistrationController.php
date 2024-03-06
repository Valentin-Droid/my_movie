<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;


class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/signup', name: 'app_registration')]
    public function registerAction(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setCreativeAt(new \DateTime());
            $user->setRoles(["ROLE_USER"]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}
