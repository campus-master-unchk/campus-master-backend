<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    public function all(): Collection
    {
        return Department::all();
    }

    public function findById(int $id): ?Department
    {
        return Department::find($id);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Department::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Department::destroy($id);
    }
}