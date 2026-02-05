<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\AnnouncementService;
use App\Core\Domain\Entities\Course;
use App\Core\Infrastructure\Repositories\AnnouncementRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementService $announcementService,
        protected AnnouncementRepository $announcementRepo
    ) {}

    public function createAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,exam,homework,schedule',
            'priority' => 'required|in:normal,important,urgent',
            'state' => 'required|in:published,draft',
            'course_id' => 'required|exists:courses,id'
        ]);

        $announcement = $this->announcementService->processAnnouncement($data, Auth::user()->teacher->id);
        return response()->json($announcement, 201);
    }

    public function indexForGeneral()
    {
        return response()->json($this->announcementRepo->getAnnouncementsGeneral());
    }

    public function indexForStudent()
    {
        $student = Auth::user()->student;
        // Logique pour récupérer les IDs des cours liés à la spécialité de l'étudiant
        $courseIds = Course::whereHas('module.speciality', function($q) use ($student) {
            $q->where('id', $student->speciality_id);
        })->pluck('id')->toArray();

        return response()->json($this->announcementRepo->getPublishedForStudent($courseIds));
    }

    public function publish($id)
    {
        try {
            $announcement = $this->announcementService->publishExistingAnnouncement($id, Auth::user()->teacher->id);
            return response()->json(['message' => 'Annonce publiée', 'data' => $announcement]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function indexForTeacher()
    {
        return response()->json($this->announcementRepo->getByTeacher(Auth::user()->teacher->id));
    }

    public function showAnnouncement($id)
    {
        return response()->json($this->announcementRepo->findById($id));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,exam,homework,schedule',
            'priority' => 'required|in:normal,important,urgent',
            'state' => 'required|in:published,draft',
            'course_id' => 'required|exists:courses,id'
        ]);

        $announcement = $this->announcementRepo->update($id, $data);
        return response()->json($announcement);
    }

    public function deleteAnnouncement($id)
    {
        $this->announcementRepo->delete($id);
        return response()->json(['message' => 'Annonce supprimée']);
    }
}
