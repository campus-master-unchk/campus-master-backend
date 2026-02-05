<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Grade;

class GradeRepository
{
    public function updateOrCreate(array $attributes, array $values): Grade
    {
        return Grade::updateOrCreate($attributes, $values);
    }

    public function getStudentGrades(int $studentId)
    {
        return Grade::where('student_id', $studentId)
            ->with(['submission.devoir'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}