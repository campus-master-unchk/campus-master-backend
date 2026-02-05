<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'type', 'priority', 'state', 'course_id', 'teacher_id'];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
}