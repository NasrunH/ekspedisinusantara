<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log SQL queries in development
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    [
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'connection' => $query->connection->getName()
                    ]
                );
            });
        }
        
        // Disable strict mode for MySQL
        if (DB::connection() instanceof \Illuminate\Database\MySqlConnection) {
            DB::statement('SET SESSION sql_mode=""');
        }
        
        // Test database connections on boot
        try {
            // Only test MySQL initially to avoid blocking app startup
            DB::connection('mysql')->getPdo();
            Log::info('MySQL connection successful');
        } catch (\Exception $e) {
            Log::error('MySQL connection failed: ' . $e->getMessage());
        }
    }
}
