<?php 

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\DevoirRepository;
use Illuminate\Support\Facades\Storage;
use Exception;

class DevoirService
{
    public function __construct(protected DevoirRepository $devoirRepository) {}

    public function createDevoir(array $data, $file, int $teacherId)
    {
        if ($file) {
            // Stockage de l'énoncé dans un dossier sécurisé (non public)
            $path = $file->store('devoirs/instructions', 'private');
            $data['url_devoir'] = $path;
        }

        $data['teacher_id'] = $teacherId;
        return $this->devoirRepository->create($data);
    }

    public function updateDevoir(int $id, array $data, $file, int $teacherId)
    {
        $devoir = $this->devoirRepository->findById($id);
        if (!$devoir || $devoir->teacher_id !== $teacherId) {
            throw new Exception("Vous n'avez pas le droit de modifier ce devoir.");
        }

        if ($file) {
            // Supprimer l'ancien énoncé s'il existe
            if ($devoir->url_devoir) {
                Storage::disk('private')->delete($devoir->url_devoir);
            }
            $data['url_devoir'] = $file->store('devoirs/instructions', 'private');
        }

        return $this->devoirRepository->update($id, $data);
    }

    public function deleteDevoir(int $id, int $teacherId)
    {
        $devoir = $this->devoirRepository->findById($id);
        if (!$devoir || $devoir->teacher_id !== $teacherId) {
            throw new Exception("Action interdite.");
        }

        if ($devoir->url_devoir) {
            Storage::disk('private')->delete($devoir->url_devoir);
        }

        return $this->devoirRepository->delete($id);
    }
}