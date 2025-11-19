<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_profile_id',
        'institution',
        'degree',
        'start_date',
        'end_date',
        'description',
    ];

    public function profile()
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }
}
