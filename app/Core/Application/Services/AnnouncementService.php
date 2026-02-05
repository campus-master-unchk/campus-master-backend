<?php 

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\AnnouncementRepository;
use App\Core\Domain\Entities\Course;
use App\Core\Application\Services\NotificationService;

class AnnouncementService
{
    public function __construct(
        protected AnnouncementRepository $announcementRepo,
        protected NotificationService $notificationService
        ) {}

    public function processAnnouncement(array $data, int $teacherId)
    {
        $data['teacher_id'] = $teacherId;
        $announcement = $this->announcementRepo->create($data);

        if ($announcement->state === 'published') {
            $this->notifyStudents($announcement);
        }

        return $announcement;
    }

    public function publishExistingAnnouncement(int $id, int $teacherId)
    {
        $announcement = $this->announcementRepo->findById($id);
        if (!$announcement || $announcement->teacher_id !== $teacherId) throw new \Exception("Interdit");

        $this->announcementRepo->update($id, ['state' => 'published']);
        $this->notifyStudents($announcement);
        
        return $announcement;
    }

    private function notifyStudents($announcement)
    {
        $course = Course::with('module.speciality.students.user')->find($announcement->course_id);
        if ($course) {
            $users = $course->module->speciality->students->pluck('user');
// USAGE DU SERVICE DE NOTIFICATION
        $this->notificationService->notifyGroup($users, [
            'title' => "Nouvelle annonce : " . $announcement->title,
            'message' => "Votre enseignant a posté une nouvelle information dans " . $course->name,
            'type' => 'announcement',
            'url' => "/courses/{$course->id}/announcements"
        ]);        }
    }
}