<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\User;
use App\Form\TrickType;
use App\Form\MessageType;
use App\Repository\TrickRepository;
use App\Repository\MessageRepository;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, TrickRepository $trickRepository, UserRepository $UserRepository, FileUploader $fileUploader): Response
    {
        $trick = new Trick();
        $form = $this->createForm(
            TrickType::class,
            $trick
        );
        $form->handleRequest($request);
        // dd($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // dd($request);
            // Version test
            $user = $this->getUser();
            foreach($trick->getPictures() as $picture)
            {
                $picture->setPublisher($user);
            }
            foreach($trick->getVideos() as $video)
            {
                $video->setPublisher($user);
            }
            $trick->setUser($user);
            $slugger = new AsciiSlugger();
            $trick->setSlug(strToLower($slugger->slug($trick->getName())));
            $fileUploader->uploadPicture($trick);
            $fileUploader->uploadVideo($trick);
            //$trick->setUserId($this->getUser());
            //$slugger = new AsciiSlugger();
            //$trick->setSlug(strToLower($slugger->slug($trick->getName())));

            $trickRepository->save($trick, true);
            $this->addFlash('success', 'Nouvelle figure ' .$trick->getName(). ' créée !');
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/figure/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Trick $trick,
        MessageRepository $messageRepository,
        UserRepository $userRepository
    ): Response {
        // dd($request);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // user test
            $user = $this->getUser();
            $message->setTrick($trick);
            $message->setAuthor($user);
            $messageRepository->save($message, true);
            return $this->redirectToRoute('app_trick_show', [
                'slug'=>$trick->getSlug()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/figure/edit/{id}', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Request $request, 
        Trick $trick, 
        TrickRepository $trickRepository,
        UserRepository $userRepository,
        PictureRepository $pictureRepository,
        FileUploader $fileUploader
    ): Response
    {

        $currentUser = $this->getUser();
        // if ($trick->getUser() !== $currentUser && !$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException();
        // }
        $this->denyAccessUnlessGranted('edit', $trick);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        // dd($form->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            foreach($trick->getPictures() as $picture)
            {
                $picture->setPublisher($user);
            }
            foreach($trick->getVideos() as $video)
            {
                $video->setPublisher($user);
            } 

            $trick->setUpdateDate(new \DateTimeImmutable());

            $fileUploader->uploadPicture($trick);
            $fileUploader->uploadVideo($trick);

            $trickRepository->save($trick, true);
            $this->addFlash('success', 'Figure bien éditée !');
            return $this->redirectToRoute('app_trick_show', [
                'slug'=>$trick->getSlug()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/figure/delete/{id}', name: 'app_trick_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $trick);
        $currentUser = $this->getUser();
        if ($trick->getUser() !== $currentUser && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
            $this->addFlash(
                'notice',
                'Trick deleted!'
            );
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
