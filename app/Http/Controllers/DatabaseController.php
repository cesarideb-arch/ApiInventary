<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
}    