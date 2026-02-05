<?php 

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Entities\Course;
use App\Core\Domain\Entities\RessourceCourse;

class CourseRepository
{
    // Méthodes pour les Cours
    public function all() { return Course::with('teacher.user', 'module')->get(); }
    
    public function create(array $data) { return Course::create($data); }
    
    public function update(int $id, array $data) { return Course::where('id', $id)->update($data); }
    
    public function delete(int $id) { return Course::destroy($id); }

    public function findById(int $id) { return Course::with('resources')->find($id); }

    public function getByTeacher(int $teacherId) { 
        return Course::where('teacher_id', $teacherId)->withCount('resources')->get(); 
    }

    public function getByModule(int $moduleId) {
        return Course::where('module_id', $moduleId)->with('teacher.user', 'resources')->get();
    }

    // Pour l'étudiant : voir uniquement les cours publiés
    public function getPublishedByModule(int $moduleId) {
        return Course::where('module_id', $moduleId)
            ->where('state', 'published') // Filtre de statut
            ->with(['teacher.user', 'resources'])
            ->get();
    }

    // Méthodes pour les Ressources
    public function addResource(array $data) { return RessourceCourse::create($data); }

    public function findResourceById(int $id) { return RessourceCourse::with('course')->find($id); }

    public function updateResource(int $id, array $data) { return RessourceCourse::where('id', $id)->update($data); }

    public function deleteResource(int $id) { return RessourceCourse::destroy($id); }
}