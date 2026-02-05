<?php

namespace App\Http\Controllers;

use App\Core\Application\Services\SubmissionService;
use App\Core\Infrastructure\Repositories\SubmissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService,
        protected SubmissionRepository $submissionRepository
    ) {}

    /**
     * POST : Permet à l'étudiant de déposer un fichier
     */
    public function submitWork(Request $request)
    {
        $data = $request->validate([
            'devoir_id' => 'required|exists:devoirs,id',
            'commentaire' => 'nullable|string|max:500',
            'file' => 'required|file|mimes:pdf,zip,rar,doc,docx|max:20480', // 20MB max
        ]);

        try {
            $submission = $this->submissionService->submitWork(
                $data, 
                $request->file('file'), 
                Auth::user()->student->id
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Devoir déposé avec succès',
                'data' => $submission
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 403);
        }
    }

    /**
     * GET : L'étudiant voit ses propres tentatives pour un devoir
     */
    public function myHistory($devoirId)
    {
        $studentId = Auth::user()->student->id;
        $history = $this->submissionRepository->getHistoryForStudent($studentId, $devoirId);
        
        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }

    /**
     * GET : L'enseignant voit tous les derniers rendus d'un devoir
     */
    public function teacherView($devoirId)
    {
        // On pourrait ajouter un middleware ou une vérification ici
        // pour s'assurer que l'utilisateur est bien le prof du cours
        $submissions = $this->submissionRepository->getLatestSubmissionsPerStudent($devoirId);
        
        return response()->json([
            'status' => 'success',
            'data' => $submissions
        ]);
    }
}