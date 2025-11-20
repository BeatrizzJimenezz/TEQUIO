<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponentSchedule extends Model
{
    use HasFactory;

    protected $table = 'component_schedules';

    protected $fillable = [
        'component_id',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relation with component
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    /**
     * Check if this schedule overlaps with another schedule
     */
    public function overlapsWith(ComponentSchedule $other): bool
    {
        // Schedules must be on the same date to overlap
        if (!$this->date->equalTo($other->date)) {
            return false;
        }

        // Convert times to timestamps for comparison
        $thisStart = strtotime($this->start_time);
        $thisEnd = strtotime($this->end_time);
        $otherStart = strtotime($other->start_time);
        $otherEnd = strtotime($other->end_time);

        // Two time ranges overlap if: start1 < end2 AND start2 < end1
        return $thisStart < $otherEnd && $otherStart < $thisEnd;
    }
}