<?php 

namespace App\Core\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'department_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}