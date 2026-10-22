<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ana = User::create([
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'password',
        ]);

        $luis = User::create([
            'name' => 'Luis',
            'email' => 'luis@example.com',
            'password' => 'password',
        ]);

        $ana->tareas()->createMany([
            ['titulo' => 'Estudiar para el parcial', 'descripcion' => 'Temas 1 a 4'],
            ['titulo' => 'Pagar la luz', 'descripcion' => 'Antes del viernes'],
        ]);

        $luis->tareas()->createMany([
            ['titulo' => 'Entregar proyecto', 'descripcion' => 'Subir a la plataforma'],
            ['titulo' => 'Contraseña del banco: 1234', 'descripcion' => 'Dato privado de Luis'],
        ]);
    }
}
