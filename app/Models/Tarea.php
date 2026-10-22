<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// "user_id" NO está en Fillable: el dueño lo decide el servidor (relación del usuario), nunca el cliente.
#[Fillable(['titulo', 'descripcion', 'completada'])]
class Tarea extends Model
{
    protected function casts(): array
    {
        return [
            'completada' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
