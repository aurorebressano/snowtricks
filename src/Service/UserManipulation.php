<?php

namespace App\Service;

use App\Service\ResetMail;
use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserManipulation
{
    public function __construct(private TrickRepository $trickRepository, private MessageRepository $messageRepository, private UserRepository $userRepository, private UserPasswordHasherInterface $userPasswordHasher, private EntityManagerInterface $entityManager, private ResetMail $resetMail, private TranslatorInterface $translator)
    {
        
    }

    public function setPassword(User $user, Form $form)
    {
         //encode the plain password
         $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user, $form->get('plainPassword')->getData()
            )
        );
    }

    public function saveUserRegistered(User $user)
    {
        // encode token
        $user->setToken(bin2hex($user->getEmail()));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function validateUser(User $user)
    {
        // delete token
        $user->setToken(null);
        $user->setIsValidated(true);
        $user->setRoles(['ROLE_USER', 'ROLE_PUBLISHER']);

        $this->userRepository->save($user, true);
    }

    public function isAValidatedUSer(Form $form) : bool
    {
        $isOkay = false;

        $email = $form->getData('email');
        $user = $this->userRepository->findOneByEmail($email);
        if($user && $user->isIsValidated() === true)
        {
            $isOkay = true;
            $user->setToken(bin2hex(random_bytes(32)));
            $this->userRepository->save($user, true);
            $this->resetMail->__invoke($user);
        }

        return $isOkay;
    }

    public function newPasswordAttribution(String $token, Request $request, Form $form) : bool
    {
        $goodToken = false;
        $user = $this->userRepository->findOneByToken($token);
        if($user && $user->getToken() == $request->attributes->get('token'))
        {
            $user->setToken(null);
            $this->setPassword($user, $form);
            $this->userRepository->save($user, true);
            $goodToken = true;
        }
        else
        {
            $user->setToken(null);
            $this->userRepository->save($user, true);
        }
        return $goodToken;
    }
}