<?php 

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Submission;
use Illuminate\Database\Eloquent\Collection;

class SubmissionRepository
{
    /**
     * Créer un nouveau dépôt (historisation automatique)
     */
    public function create(array $data): Submission
    {
        return Submission::create($data);
    }

    /**
     * Récupérer une soumission spécifique par son ID
     */
    public function findById(int $id): ?Submission
    {
        return Submission::with(['student.user', 'devoir'])->find($id);
    }

    /**
     * POUR L'ÉTUDIANT : Récupérer toutes ses tentatives pour un devoir spécifique
     * Classé du plus récent au plus ancien
     */
    public function getHistoryForStudent(int $studentId, int $devoirId): Collection
    {
        return Submission::where('student_id', $studentId)
            ->where('devoir_id', $devoirId)
            ->orderBy('date_submission', 'desc')
            ->get();
    }

    /**
     * POUR L'ENSEIGNANT : Récupérer tous les dépôts de tous les étudiants pour un devoir
     * Utile pour avoir une vue d'ensemble avant la notation
     */
    public function getAllSubmissionsForDevoir(int $devoirId): Collection
    {
        return Submission::where('devoir_id', $devoirId)
            ->with('student.user')
            ->orderBy('student_id')
            ->orderBy('date_submission', 'desc')
            ->get();
    }

    /**
     * POUR L'ENSEIGNANT : Récupérer uniquement le DERNIER dépôt de chaque étudiant
     * C'est généralement celui-ci qui est noté
     */
    public function getLatestSubmissionsPerStudent(int $devoirId): Collection
    {
        return Submission::where('devoir_id', $devoirId)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('submissions')
                    ->groupBy('student_id');
            })
            ->with('student.user')
            ->get();
    }
}