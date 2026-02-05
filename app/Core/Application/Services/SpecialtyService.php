<?php 

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\SpecialtyRepository;

class SpecialtyService
{
    public function __construct(protected SpecialtyRepository $specialtyRepository) {}

    public function all()
    {
        return $this->specialtyRepository->all();
    }

    public function createSpecialty(array $data)
    {
        // On pourrait ajouter ici une règle : "Maximum 10 spécialités par département"
        return $this->specialtyRepository->create($data);
    }

    public function getSpecialtiesByDept(int $deptId)
    {
        return $this->specialtyRepository->getByDepartment($deptId);
    }

    public function updateSpecialty(int $id, array $data)
    {
        return $this->specialtyRepository->update($id, $data);
    }

    public function deleteSpecialty(int $id)
    {
        return $this->specialtyRepository->delete($id);
    }
}
