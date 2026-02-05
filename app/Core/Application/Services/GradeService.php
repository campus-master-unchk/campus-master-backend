<?php

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\GradeRepository;
use App\Core\Infrastructure\Repositories\SubmissionRepository;
use App\Core\Application\Services\NotificationService;

use Exception;

class GradeService
{
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected SubmissionRepository $submissionRepository,
        protected NotificationService $notificationService
    ) {}

    public function assignGrade(array $data, int $teacherId)
    {
        $submission = $this->submissionRepository->findById($data['submission_id']);

        if (!$submission) {
            throw new Exception("Travail introuvable.");
        }

        // Vérification : Est-ce que ce prof est bien le propriétaire du cours ?
        if ($submission->devoir->teacher_id !== $teacherId) {
            throw new Exception("Vous n'êtes pas autorisé à noter ce travail.");
        }

        $grade = $this->gradeRepository->updateOrCreate(
            ['submission_id' => $submission->id, 'student_id' => $submission->student_id],
            ['grade' => $data['grade'], 'commentaire' => $data['commentaire'] ?? null]
        );

        $this->notificationService->notifyStudent($grade->student->user, [
            'title' => "Nouvelle note disponible",
            'message' => "Vous avez reçu un {$grade->grade}/20 pour le devoir : {$grade->submission->devoir->name}",
            'type' => 'grade',
            'url' => "/dashboard/grades"
        ]);
        return $grade;
    }
}
