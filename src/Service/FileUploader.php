<?php

namespace App\Service;

use App\Entity\Avatar;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader{
    public function __construct(private SluggerInterface $sluggerInterface, private $targetDirectory)
    {
        
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->sluggerInterface->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException($e);
        }

        return $fileName;
    }

    public function uploadPicture(Trick $trick)
    {
        foreach($trick->getPictures() as $picture)
        {
            if($picture->getFile() !== null)
            {
                $picture->setPath($this->upload($picture->getFile()));
            }
            elseif($picture->getPath() === null && $picture->getFile() === null)
            {
                $trick->removePicture($picture);
            }
        }
    }

    public function uploadPersonnalPicture(User $user, $form)
    {
        if($user->getFile() !== null)
        {
            $user->setPicture($this->upload($user->getFile()));
        }
    }

    public function uploadVideo(Trick $trick)
    {
        foreach($trick->getVideos() as $video)
        {
            $check = parse_url($video->getPath(), PHP_URL_HOST);
            parse_str(parse_url($video->getPath(), PHP_URL_QUERY), $videoId);
            if($check === "www.youtube.com" && array_key_exists("v", $videoId))
            {
                $video->setVideoId($videoId["v"]);
                $trick->addVideo($video);
            }
            elseif($video->getPath() === null || $video->getVideoId() === null)
            {
                $trick->removeVideo($video);
            }
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}