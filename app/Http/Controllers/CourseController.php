<?php

namespace App\Http\Controllers;

use App\Core\Application\Services\CourseService;
use App\Core\Infrastructure\Repositories\CourseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService,
        protected CourseRepository $courseRepository
    ) {}

    /** --- LECTURE --- **/
    public function index()
    {
        return response()->json(['data' => $this->courseRepository->all()]);
    }

    public function show($id)
    {
        $course = $this->courseRepository->findById($id);
        return $course ? response()->json($course) : response()->json(['message' => 'Non trouvé'], 404);
    }

    public function myCourses()
    {
        return response()->json($this->courseRepository->getByTeacher(Auth::user()->teacher->id));
    }

    public function getPublishedByModule()
    {
        // $user = Auth::user();

        // $moduleId = request()->input('module_id');

        // return response()->json($this->courseRepository->getPublishedByModule($moduleId));
    }

    /** --- ACTIONS --- **/
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'image' => 'required|image|max:2048',
            'description' => 'nullable',
            'state' => 'nullable',
            // Validation ressources multiples
            'resources' => 'nullable|array',
            'resources.*.name' => 'required|string',
            'resources.*.type' => 'required|in:pdf,ppt,docx,video',
            'resources.*.video_link' => 'required_if:resources.*.type,video',
            'resource_files.*' => 'nullable|file|max:20480'
        ]);

        try {
            $course = $this->courseService->createFullCourse(
                $request->only(['name', 'module_id', 'description']),
                $request->file('image'),
                $request->input('resources', []),
                $request->file('resource_files', []),
                Auth::user()->teacher->id
            );
            return response()->json($course, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'image' => 'required|image|max:2048',
            'description' => 'nullable',
            'state' => 'nullable',
            // Validation ressources multiples
            'resources' => 'nullable|array',
            'resources.*.name' => 'required|string',
            'resources.*.type' => 'required|in:pdf,ppt,docx,video',
            'resources.*.video_link' => 'required_if:resources.*.type,video',
            'resource_files.*' => 'nullable|file|max:20480'
        ]);

        try {
            $course = $this->courseService->updateFullCourse(
                $id,
                $request->only(['name', 'description']),
                $request->file('image'),
                $request->input('new_resources', []),
                $request->file('new_resource_files', []),
                Auth::user()->teacher->id
            );
            return response()->json($course);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function changeState(Request $request, $id)
    {
        $data = $request->validate(['state' => 'required|in:published,draft']);

        try {
            $this->courseService->toggleStatus($id, $data['state'], Auth::user()->teacher->id);
            return response()->json(['message' => 'Statut mis à jour avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy($id)
    {
        try {
            $this->courseService->deleteCourse($id, Auth::user()->teacher->id);
            return response()->json(['message' => 'Cours et ressources supprimés']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
