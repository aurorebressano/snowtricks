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
use App\Service\TrickManipulation;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, TrickRepository $trickRepository, UserRepository $UserRepository, FileUploader $fileUploader, TranslatorInterface $translator, TrickManipulation $trickManipulation): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, ["validation_groups" => "new"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $trickManipulation->createTrick($this->getUser(), $trick);
            $this->addFlash('success', $translator->trans('Trick.created'));
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
        UserRepository $userRepository,
        TrickManipulation $trickManipulation
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickManipulation->showTrick($this->getUser(), $trick , $message);
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
        FileUploader $fileUploader,
        TranslatorInterface $translator,
        TrickManipulation $trickManipulation
    ): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick,  $translator->trans("Access denied, you can't edit this trick"));

        foreach($trick->getPictures() as $picture)
        {
            $picture->setFile(new File($this->getParameter("picture_directory")."/".$picture->getPath()));
        }

        $form = $this->createForm(
            TrickType::class, 
            $trick,
            ["validation_groups" => "edit"]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $trickManipulation->editTrick($this->getUser(),$trick); 
            $this->addFlash('success', $translator->trans("Trick.edited"));
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
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('delete', $trick, $translator->trans("Access denied, you can't delete this trick"));
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
            $this->addFlash('notice', $translator->trans("Trick.deleted"));
        }
        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
