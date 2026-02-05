<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Domain\Entities\Module;
use App\Core\Domain\Entities\Teacher;
use App\Core\Domain\Entities\Devoir;
use App\Core\Domain\Entities\RessourceCourse;

class Course extends Model
{
    protected $fillable = [
        'name',
        'course_url_img',
        'description',
        'module_id',
        'teacher_id',
        'state'
    ];

    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
    public function resources(): HasMany { return $this->hasMany(RessourceCourse::class, 'course_id'); }
    public function devoirs(): HasMany { return $this->hasMany(Devoir::class); }
}
