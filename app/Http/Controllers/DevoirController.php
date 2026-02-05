<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\DevoirService;
use App\Core\Infrastructure\Repositories\DevoirRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevoirController extends Controller
{
    public function __construct(
        protected DevoirService $devoirService,
        protected DevoirRepository $devoirRepository
    ) {}

    /** --- LISTES --- **/

    public function indexByCourse($courseId) {
        // Un étudiant ne verra que les devoirs 'published'
        return response()->json($this->devoirRepository->getPublishedByCourse($courseId));
    }

    public function myDevoirs() {
        return response()->json($this->devoirRepository->getByTeacher(Auth::user()->teacher->id));
    }

    /** --- ACTIONS --- **/

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'date_limit' => 'required|date|after:today',
            'state' => 'required|in:published,draft',
            'file' => 'nullable|file|mimes:pdf,docx,zip|max:5120',
        ]);

        try {
            $devoir = $this->devoirService->createDevoir($data, $request->file('file'), Auth::user()->teacher->id);
            return response()->json(['status' => 'success', 'data' => $devoir], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'date_limit' => 'sometimes|date|after:today',
            'state' => 'sometimes|in:published,draft',
            'file' => 'nullable|file|max:5120'
        ]);

        try {
            $this->devoirService->updateDevoir($id, $data, $request->file('file'), Auth::user()->teacher->id);
            return response()->json(['message' => 'Devoir mis à jour']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function changeState($id, Request $request) {
        $data = $request->validate([
            'state' => 'required|in:published,draft'
        ]);

        try {
            $this->devoirService->updateDevoir($id, $data, null, Auth::user()->teacher->id);
            return response()->json(['message' => 'État du devoir modifié']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy($id) {
        try {
            $this->devoirService->deleteDevoir($id, Auth::user()->teacher->id);
            return response()->json(['message' => 'Devoir supprimé']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

}