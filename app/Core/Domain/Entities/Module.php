<?php

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name',
        'description',
        'img_module_url',
        'department_id',
        'specialty_id',
        'level_id',
        'semestre'
    ];

    // Relations 

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
