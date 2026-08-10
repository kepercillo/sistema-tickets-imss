<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function up(): void
    {
       $this->call(import:catalogos); // Vacio

       $this->call(UserSeeder::class);
    }

    public function run(): void
    {
       // 1. Crear Categorías iniciales
        $categories = [
            'SOPORTE TÉCNICO', 
            'REDES E INTERNET', 
            'IMPRESORAS', 
            'FALLA DE EQUIPO', 
            'CORREO INSTITUCIONAL', 
            'VIDEO CONFERENCIA', 
            'ERROR DE PROGRAMA'
        ];
        
        foreach ($categories as $category) {
            Category::create(['name' => mb_strtoupper($category, 'UTF-8')]);
        }

        // 2. Ejecutar el comando de importación de catálogos desde el CSV
        $this->command->info('IMPORTANDO CATÁLOGOS DESDE COMANDO ARTISAN...');
        Artisan::call('import:catalogos');
        $this->command->info('CATÁLOGOS IMPORTADOS CON ÉXITO.');

    // 3. Crear Usuario Administrador
    User::create([
        'name' => 'Administrador General',
        'username' => 'admin',
        'email' => 'admin@imss.com',
        'clues' => 'CSIMB006731',
        'department' => 'TECNOLOGÍAS DE LA INFORMACIÓN',
        'password' => Hash::make('admin123'),
        'role' => 'ADMINISTRADOR',
    ]);

    // 4. Crear Usuario de Soporte (Técnico)
    User::create([
        'name' => 'Técnico Soporte 1',
        'username' => 'soporte1',
        'email' => 'soporte1@imss.com',
        'clues' => 'CSIMB006731',
        'department' => 'TECNOLOGÍAS DE LA INFORMACIÓN',
        'password' => Hash::make('soporte123'),
        'role' => 'SOPORTE',
    ]);

    // 5. Crear Usuario Empleado común
    User::create([
        'name' => 'Juan Pérez',
        'username' => 'juan.perez',
        'email' => 'juan.perez@imss.com',
        'clues' =>'CSIMB006731',
        'department' => 'TECNOLOGÍAS DE LA INFORMACIÓN',
        'password' => Hash::make('user123'),
        'role' => 'EMPLEADO',
    ]);
    }
}