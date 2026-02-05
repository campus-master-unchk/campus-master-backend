<?php

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RessourceCourse extends Model
{
    protected $table = 'ressource_courses';
    protected $fillable = ['name', 'description', 'course_id', 'type', 'url_resource'];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
}