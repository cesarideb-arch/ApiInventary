<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;


class DatabaseController extends Controller
{
     public function exportDatabase()
    {
        $dbHost = env('DB_HOST', 'localhost');
        $dbName = env('DB_DATABASE', 'api');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');
    
        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
        // Get the user's download directory
        $user = getenv('USERPROFILE');
        $directoryPath = $user . '\\Downloads\\';
    
        $filePath = $directoryPath . $fileName;
    
        // Create directory if it does not exist
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
    
        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s > "%s"',
            $mysqldumpPath,
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbName),
            $filePath
        );
    
        echo "Executing command: $command\n";
    
        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);
    
        if ($returnVar !== 0) {
            echo "Command failed with return code $returnVar\n";
            echo implode("\n", $output);
        } else {
            echo "Backup created successfully at $filePath\n";
        }
    }    
    public function importDatabase(Request $request)
    {
        try {
            $request->validate([
                'sql_file' => 'required|file|mimes:sql,txt|max:10240' // 10MB max
            ]);

            $dbHost = env('DB_HOST', 'localhost');
            $dbName = env('DB_DATABASE', 'api');
            $dbUser = env('DB_USERNAME', 'root');
            $dbPass = env('DB_PASSWORD', '');

            $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
            
            $sqlFile = $request->file('sql_file');
            $tempPath = $sqlFile->getRealPath();

            $command = sprintf(
                '"%s" --user=%s --password=%s --host=%s %s < "%s"',
                $mysqlPath,
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbHost),
                escapeshellarg($dbName),
                $tempPath
            );

            $output = null;
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                \Log::error('Import failed', ['returnVar' => $returnVar, 'output' => $output]);
                return response()->json(['error' => 'Error al importar la base de datos'], 500);
            }

            // Solo retornar éxito - el frontend manejará el logout
            return response()->json([
                'message' => 'Base de datos importada exitosamente. La sesión se cerrará automáticamente.',
                'logout_required' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al importar la base de datos'], 500);
        }
    }

     public function resetDatabase(Request $request)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = reset($table);
                if ($tableName !== 'migrations') {
                    DB::table($tableName)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            // Verificar y ejecutar el seeder
            $seederClass = 'UserSeed';

            // Intentar diferentes namespaces posibles para el seeder
            $possibleNamespaces = [
                "Database\\Seeders\\{$seederClass}",
                "App\\Database\\Seeders\\{$seederClass}",
                $seederClass // Por si está en el namespace global
            ];

            $seederFound = false;
            $seederMessage = '';

            foreach ($possibleNamespaces as $namespace) {
                if (class_exists($namespace)) {
                    Artisan::call('db:seed', ['--class' => $namespace]);
                    $seederMessage = ' y usuarios cargados';
                    $seederFound = true;
                    break;
                }
            }

            if (!$seederFound) {
                \Log::warning("Seeder {$seederClass} no encontrado en ningún namespace");
                $seederMessage = ' (pero no se pudo cargar el seeder de usuarios)';
            }

            return response()->json([
                'message' => 'Base de datos restablecida exitosamente' . $seederMessage . '. La sesión se cerrará automáticamente.',
                'logout_required' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Reset error: ' . $e->getMessage());
            return response()->json(['error' => 'Error al restablecer la base de datos: ' . $e->getMessage()], 500);
        }
    }
}