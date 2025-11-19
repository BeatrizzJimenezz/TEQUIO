<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicTraining extends Model
{
    use HasFactory;

    protected $table = 'academic_trainings';

    protected $fillable = [
        'professional_profile_id',
        'institution',
        'degree',
        'start_date',
        'end_date',
        'description',
    ];

    /**
     * Relationship to ProfessionalProfile
     */
    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }
}
