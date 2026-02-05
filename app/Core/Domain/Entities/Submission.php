<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Core\Domain\Entities\Student;
use App\Core\Domain\Entities\Devoir;

class Submission extends Model
{
    protected $fillable = [
            'devoir_id',
            'student_id',
            'url_submission',
            'commentaire',
            'date_submission'
        ];

    public function devoir() {
        return $this->belongsTo(Devoir::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}