<?php 

namespace App\Http\Controllers;

use App\Core\Application\Services\GradeService;
use App\Core\Infrastructure\Repositories\GradeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function __construct(
        protected GradeService $gradeService,
        protected GradeRepository $gradeRepository
    ) {}

    /**
     * POST : L'enseignant attribue une note
     */
    public function createGrade(Request $request)
    {
        $data = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'grade' => 'required|numeric|min:0|max:20',
            'commentaire' => 'nullable|string'
        ]);

        try {
            $grade = $this->gradeService->assignGrade($data, Auth::user()->teacher->id);
            return response()->json(['status' => 'success', 'data' => $grade]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * GET : L'étudiant consulte son bulletin de notes
     */
    public function myGrades()
    {
        $grades = $this->gradeRepository->getStudentGrades(Auth::user()->student->id);
        return response()->json(['status' => 'success', 'data' => $grades]);
    }
}