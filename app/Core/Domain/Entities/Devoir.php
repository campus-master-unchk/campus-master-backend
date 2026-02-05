<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Domain\Entities\Course;
use App\Core\Domain\Entities\Submission;
use App\Core\Domain\Entities\Teacher;

class Devoir extends Model
{
    protected $fillable = [
        'name',
        'teacher_id',
        'description',
        'course_id',
        'url_devoir',
        'state',
        'date_limit',
    ];

    protected $casts = [
        'date_limit' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
