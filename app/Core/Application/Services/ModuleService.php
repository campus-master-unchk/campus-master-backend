<?php

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\ModuleRepository;
use Illuminate\Support\Facades\Storage;

class ModuleService
{
    public function __construct(protected ModuleRepository $moduleRepository) {}

    public function getAllModules()
    {
        return $this->moduleRepository->getAllModules();
    }
    public function createModule(array $data, $image = null)
    {
        if ($image) {
            // Stockage de l'image dans le dossier public/modules
            $path = $image->store('modules', 'public');
            $data['img_module_url'] = Storage::url($path);
        }

        return $this->moduleRepository->create($data);
    }

    public function getModulesForStudent($student)
    {
        return $this->moduleRepository->getFiltered(
            $student->department_id,
            $student->specialty_id,
            $student->level_id
        );
    }

    public function getModuleById($id)
    {
        return $this->moduleRepository->findById($id);
    }
    
    public function updateModule($id, array $data, $image = null)
    {
        $module = $this->moduleRepository->findById($id);
        
        unset($data['image']);
        if ($image) {
            // Stockage de l'image dans le dossier public/modules
            $path = $image->store('modules', 'public');
            $data['img_module_url'] = Storage::url($path);
        }
        
        $this->moduleRepository->update($id, $data);
        return $module;
    }
    
    public function deleteModule($id)
    {
        $module = $this->moduleRepository->findById($id);
        $this->moduleRepository->delete($id);
        return $module;
    }
}
