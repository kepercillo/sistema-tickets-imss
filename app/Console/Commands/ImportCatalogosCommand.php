<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CatalogoClues;
use App\Models\CatalogoDepartament;
use Illuminate\Support\Facades\DB;
use App\Models\Directory;
use Illuminate\Support\Str;

class ImportCatalogosCommand extends Command
{
    protected $signature = 'import:catalogos';
    protected $description = 'IMPORTA CLUES Y DEPARTAMENTOS DESDE ARCHIVOS CSV A LAS TABLAS DE CATÁLOGOS';

    public function handle()
    {
        $this->info('=== INICIANDO IMPORTACIÓN DE CATÁLOGOS ===');

        // 1. IMPORTAR DEPARTAMENTOS (Sin encabezado - Una sola columna)
        $pathDepartamentos = database_path('data/departamentos.csv');
        if (file_exists($pathDepartamentos)) {
            $this->info('PROCESANDO DEPARTAMENTOS...');
            $file = fopen($pathDepartamentos, 'r');
            $countDepto = 0;

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
                    if (!empty($row[0])) {
                        // firstOrCreate evita duplicados si corres el script más de una vez
                        CatalogoDepartament::firstOrCreate([
                            'name' => mb_strtoupper(trim($row[0]), 'UTF-8')
                        ]);
                        $countDepto++;
                    }
                }
                DB::commit();
                $this->info("DEPARTAMENTOS PROCESADOS: {$countDepto} REGISTROS.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('ERROR AL IMPORTAR DEPARTAMENTOS: ' . $e->getMessage());
            }
            fclose($file);
        }

        // 2. IMPORTAR CLUES (Con encabezado - Columna 0: CLUES, Columna 1: Nombre)
        $pathClues = database_path('data/clues.csv');
if (file_exists($pathClues)) {
    $this->info('PROCESANDO CLUES DESDE EL EXCEL DE 33 COLUMNAS...');
    $file = fopen($pathClues, 'r');
    
    // Saltamos la primera línea de encabezados
    fgetcsv($file, 2000, ",");
    $countClues = 0;

    DB::beginTransaction();
    try {
        while (($row = fgetcsv($file, 2000, ",")) !== FALSE) {
            if (!empty($row[0])) { // Aseguramos que venga el valor de la CLUES
                CatalogoClues::updateOrCreate(
                    ['clues' => trim($row[0])],
                    [
                        'nombre_unidad'              => $row[1] ?? null,
                        'nombre_comercial'           => $row[2] ?? null,
                        'region'                     => $row[3] ?? null,
                        'nombre_institucion'         => $row[4] ?? null,
                        'clave_entidad'              => $row[5] ?? null,
                        'entidad'                    => $row[6] ?? null,
                        'municipio'                  => $row[7] ?? null,
                        'localidad'                  => $row[8] ?? null,
                        'clave_jurisdiccion'         => $row[9] ?? null,
                        'jurisdiccion'               => $row[10] ?? null,
                        'nombre_tipo_establecimiento'=> $row[11] ?? null,
                        'clave_tipo_vialidad'        => $row[12] ?? null,
                        'tipo_vialidad'              => $row[13] ?? null,
                        'vialidad'                   => $row[14] ?? null,
                        'numero_exterior'            => $row[15] ?? null,
                        'numero_interior'            => $row[16] ?? null,
                        'clave_tipo_asentamiento'    => $row[17] ?? null,
                        'tipo_asentamiento'          => $row[18] ?? null,
                        'asentamiento'               => $row[19] ?? null,
                        'codigo_postal'              => $row[20] ?? null,
                        'referencias_domicilio'      => $row[21] ?? null,
                        'telefono'                   => $row[22] ?? null,
                        'nivel_atencion'             => $row[23] ?? null,
                        'clave_estrato_unidad'       => $row[24] ?? null,
                        'estrato_unidad'             => $row[25] ?? null,
                        'clave_tipo_obra'            => $row[26] ?? null,
                        'tipo_obra'                  => $row[27] ?? null,
                        'clave_propiedad_inmueble'   => $row[28] ?? null,
                        'propiedad_inmueble'         => $row[29] ?? null,
                        'observaciones'              => $row[30] ?? null,
                        'latitud'                    => $row[31] ?? null,
                        'longitud'                   => $row[32] ?? null,
                    ]
                );
                $countClues++;
            }
        }
        DB::commit();
        $this->info("CLUES PROCESADAS Y ACTUALIZADAS: {$countClues} REGISTROS.");
    } catch (\Exception $e) {
        DB::rollBack();
        $this->error('ERROR AL IMPORTAR CLUES: ' . $e->getMessage());
    }
    fclose($file);
}

// Ruta hacia la carpeta database/data/
    $path = database_path('data/directorio.csv'); 

    if (!file_exists($path)) {
        $this->error("El archivo del directorio no existe en: {$path}");
        return;
    }

    $file = fopen($path, 'r');
    
    // Omitir la primera línea (Encabezados)
    $header = fgetcsv($file, 1000, ',');

    $this->info("Importando directorio institucional desde carpeta data...");
    
    // Desactivar restricciones de claves foráneas temporalmente para un vaciado seguro
    //Directory::truncate();

    while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
    // 1. Validar que la fila tenga al menos el nombre y no sea una línea muerta
    if (!isset($row[1]) || empty(trim($row[1]))) {
        continue; 
    }

    // 2. Limpieza de caracteres extraños y asegurar nulos si el índice no existe
    Directory::create([
        'NOMBRE'        => !empty($row[0]) ? Str::upper(trim($row[0])) : null, // Cambiado de $row[1] a $row[0]
        'EMAIL'         => !empty($row[1]) ? Str::upper(trim($row[1])) : null, // Cambiado de $row[2] a $row[1]
        'CUENTA'        => !empty($row[2]) ? Str::upper(trim($row[2])) : null, // Cambiado de $row[3] a $row[2]
        'PUESTO'        => !empty($row[3]) ? Str::upper(trim($row[3])) : null, // Cambiado de $row[4] a $row[3]
        'COORDINACION'  => !empty($row[4]) ? Str::upper(trim($row[4])) : null, // Cambiado de $row[5] a $row[4]
        'CURP'          => !empty($row[5]) ? Str::upper(trim($row[5])) : null, // Cambiado de $row[6] a $row[5]
        'RFC'           => !empty($row[7]) ? Str::upper(trim($row[6])) : null, // Cambiado de $row[7] a $row[6]
        'TELEFONO'      => !empty($row[7]) ? Str::upper(trim($row[7])) : null, // Cambiado de $row[8] a $row[7]
        'OBSERVACIONES' => !empty($row[8]) ? Str::upper(trim($row[8])) : null, // Cambiado de $row[10] a $row[8]
    ]);
}

    fclose($file);
    $this->info("¡Directorio cargado correctamente desde data!");

        $this->info('=== PROCESO FINALIZADO CON ÉXITO ===');
    }
}