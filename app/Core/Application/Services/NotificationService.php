<?php

namespace App\Core\Application\Services;

use App\Core\Domain\Entities\User;
use App\Core\Infrastructure\Repositories\NotificationRepository;
use App\Notifications\AcademicNotification;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function __construct(protected NotificationRepository $notifRepo) {}

    /** CIBLAGE : Envoyer à un étudiant précis (ex: pour une Note) */
    public function notifyStudent(User $user, array $data)
    {
        $user->notify(new NewAnnouncementNotification(
            $data['title'],
            $data['message'],
            $data['type'],
            $data['url']
        ));
    }

    /** CIBLAGE GROUPE : Envoyer à une liste d'utilisateurs (ex: pour un cours) */
    public function notifyGroup($users, array $data)
    {
        Notification::send($users, new NewAnnouncementNotification(
            $data['title'],
            $data['message'],
            $data['type'],
            $data['url']
        ));
    }

    /** CIBLAGE GLOBAL : Tout le monde (ex: Administration) */
    public function notifyAll(array $data)
    {
        $users = User::all();
        Notification::send($users, new NewAnnouncementNotification(
            $data['title'],
            $data['message'],
            'admin',
            $data['url']
        ));
    }
}