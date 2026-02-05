<?php

namespace App\Core\Application\Services;

use App\Core\Infrastructure\Repositories\CourseRepository;
use Illuminate\Support\Facades\{Storage, DB};
use Exception;

class CourseService
{
    public function __construct(protected CourseRepository $courseRepository) {}

    /**
     * Création complète : Cours + Multiples Ressources
     */
    public function createFullCourse(array $courseData, $thumbnail, array $resources, array $files, int $teacherId)
    {
        return DB::transaction(function () use ($courseData, $thumbnail, $resources, $files, $teacherId) {
            // 1. Sauvegarde de la miniature du cours
            if ($thumbnail) {
                $path = $thumbnail->store('courses/thumbnails', 'public');
                $courseData['course_url_img'] = Storage::url($path);
            }
            $courseData['teacher_id'] = $teacherId;
            $course = $this->courseRepository->create($courseData);

            // 2. Traitement des ressources en boucle
            $this->processResources($course->id, $resources, $files);

            return $course->load('resources');
        });
    }

    /**
     * Mise à jour du cours + Ajout de nouvelles ressources (Sync)
     */
    public function updateFullCourse(int $id, array $courseData, $thumbnail, array $newResources, array $newFiles, int $teacherId)
    {
        return DB::transaction(function () use ($id, $courseData, $thumbnail, $newResources, $newFiles, $teacherId) {
            $course = $this->courseRepository->findById($id);
            if (!$course || $course->teacher_id !== $teacherId) throw new Exception("Action non autorisée.");

            // Update miniature si nouvelle image
            if ($thumbnail) {
                if ($course->course_url_img) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $course->course_url_img));
                }
                $courseData['course_url_img'] = Storage::url($thumbnail->store('courses/thumbnails', 'public'));
            }
            $this->courseRepository->update($id, $courseData);

            // Ajout des nouvelles ressources fournies
            $this->processResources($id, $newResources, $newFiles);

            return $course->refresh()->load('resources');
        });
    }

    /**
     * Logique privée pour traiter le mix Vidéo (Lien) / Documents (Fichiers)
     */
    private function processResources(int $courseId, array $resources, array $files)
    {
        foreach ($resources as $index => $res) {
            $resData = [
                'name' => $res['name'],
                'type' => $res['type'],
                'description' => $res['description'] ?? null,
                'course_id' => $courseId
            ];

            if ($res['type'] === 'video') {
                $resData['url_resource'] = $res['video_link'];
            } else {
                // On récupère le fichier via l'index correspondant dans le tableau resource_files
                if (isset($files[$index])) {
                    $resData['url_resource'] = $files[$index]->store('courses/resources', 'private');
                } else { continue; } // On ignore si c'est un doc sans fichier
            }
            $this->courseRepository->addResource($resData);
        }
    }

    public function toggleStatus(int $id, string $status, int $teacherId)
    {
        if (!in_array($status, ['published', 'draft'])) {
            throw new Exception("Statut invalide.");
        }

        $entity = $this->courseRepository->findById($id);
        if (!$entity || $entity->teacher_id !== $teacherId) {
            throw new Exception("Action non autorisée.");
        }

        return $this->courseRepository->update($id, ['state' => $status]);
    }

    public function deleteCourse(int $id, int $teacherId)
    {
        $course = $this->courseRepository->findById($id);
        if (!$course || $course->teacher_id !== $teacherId) throw new Exception("Action interdite.");

        // Supprimer les fichiers physiques des ressources (sauf les liens vidéos)
        foreach ($course->resources as $res) {
            if ($res->type !== 'video') Storage::disk('private')->delete($res->url_resource);
        }
        // Supprimer la miniature
        Storage::disk('public')->delete(str_replace('/storage/', '', $course->course_url_img));

        return $this->courseRepository->delete($id);
    }
}