<?php 

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\DepartmentRepository;

class DepartmentService
{
    public function __construct(protected DepartmentRepository $departmentRepository) {}

    // Récupérer tous les départements
    public function getAllDepartments()
    {
        return $this->departmentRepository->all();
    }

    // Créer un nouveau département
    public function createDepartment(array $data)
    {
        return $this->departmentRepository->create($data);
    }
    
    // Mettre à jour un département
    public function updateDepartment(int $id, array $data)
    {
        return $this->departmentRepository->update($id, $data);
    }

    // Supprimer un département
    public function deleteDepartment(int $id)
    {
        return $this->departmentRepository->delete($id);
    }
}