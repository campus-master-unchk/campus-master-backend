<?php 

namespace App\Notifications;

use App\Core\Domain\Entities\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(protected Announcement $announcement) {}

    /**
     * On définit les canaux de diffusion (database, mail, broadcast)
     */
    public function via($notifiable): array
    {
        return ['database']; 
    }

    /**
     * Structure des données stockées en JSON dans la base
     */
    public function toArray($notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'type' => $this->announcement->type,
            'priority' => $this->announcement->priority,
            'course_name' => $this->announcement->course->name,
            'message' => "Une nouvelle annonce a été publiée dans le cours : " . $this->announcement->course->name,
            'action_url' => "/courses/" . $this->announcement->course_id . "/announcements"
        ];
    }
}