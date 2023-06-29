<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegistrationMail implements RegistrationMailInterface
{
    public function __construct(private MailerInterface $mailer)
    {
        
    }

    public function __invoke(User $user): void
    {
        // Create a Transport object
        // dd('test');
        $emailSender = strip_tags('aurorebressano@gmail.com');
        $message = "<p>Cliquez sur ce lien pour confirmer votre email: <a href='https://127.0.0.1:8000/registervalidation/" .$user->getToken() . "'>Confirmer !</a></p>";
        $transport = Transport::fromDsn('smtp://6c8124977333a7:1d79ef799b3ca7@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login');
        
        // Create a Mailer object
        $mailer = new Mailer($transport); 

        // Create an Email object
        $email = (new Email());
        
        // Set the "From address"
        $email->from($emailSender);
        
        // Set the "From address"
        $email->to($emailSender);
        
        // Set a "subject"
        $email->subject('Confirmer votre inscription');
        
        // Set the plain-text "Body"
        $email->html($message);
        
        // Send the message
        try {
            $mailer->send($email);

            $message = "Veuillez consulter votre boite mail pour confirmer votre inscription ";

        } catch (TransportExceptionInterface $e) {
            dd("Echec d'envoi du message");
        }
    }
}