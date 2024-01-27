<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserChangedNotifier
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private Security                    $security
    )
    {
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getPlainPassword()) {
            $user
                ->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()))
                ->eraseCredentials();
        }
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $user = $event->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getPlainPassword()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        }

        $user
            ->setIsEnable(true)
            ->eraseCredentials();

        if (!$this->security->getToken()?->getUser() instanceof User) {
            return;
        }
    }
}
