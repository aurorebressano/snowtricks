<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Video;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        // users

        $usersDatas = array(
            ['email' => 'admin@admin.fr', 
            'role' => ["ROLE_ADMIN", "ROLE_PUBLISHER"], 
            'name' => 'Administrateur', 
            'firstname' => 'test', 
            'picture' => '6110WZf33tL-6485984ab7bca.jpg'],
            ['email' =>'test@gmail.com', 
            'role' => ["ROLE_USER", "ROLE_PUBLISHER"], 
            'name' =>'testUser', 
            'firstname' =>'Lambda', 
            'picture' => '6110WZf33tL-646a3ea52412a-6485f50fb022f.jpg']
        );

        // tricks

        $tricksDatas = array(
            ['sad', 'saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.', '2023-04-10 14:31:03', '2023-05-28 12:56:00', 'sad'],
            ['indy', 'saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière', '2023-04-15 18:29:25', NULL, 'indy'],
            ['stalefish', 'saisie de la carre backside de la planche entre les deux pieds avec la main arrière', '2023-04-15 18:30:15', NULL, 'stalefish'],
            ['tail grab', 'saisie de la partie arrière de la planche, avec la main arrière', '2023-04-15 18:30:44', NULL, 'tail-grab'],
            ['nose grab', 'saisie de la partie avant de la planche, avec la main avant', '2023-04-15 18:31:23', NULL, 'nose-grab'],
            ['japan air', "saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.", '2023-04-15 18:33:05', NULL, 'japan-air'],
            ['seat belt', 'saisie du carre frontside à l\'arrière avec la main avant', '2023-04-15 19:20:01', NULL, 'seat-belt'],
            ['truck driver', 'saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)', '2023-04-15 19:20:56', NULL, 'truck-driver'],
            ['180', "désigne un demi-tour, soit 180 degrés d'angle", '2023-04-15 19:21:36', NULL, '180'],
            ['front flip', 'rotation en avant', '2023-04-15 19:23:27', NULL, 'front-flip'],
            ['corkscrew', 'rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation', '2023-04-15 19:24:06', NULL, 'corkscrew'],
            ['rocket air', 'manière de réaliser des figures passée de mode, qui fait penser au freestyle des années 1980', '2023-04-15 19:25:25', '2023-05-28 12:24:37', 'rocket-air']
        );

        // categories

        $categoriesDatas = [
            'grabs',
            'rotations',
            'flips',
            'rotations désaxées',
            'slides',
            'one foot',
            'Old school'
        ];

        // pictures

        $picturesDatas = [
            ['path' => 'backsideair_mini.png'],
            ['path' => 'japanair_mini.png'],
            ['path' => 'nosegrab_mini.png'],
            ['path' => 'tailgrab_mini.png'],
            ['path' => 'mute_mini.png'],
            ['path' => 'stalefish.jpg'],
            ['path' => 'nosegrab.jpg'],
            ['path' => 'japanair.jpg'],
            ['path' => 'sad.png'],
            ['path' => 'stalefish.jpg'],
            ['path' => 'tailgrab.jpg'],
            ['path' => 'nosegrab.jpg']
        ];

        // videos

        $videosDatas = [
            ['path' => 'https://www.youtube.com/embed?v=mBB7CznvSPQ', 'video_id' => 'mBB7CznvSPQ'],
            ['path' => 'https://www.youtube.com/embed?v=SFYYzy0UF-8', 'video_id' => 'SFYYzy0UF-8'],
            ['path' => 'https://www.youtube.com/watch?v=CEUpFZEb2mY', 'video_id' => 'CEUpFZEb2mY'],
            ['path' => 'https://www.youtube.com/watch?v=OmXJqHe1c2Q', 'video_id' => 'OmXJqHe1c2Q'],
            ['path' => 'https://www.youtube.com/embed?v=mBB7CznvSPQ', 'video_id' => 'mBB7CznvSPQ'],
            ['path' => 'https://www.youtube.com/embed?v=SFYYzy0UF-8', 'video_id' => 'SFYYzy0UF-8'],
            ['path' => 'https://www.youtube.com/watch?v=CEUpFZEb2mY', 'video_id' => 'CEUpFZEb2mY'],
            ['path' => 'https://www.youtube.com/watch?v=OmXJqHe1c2Q', 'video_id' => 'OmXJqHe1c2Q'],
            ['path' => 'https://www.youtube.com/embed?v=mBB7CznvSPQ', 'video_id' => 'mBB7CznvSPQ'],
            ['path' => 'https://www.youtube.com/embed?v=SFYYzy0UF-8', 'video_id' => 'SFYYzy0UF-8'],
            ['path' => 'https://www.youtube.com/watch?v=CEUpFZEb2mY', 'video_id' => 'CEUpFZEb2mY'],
            ['path' => 'https://www.youtube.com/watch?v=OmXJqHe1c2Q', 'video_id' => 'OmXJqHe1c2Q']
        ];

        $users = array();

        foreach($usersDatas as $data)
        {
            $user = new User();
            $user->setEmail($data['email'])
            ->setIsValidated(true)
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'test'))
            ->setName($data['name'])
            ->setFirstname(($data['firstname']))
            ->setRoles($data['role'])
            ->setPicture($data['picture']);

            array_push($users, $user);

            $manager->persist($user);
        }

        $categories = array();
        foreach($categoriesDatas as $categD)
        {
            $category = new Category();
            $category->setTitle($categD);

            array_push($categories, $category);

            $manager->persist($category);
        }

        $pictures = [];
        $videos = [];

        foreach($tricksDatas as $key => $tricksD)
        {
            $trick = new Trick();

            $picture = new Picture();
            $picture->setPath($picturesDatas[$key]['path']);
            $picture->setHeader(1);
            $picture->setTrick($trick);
            $picture->setPublisher($users[array_rand($users)]);

            array_push($pictures, $picture);

            $manager->persist($picture);

            $video = new Video();
            $video->setPath($videosDatas[$key]['path']);
            $video->setTrick($trick);
            $video->setVideoId($videosDatas[$key]['video_id']);
            $video->setPublisher($users[array_rand($users)]);

            array_push($videos, $video);

            $manager->persist($video);

            $trick->setUser($users[array_rand($users)]);
            $trick->setCategory($categories[array_rand($categories)]);
            $trick->addPicture($pictures[array_rand($pictures)]);
            $trick->addVideo($videos[array_rand($videos)]);
            $trick->setName($tricksD[0]);
            $trick->setDescription($tricksD[1]);
            $trick->setSlug($tricksD[0]);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
