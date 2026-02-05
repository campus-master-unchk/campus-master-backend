<?php

namespace App\Core\Domain\Entities;
use Illuminate\Database\Eloquent\Model;
use App\Core\Domain\Entities\Teacher;
use App\Core\Domain\Entities\Specialty;
use App\Core\Domain\Entities\Module;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Relations
     */

    // Un département possède plusieurs spécialités
    public function specialties()
    {
        return $this->hasMany(Specialty::class); 
    }

    // Un département possède plusieurs enseignants
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    // Un département possède plusieurs étudiants
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Un département propose plusieurs modules
    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
