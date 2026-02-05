<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = ['submission_id', 'student_id', 'grade', 'commentaire'];

    public function submission(): BelongsTo { return $this->belongsTo(Submission::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
}