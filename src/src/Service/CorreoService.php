<?php
namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CorreoService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function enviarCorreoCreacionProfesor(string $emailDestino, string $nombreProfesor, string $password)
    {
        $email = (new Email())
            ->from('pabllito.perez1@gmail.com')
            ->to($emailDestino)
            ->subject('Credenciales')
            ->html('<p>Hola ' . $nombreProfesor . ', tu cuenta de profesor ha sido creada.
                    Inicia sesión con el email que te registraste y la siguiente contraseña: '. $password .    
                '</p>');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // Manejar cualquier error de envío acá
        }
    }
}