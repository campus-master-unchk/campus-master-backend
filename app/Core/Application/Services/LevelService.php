<?php 

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\LevelRepository;

class LevelService
{
    public function __construct(protected LevelRepository $levelRepository) {}

    public function listLevels()
    {
        return $this->levelRepository->all();
    }

    public function addLevel(string $name)
    {
        return $this->levelRepository->create(['name' => $name]);
    }

    public function updateLevel(int $id, string $name)
    {
        return $this->levelRepository->update($id, ['name' => $name]);
    }

    public function removeLevel(int $id)
    {
        return $this->levelRepository->delete($id);
    }
}