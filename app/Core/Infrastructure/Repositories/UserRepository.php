<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Student;
use App\Core\Domain\Entities\Teacher;
use App\Core\Domain\Entities\User;

class UserRepository
{
    // get all student and teacher 
    public function getAll()
    {
        return User::where('user_type', 'student')->orWhere('user_type', 'teacher')->with('student', 'teacher')->get();
    }

    // Creation de l'utilisateur 
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function createStudentProfile(array $data): Student
    {
        return Student::create($data);
    }

    public function createTeacherProfile(array $data): Teacher
    {
        return Teacher::create($data);
    }


    // Mise a jour de l'utilisateur 
    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function updateStudentProfile(int $userId, array $data): bool
    {
        return Student::where('user_id', $userId)->update($data);
    }

    public function updateTeacherProfile(int $userId, array $data): bool
    {
        return Teacher::where('user_id', $userId)->update($data);
    }
}
