<?php

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\SubmissionRepository;
use App\Core\Infrastructure\Repositories\DevoirRepository;
use App\Core\Application\Services\NotificationService;
use Carbon\Carbon;
use Exception;

class SubmissionService
{
    public function __construct(
        protected SubmissionRepository $submissionRepository,
        protected DevoirRepository $devoirRepository,
        protected NotificationService $notificationService
    ) {}

    /**
     * Gère le dépôt d'un devoir par un étudiant
     */
    public function submitWork(array $data, $file, int $studentId)
    {
        $devoir = $this->devoirRepository->findById($data['devoir_id']);

        if (!$devoir) {
            throw new Exception("Le devoir spécifié n'existe pas.");
        }

        // Vérification de la date limite
        if (Carbon::now()->isAfter($devoir->date_limit)) {
            throw new Exception("Dépôt impossible : la date limite (" . $devoir->date_limit->format('d/m/Y H:i') . ") est dépassée.");
        }

        if ($file) {
            // Organisation du stockage : submissions/devoir_ID/student_ID/timestamp_nom.ext
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs(
                "submissions/devoir_{$devoir->id}/student_{$studentId}",
                $filename,
                'private'
            );
            $data['url_submission'] = $path;
        }

        $data['student_id'] = $studentId;
        $data['date_submission'] = Carbon::now();

        $submission = $this->submissionRepository->create($data);

        // USAGE DU SERVICE DE NOTIFICATION
        $this->notificationService->notifyStudent($submission->devoir->teacher->user, [
            'title' => "Nouveau dépôt reçu",
            'message' => "Un étudiant a déposé son travail pour le devoir : " . $submission->devoir->name,
            'type' => 'submission',
            'url' => "/teacher/assignments/{$submission->devoir_id}"
        ]);

        return $submission;
    }
}
