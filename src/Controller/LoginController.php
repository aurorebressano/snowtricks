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
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\UserManipulation;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
            return $this->render('security/login.html.twig', [
                'last_username' => $authenticationUtils->getLastUsername(), 
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]);
    }

    #[Route('/loginsuccess', name: 'security_login_valid')] 
    public function loginsuccess(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
            $this->addFlash('success',  $translator->trans('Welcome') . $this->getUser());
            return $this->redirectToRoute('app_trick_index');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgotpwd', name: 'app_forgotpwd')]
    public function forgot(Request $request, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, ResetMailInterface $resetMailInterface, EntityManagerInterface $entityManager, TranslatorInterface $translator, UserManipulation $userManipulation): Response
    {
        $form = $this->createForm(ResetPwdFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $isAValidatedUser = $userManipulation->isAValidatedUSer($form);
            if($isAValidatedUser == true)
            {
                $this->addFlash('success', $translator->trans('Request email successfully sent'));
            }
            else
            {
                $this->addFlash('warning', $translator->trans('Please validate your account first'));
            }
            return $this->redirectToRoute('app_trick_index');
        }

        // Premier clic sur le "forgot password", on renvoie vers le formulaire de saisie d'email
        return $this->render('security/forgotpwd.html.twig', [
           "form"=> $form
        ]);

    }

    #[Route('/newpwd/{token}', name: 'app_newpwd')]
    public function newpwd(Request $request, UserPasswordHasherInterface $userPasswordHasher, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, TranslatorInterface $translator, UserManipulation $userManipulation, $token): Response
    {
        $form = $this->createForm(NewPwdType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $userRepository->findOneByToken($token);
            $goodToken = $userManipulation->newPasswordAttribution($token, $request, $form);
            if($goodToken == true)
            {
                $this->addFlash('success', $translator->trans('Password successfully changed'));
                return $this->redirectToRoute('app_login',  [], Response::HTTP_SEE_OTHER);
            }
            else
            {
                $this->addFlash('warning', $translator->trans('An error has occurred. Please try again'));
                return $this->redirectToRoute('app_forgotpwd',  [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('security/resetpwd.html.twig', [  
            'form' => $form
        ]);
    }
}
