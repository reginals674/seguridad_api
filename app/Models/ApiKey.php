<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['nombre', 'key_hash'])]
#[Hidden(['key_hash'])]
class ApiKey extends Model
{
    protected function casts(): array
    {
        return [
            'ultimo_uso_at' => 'datetime',
            'revocada_at' => 'datetime',
        ];
    }

    /**
     * Crea una clave nueva y devuelve el texto plano (solo se puede ver aquí).
     *
     * @return array{0: self, 1: string}
     */
    public static function generar(string $nombre): array
    {
        $plana = 'sk_'.Str::random(40);

        $apiKey = static::create([
            'nombre' => $nombre,
            'key_hash' => static::hashear($plana),
        ]);

        return [$apiKey, $plana];
    }

    public static function buscarActiva(string $plana): ?self
    {
        return static::where('key_hash', static::hashear($plana))
            ->whereNull('revocada_at')
            ->first();
    }

    public static function hashear(string $plana): string
    {
        return hash('sha256', $plana);
    }
}
