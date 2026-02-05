<?php 

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Specialty;
use Illuminate\Database\Eloquent\Collection;

class SpecialtyRepository
{
    // Récupérer toutes les spécialités
    public function all(): Collection
    {
        return Specialty::with('department')->get(); 
    }

    // Récupérer les spécialités d'un département
    public function getByDepartment(int $departmentId): Collection
    {
        return Specialty::where('department_id', $departmentId)->get();
    }

    // Créer une nouvelle spécialité
    public function create(array $data): Specialty
    {
        return Specialty::create($data);
    }
    
    // Mettre à jour une spécialité
    public function update(int $id, array $data): Specialty
    {
        $specialty = Specialty::findOrFail($id);
        $specialty->update($data);
        return $specialty;
    }
    
    // Supprimer une spécialité
    public function delete(int $id): bool
    {
        $specialty = Specialty::findOrFail($id);
        return $specialty->delete();
    }
}
