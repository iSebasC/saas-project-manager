<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Health check básico - verifica que el servidor responda
     */
    public function health()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API is running',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ], 200);
    }

    /**
     * Prueba de conexión a base de datos
     */
    public function testDatabase()
    {
        try {
            // Intenta hacer una consulta simple
            DB::connection()->getPdo();
            
            // Cuenta las tablas para verificar que las migraciones corrieron
            $tables = DB::select('SHOW TABLES');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful',
                'tables_count' => count($tables),
                'database' => config('database.connections.mysql.database'),
                'timestamp' => now()->toISOString(),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Info completa del sistema
     */
    public function info()
    {
        try {
            $dbConnected = false;
            $tablesCount = 0;
            
            try {
                DB::connection()->getPdo();
                $dbConnected = true;
                $tables = DB::select('SHOW TABLES');
                $tablesCount = count($tables);
            } catch (\Exception $e) {
                // Continuar sin base de datos
            }
            
            return response()->json([
                'status' => 'success',
                'app' => [
                    'name' => config('app.name'),
                    'environment' => app()->environment(),
                    'debug' => config('app.debug'),
                    'url' => config('app.url'),
                    'timezone' => config('app.timezone'),
                ],
                'database' => [
                    'connected' => $dbConnected,
                    'tables_count' => $tablesCount,
                    'driver' => config('database.default'),
                ],
                'php' => [
                    'version' => PHP_VERSION,
                ],
                'laravel' => [
                    'version' => app()->version(),
                ],
                'timestamp' => now()->toISOString(),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get system info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
