<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Service\RegistrationMailInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, RegistrationMailInterface $registrationMailInterface): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            //encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            if ($form->isValid()) {
                // encode token
                $user->setToken(bin2hex($user->getEmail()));
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email
                $registrationMailInterface($user);

                $this->addFlash(
                    'notice',
                    'Mail for validation is send, check your inbox'
                );
                return $this->redirectToRoute('app_trick_index');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registervalidation/{token}', name: 'app_registervalidation')]
    public function registerValidation(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, $token): Response
    {
        $user = $userRepository->findOneByToken($token);
        if($user && $user->getToken() == $request->attributes->get('token'))
        {
            // delete token
            $user->setToken(null);
            $user->setIsValidated(true);
            $user->setRoles(['ROLE_USER', 'ROLE_PUBLISHER']);

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_login');
        }
        else
        {
            return $this->redirectToRoute('app_trick_index');
        }
    }
}
