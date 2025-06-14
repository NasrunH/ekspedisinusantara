<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Shipment;

class SyncDatabases extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:sync {--direction=both : Direction of sync (mysql-to-postgres, postgres-to-mysql, both)}';

    /**
     * The console command description.
     */
    protected $description = 'Sinkronisasi data antara MySQL dan PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $direction = $this->option('direction');
        
        $this->info('Memulai sinkronisasi database...');
        
        try {
            switch ($direction) {
                case 'mysql-to-postgres':
                    $this->syncMysqlToPostgres();
                    break;
                case 'postgres-to-mysql':
                    $this->syncPostgresToMysql();
                    break;
                case 'both':
                default:
                    $this->syncMysqlToPostgres();
                    $this->syncPostgresToMysql();
                    break;
            }
            
            $this->info('✅ Sinkronisasi database berhasil!');
        } catch (\Exception $e) {
            $this->error('❌ Gagal melakukan sinkronisasi: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function syncMysqlToPostgres()
    {
        $this->info('Sinkronisasi MySQL → PostgreSQL...');
        
        try {
            // Cek apakah tabel shipments ada di PostgreSQL
            $tableExists = DB::connection('pgsql')
                ->select("SELECT to_regclass('public.shipments') as exists");
            
            if (empty($tableExists[0]->exists)) {
                // Buat tabel jika belum ada
                $this->info('Creating shipments table in PostgreSQL');
                
                DB::connection('pgsql')->statement('
                    CREATE TABLE IF NOT EXISTS shipments (
                        id SERIAL PRIMARY KEY,
                        tracking_number VARCHAR(20) NOT NULL UNIQUE,
                        sender_name VARCHAR(100) NOT NULL,
                        sender_address TEXT NOT NULL,
                        sender_phone VARCHAR(20) NOT NULL,
                        recipient_name VARCHAR(100) NOT NULL,
                        recipient_address TEXT NOT NULL,
                        recipient_phone VARCHAR(20) NOT NULL,
                        weight DECIMAL(10,2) NOT NULL,
                        description TEXT,
                        status VARCHAR(20) CHECK (status IN (\'pending\', \'in_transit\', \'delivered\')) NOT NULL DEFAULT \'pending\',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
                
                // Buat trigger untuk update timestamp
                DB::connection('pgsql')->statement('
                    CREATE OR REPLACE FUNCTION update_modified_column()
                    RETURNS TRIGGER AS $$
                    BEGIN
                        NEW.updated_at = NOW();
                        RETURN NEW;
                    END;
                    $$ LANGUAGE plpgsql
                ');
                
                DB::connection('pgsql')->statement('
                    DROP TRIGGER IF EXISTS update_shipments_modtime ON shipments;
                    CREATE TRIGGER update_shipments_modtime
                    BEFORE UPDATE ON shipments
                    FOR EACH ROW
                    EXECUTE FUNCTION update_modified_column()
                ');
            }
            
            $mysqlShipments = DB::connection('mysql')->table('shipments')->get();
            $syncedCount = 0;
            
            foreach ($mysqlShipments as $shipment) {
                $shipmentArray = (array) $shipment;
                
                // Cek apakah record sudah ada
                $exists = DB::connection('pgsql')
                    ->table('shipments')
                    ->where('id', $shipment->id)
                    ->exists();
                
                if ($exists) {
                    // Update
                    DB::connection('pgsql')
                        ->table('shipments')
                        ->where('id', $shipment->id)
                        ->update($shipmentArray);
                } else {
                    // Insert
                    DB::connection('pgsql')
                        ->table('shipments')
                        ->insert($shipmentArray);
                }
                
                $syncedCount++;
            }
            
            $this->info("✅ {$syncedCount} record berhasil disinkronkan ke PostgreSQL");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function syncPostgresToMysql()
    {
        $this->info('Sinkronisasi PostgreSQL → MySQL...');
        
        try {
            // Cek apakah tabel shipments ada di MySQL
            try {
                $tableExists = DB::connection('mysql')
                    ->select("SHOW TABLES LIKE 'shipments'");
                
                if (empty($tableExists)) {
                    // Buat tabel jika belum ada
                    $this->info('Creating shipments table in MySQL');
                    
                    DB::connection('mysql')->statement('
                        CREATE TABLE IF NOT EXISTS shipments (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            tracking_number VARCHAR(20) NOT NULL UNIQUE,
                            sender_name VARCHAR(100) NOT NULL,
                            sender_address TEXT NOT NULL,
                            sender_phone VARCHAR(20) NOT NULL,
                            recipient_name VARCHAR(100) NOT NULL,
                            recipient_address TEXT NOT NULL,
                            recipient_phone VARCHAR(20) NOT NULL,
                            weight DECIMAL(10,2) NOT NULL,
                            description TEXT,
                            status ENUM(\'pending\', \'in_transit\', \'delivered\') NOT NULL DEFAULT \'pending\',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        )
                    ');
                }
            } catch (\Exception $e) {
                $this->warn("Warning checking MySQL table: " . $e->getMessage());
                // Continue anyway
            }
            
            $postgresShipments = DB::connection('pgsql')->table('shipments')->get();
            $syncedCount = 0;
            
            foreach ($postgresShipments as $shipment) {
                $shipmentArray = (array) $shipment;
                
                // Cek apakah record sudah ada
                $exists = DB::connection('mysql')
                    ->table('shipments')
                    ->where('id', $shipment->id)
                    ->exists();
                
                if ($exists) {
                    // Update
                    DB::connection('mysql')
                        ->table('shipments')
                        ->where('id', $shipment->id)
                        ->update($shipmentArray);
                } else {
                    // Insert
                    DB::connection('mysql')
                        ->table('shipments')
                        ->insert($shipmentArray);
                }
                
                $syncedCount++;
            }
            
            $this->info("✅ {$syncedCount} record berhasil disinkronkan ke MySQL");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
}
