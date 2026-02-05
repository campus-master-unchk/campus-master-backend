<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Module;

class ModuleRepository
{
    public function getAllModules()
    {
        return Module::with('department', 'specialty', 'level')->get();
    }

    public function create(array $data): Module
    {
        return Module::create($data);
    }

    public function getFiltered(int $deptId, int $specId, int $levelId)
    {
        return Module::where('department_id', $deptId)
            ->where('specialty_id', $specId)
            ->where('level_id', $levelId)
            ->with(['level', 'specialty'])
            ->get();
    }

    public function findById(int $id): ?Module
    {
        return Module::with(['department', 'specialty', 'level'])->find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Module::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Module::where('id', $id)->delete();
    }
}
