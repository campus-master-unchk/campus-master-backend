<?php

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = ['name'];

    /**
     * Un niveau est lié à plusieurs étudiants
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Un niveau est lié à plusieurs modules (ex: Master 2 a le module 'Big Data')
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
