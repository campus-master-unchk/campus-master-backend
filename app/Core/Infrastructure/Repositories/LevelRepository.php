<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Level;
use Illuminate\Database\Eloquent\Collection;

class LevelRepository
{
    public function all(): Collection
    {
        return Level::all();
    }

    public function create(array $data): Level
    {
        return Level::create($data);
    }

    public function update(int $id, array $data): Level
    {
        $level = $this->findById($id);
        $level->update($data);
        return $level;
    }

    private function findById(int $id): ?Level
    {
        return Level::find($id);
    }

    public function delete(int $id): bool
    {
        return Level::destroy($id);
    }
}
