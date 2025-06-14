<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseTestController extends Controller
{
    /**
     * Test database connections and return results
     */
    public function testConnections()
    {
        $results = [
            'mysql' => [
                'status' => 'unknown',
                'message' => '',
                'config' => [
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'database' => config('database.connections.mysql.database'),
                    'username' => config('database.connections.mysql.username'),
                    'has_password' => !empty(config('database.connections.mysql.password')),
                ]
            ],
            'pgsql' => [
                'status' => 'unknown',
                'message' => '',
                'config' => [
                    'host' => config('database.connections.pgsql.host'),
                    'port' => config('database.connections.pgsql.port'),
                    'database' => config('database.connections.pgsql.database'),
                    'username' => config('database.connections.pgsql.username'),
                    'has_password' => !empty(config('database.connections.pgsql.password')),
                ]
            ],
            'env' => [
                'APP_NAME' => env('APP_NAME'),
                'DEVICE_NAME' => env('DEVICE_NAME'),
                'DB_CONNECTION' => env('DB_CONNECTION'),
                'DB_HOST' => env('DB_HOST'),
                'DB_PORT' => env('DB_PORT'),
                'DB_DATABASE' => env('DB_DATABASE'),
                'DB_USERNAME' => env('DB_USERNAME'),
                'DB_PASSWORD_LENGTH' => strlen(env('DB_PASSWORD', '')),
                'DB_POSTGRES_HOST' => env('DB_POSTGRES_HOST'),
                'DB_POSTGRES_PORT' => env('DB_POSTGRES_PORT'),
                'DB_POSTGRES_DATABASE' => env('DB_POSTGRES_DATABASE'),
                'DB_POSTGRES_USERNAME' => env('DB_POSTGRES_USERNAME'),
                'DB_POSTGRES_PASSWORD_LENGTH' => strlen(env('DB_POSTGRES_PASSWORD', '')),
            ]
        ];
        
        // Test MySQL
        try {
            DB::connection('mysql')->getPdo();
            $results['mysql']['status'] = 'success';
            $results['mysql']['message'] = 'Connection successful';
            
            // Test table
            try {
                $count = DB::connection('mysql')->table('shipments')->count();
                $results['mysql']['table_count'] = $count;
            } catch (\Exception $e) {
                $results['mysql']['table_error'] = $e->getMessage();
            }
        } catch (\Exception $e) {
            $results['mysql']['status'] = 'error';
            $results['mysql']['message'] = $e->getMessage();
        }
        
        // Test PostgreSQL
        try {
            DB::connection('pgsql')->getPdo();
            $results['pgsql']['status'] = 'success';
            $results['pgsql']['message'] = 'Connection successful';
            
            // Test table
            try {
                $count = DB::connection('pgsql')->table('shipments')->count();
                $results['pgsql']['table_count'] = $count;
            } catch (\Exception $e) {
                $results['pgsql']['table_error'] = $e->getMessage();
            }
        } catch (\Exception $e) {
            $results['pgsql']['status'] = 'error';
            $results['pgsql']['message'] = $e->getMessage();
        }
        
        return response()->json($results);
    }
}
