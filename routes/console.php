<?php

use App\Models\ApiKey;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('apikey:crear {nombre} {--env= : Variable del .env donde guardarla sin mostrarla en pantalla}', function (string $nombre) {
    [$apiKey, $plana] = ApiKey::generar($nombre);

    $this->info("API key #{$apiKey->id} creada para \"{$nombre}\".");

    if ($variable = $this->option('env')) {
        // El .env está en .gitignore: la clave queda fuera del repositorio y de la terminal.
        $ruta = base_path('.env');
        $linea = "{$variable}={$plana}";
        $contenido = file_get_contents($ruta);
        $patron = '/^'.preg_quote($variable, '/').'=.*$/m';

        $contenido = preg_match($patron, $contenido)
            ? preg_replace($patron, $linea, $contenido)
            : rtrim($contenido).PHP_EOL.PHP_EOL.$linea.PHP_EOL;

        file_put_contents($ruta, $contenido);
        $this->info("Guardada en .env como {$variable} (no se muestra aquí).");

        return;
    }

    $this->line($plana);
    $this->warn('Cópiala ahora: no se guarda en texto plano y no se puede volver a mostrar.');
})->purpose('Genera una API key nueva');

Artisan::command('apikey:listar', function () {
    $this->table(
        ['ID', 'Nombre', 'Último uso', 'Revocada'],
        ApiKey::all()->map(fn (ApiKey $k) => [$k->id, $k->nombre, $k->ultimo_uso_at ?? '-', $k->revocada_at ?? 'no']),
    );
})->purpose('Lista las API keys');

Artisan::command('apikey:revocar {id}', function (int $id) {
    $apiKey = ApiKey::findOrFail($id);
    $apiKey->forceFill(['revocada_at' => now()])->save();

    $this->info("API key #{$id} ({$apiKey->nombre}) revocada.");
})->purpose('Revoca una API key');
