<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Devoir;

class DevoirRepository
{
    public function create(array $data) { return Devoir::create($data); }

    public function update(int $id, array $data) { return Devoir::where('id', $id)->update($data); }

    public function delete(int $id) { return Devoir::destroy($id); }

    public function findById(int $id) { return Devoir::with(['course', 'teacher.user'])->find($id); }

    // Pour l'enseignant : voit tout (draft + published)
    public function getByTeacher(int $teacherId) {
        return Devoir::where('teacher_id', $teacherId)->with('course')->get();
    }

    // Pour l'étudiant : ne voit que ce qui est publié
    public function getPublishedByCourse(int $courseId) {
        return Devoir::where('course_id', $courseId)
            ->where('state', 'published')
            ->orderBy('date_limit', 'asc')
            ->get();
    }
}