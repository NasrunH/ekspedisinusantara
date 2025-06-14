<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestDatabaseConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test-connections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all database connections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing database connections...');
        
        // Test MySQL
        $this->info('Testing MySQL connection...');
        try {
            $result = DB::connection('mysql')->select('SELECT 1 as test');
            $this->info('✅ MySQL connection successful');
            
            // Show connection details
            $config = config('database.connections.mysql');
            $this->info("Host: {$config['host']}");
            $this->info("Port: {$config['port']}");
            $this->info("Database: {$config['database']}");
            $this->info("Username: {$config['username']}");
            
            // Test shipments table
            try {
                $count = DB::connection('mysql')->table('shipments')->count();
                $this->info("Shipments table exists with {$count} records");
            } catch (\Exception $e) {
                $this->error("Error accessing shipments table: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->error('❌ MySQL connection failed: ' . $e->getMessage());
        }
        
        $this->newLine();
        
        // Test PostgreSQL
        $this->info('Testing PostgreSQL connection...');
        try {
            // Get connection config
            $config = config('database.connections.pgsql');
            $this->info("Host: {$config['host']}");
            $this->info("Port: {$config['port']}");
            $this->info("Database: {$config['database']}");
            $this->info("Username: {$config['username']}");
            $this->info("Password length: " . (empty($config['password']) ? '0 (EMPTY!)' : strlen($config['password'])));
            
            // Test connection
            $result = DB::connection('pgsql')->select('SELECT 1 as test');
            $this->info('✅ PostgreSQL connection successful');
            
            // Test shipments table
            try {
                $count = DB::connection('pgsql')->table('shipments')->count();
                $this->info("Shipments table exists with {$count} records");
            } catch (\Exception $e) {
                $this->error("Error accessing shipments table: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->error('❌ PostgreSQL connection failed: ' . $e->getMessage());
        }
        
        return 0;
    }
}
