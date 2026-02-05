<?php 

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\User;

class NotificationRepository
{
    /** Récupérer toutes les notifications (pour l'historique) */
    public function getAllForUser(User $user)
    {
        return $user->notifications()->paginate(25);
    }

    /** Récupérer uniquement les non-lues (pour le badge de la cloche) */
    public function getUnreadForUser(User $user)
    {
        return $user->unreadNotifications;
    }

    /** Marquer une notification précise comme lue */
    public function markAsRead(User $user, string $id)
    {
        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return $notification;
    }

    /** Tout marquer comme lu */
    public function markAllAsRead(User $user)
    {
        return $user->unreadNotifications->markAsRead();
    }
}