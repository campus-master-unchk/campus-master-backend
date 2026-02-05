<?php

namespace App\Core\Domain\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'name',
        'description',
        'department_id',
    ];

    /**
     * Relations
     */

    // Une spécialité appartient à un département
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Une spécialité possède plusieurs étudiants
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Une spécialité possède plusieurs modules
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    // Une spécialité peut avoir plusieurs enseignants
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_specialties');
    }
}
