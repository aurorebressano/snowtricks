<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPwdType;
use App\Form\ResetPwdFormType;
use App\Form\UserType;
use App\Service\RegistrationMailInterface;
use App\Repository\UserRepository;
use App\Service\ResetMailInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(), 
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgotpwd', name: 'app_forgotpwd')]
    public function forgot(Request $request, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, ResetMailInterface $resetMailInterface, EntityManagerInterface $entityManager): Response
    {
        // Envoi de demande de reset mot de passe
        $form = $this->createForm(ResetPwdFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $email = $form->getData('email');
            $user = $userRepository->findOneByEmail($email);
            if($user && $user->isIsValidated() === true)
            {
                $user->setToken(bin2hex(random_bytes(32)));
                $userRepository->save($user, true);
                $resetMailInterface($user);
                $this->addFlash('success', 'Email de demande bien envoyé');
            } 
            else
            {
                $this->addFlash('warning', 'Veuillez d\'abord valider votre compte');
            }

            return $this->redirectToRoute('app_trick_index');
        }

        // Premier clic sur le "forgot password", on renvoie vers le formulaire de saisie d'email
        return $this->render('security/forgotpwd.html.twig', [
           "form"=> $form
        ]);

    }

    #[Route('/newpwd/{token}', name: 'app_newpwd')]
    public function newpwd(Request $request, UserPasswordHasherInterface $userPasswordHasher, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, $token): Response
    {
        $user = $userRepository->findOneByToken($token);
        $form = $this->createForm(NewPwdType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($user && $user->getToken() == $request->attributes->get('token'))
            {
                // delete token
                $user->setToken(null);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $userRepository->save($user, true);
                $this->addFlash('success', 'Mot de passe bien modifié');

                return $this->redirectToRoute('app_login',  [], Response::HTTP_SEE_OTHER);
            }
            else
            {
                $this->addFlash('warning', 'Une erreur est survenue, veuillez réessayer');
                $user->setToken(null);
                $userRepository->save($user, true);
                return $this->redirectToRoute('app_forgotpwd',  [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('security/resetpwd.html.twig', [  
            'form' => $form
        ]);
    }
}
