<?php 

namespace App\Http\Controllers;

use App\Core\Infrastructure\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $notifRepo
    ) {}

    /** GET : Liste des notifications (avec badge count via le JSON) */
    public function getAllNotif()
    {
        $user = Auth::user();
        return response()->json([
            'unread_count' => $this->notifRepo->getAllForUser($user)->count(),
            'notifications' => $this->notifRepo->getAllForUser($user)
        ]);
    }

    public function getUnreadNotif ()
    {
        $user = Auth::user();
        return response()->json([
            'unread_count' => $this->notifRepo->getUnreadForUser($user)->count(),
            'notifications' => $this->notifRepo->getUnreadForUser($user)
        ]);
    }

    /** PATCH : Marquer comme lu */
    public function markRead($id)
    {
        $this->notifRepo->markAsRead(Auth::user(), $id);
        return response()->json(['status' => 'success']);
    }

    /** PATCH : Tout marquer comme lu */
    public function markAllRead()
    {
        $this->notifRepo->markAllAsRead(Auth::user());
        return response()->json(['status' => 'success']);
    }
}