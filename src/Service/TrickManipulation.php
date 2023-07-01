<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Trick;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;


class TrickManipulation
{
    public function __construct(private FileUploader $fileUploader, private TrickRepository $trickRepository, private MessageRepository $messageRepository)
    {
        
    }

    public function createTrick(User $user, Trick $trick)
    {
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
        $this->fileUploader->uploadPicture($trick);
        $this->fileUploader->uploadVideo($trick);

        $this->trickRepository->save($trick, true);
    }

    public function showTrick(User $user, Trick $trick, Message $message)
    {
        $message->setTrick($trick);
        $message->setAuthor($user);
        $this->messageRepository->save($message, true);
    }

    public function editTrick(User $user, Trick $trick)
    {
        foreach($trick->getPictures() as $picture)
        {
            $picture->setPublisher($user);
        }
        foreach($trick->getVideos() as $video)
        {
            $video->setPublisher($user);
        } 

        $trick->setUpdateDate(new \DateTimeImmutable());

        $this->fileUploader->uploadPicture($trick);
        $this->fileUploader->uploadVideo($trick);

        $this->trickRepository->save($trick, true);
    }
}