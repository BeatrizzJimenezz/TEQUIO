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

    // Relación con el componente del evento
    public function component(): BelongsTo
    {
        return $this->belongsTo(EventComponent::class, 'component_id');
    }

    // Método para verificar si dos horarios se superponen
    public function overlapsWith(ComponentSchedule $other): bool
    {
        // Los horarios deben ser en la misma fecha para superponerse
        if (!$this->date->equalTo($other->date)) {
            return false;
        }

        // Convertir los tiempos a marcas de tiempo para la comparación
        $thisStart = strtotime($this->start_time);
        $thisEnd = strtotime($this->end_time);
        $otherStart = strtotime($other->start_time);
        $otherEnd = strtotime($other->end_time);

        // Dos rangos de tiempo se superponen si: start1 < end2 Y start2 < end1
        return $thisStart < $otherEnd && $otherStart < $thisEnd;
    }
}