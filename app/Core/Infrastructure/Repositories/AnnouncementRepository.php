<?php

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Announcement;

class AnnouncementRepository
{
    public function create(array $data) { return Announcement::create($data); }
    
    public function update(int $id, array $data) { return Announcement::where('id', $id)->update($data); }

    public function findById(int $id) { return Announcement::find($id); }
    
    public function findByCourseId(int $courseId) { return Announcement::where('course_id', $courseId)->get(); }

    public function getAnnouncementsGeneral() { return Announcement::where('type', 'general')->get(); }

    public function delete(int $id) { return Announcement::destroy($id); }

    /** Pour l'étudiant : Uniquement les annonces publiées des cours suivis */
    public function getPublishedForStudent(array $courseIds)
    {
        return Announcement::whereIn('course_id', $courseIds)
            ->where('state', 'published')
            ->with(['teacher.user', 'course'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /** Pour le prof : Voir tout son historique (draft et published) */
    public function getByTeacher(int $teacherId)
    {
        return Announcement::where('teacher_id', $teacherId)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}