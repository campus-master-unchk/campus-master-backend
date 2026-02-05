<?php

namespace App\Core\Domain\Entities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse
     */
    protected $fillable = ['user_id', 'department_id', 'specialty_id', 'level_id'];

    /**
     * Relations
     */

    // Un étudiant appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un étudiant appartient à un département
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Un étudiant appartient à une spécialité
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    // Un étudiant peut avoir plusieurs soumissions
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
    
    // Un étudiant appartient à un niveau
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
