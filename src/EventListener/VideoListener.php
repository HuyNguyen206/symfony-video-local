<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\UserInteractiveVideo;
use App\Entity\Video;
use App\Message\SmsNotiMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mime\Address;

//#[AsEntityListener(event: Events::prePersist,  entity: Video::class, method: 'sendEmail')]
class VideoListener
{
    public function __construct(protected LoggerInterface $logger, protected MailerInterface $mailer, protected MessageBusInterface $bus)
    {
    }

    public function sendEmail(Video $video, PrePersistEventArgs $args): void
    {
        $users = $args->getObjectManager()->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $email = (new TemplatedEmail())
                ->from('fabien@example.com')
                ->to(new Address($user->getEmail()))
                ->subject('Thanks for upload video!')

                // path of the Twig template to render
                ->htmlTemplate('emails/signup.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'username' => 'foo',
                ]);

            $this->mailer->send($email);
        }
    }

    public function preUpdate(Video $video, PreUpdateEventArgs $args)
    {
//        $this->bus->dispatch(new SmsNotiMessage('test'), [new DelayStamp(10000)]);
        $args->setNewValue('duration', 200);
    }

}
