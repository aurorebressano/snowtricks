<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Entity\Trick;
use App\Form\UserType;
use App\Form\TrickType;
use App\Repository\AvatarRepository;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_PUBLISHER')]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('view', $user);
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PUBLISHER')]
    public function edit(Request $request, User $user, UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('edit', $user);
        $form = $this->createForm(
            UserType::class, 
            $user,
            ["validation_groups" => "edit"]
        );
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {

            $fileUploader->uploadPersonnalPicture($user, $form);

            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId() ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PUBLISHER')]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $user);
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
