<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shipment extends Model
{
    // Enable Laravel's automatic timestamps
    public $timestamps = true;
    
    // Allow mass assignment for all fields
    protected $fillable = [
        'id',
        'tracking_number',
        'sender_name',
        'sender_address',
        'sender_phone',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'weight',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'weight' => 'integer',
    ];

    /**
     * Status options
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Menunggu',
            'in_transit' => 'Dalam Pengiriman',
            'delivered' => 'Terkirim',
        ];
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get weight in kg (since weight is stored as grams in integer)
     */
    public function getWeightKgAttribute()
    {
        return number_format($this->weight / 1000, 2);
    }

    /**
     * Get formatted created at
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Get formatted updated at
     */
    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at->format('d/m/Y H:i:s');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk pencarian
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('tracking_number', 'like', "%{$search}%")
              ->orWhere('sender_name', 'like', "%{$search}%")
              ->orWhere('recipient_name', 'like', "%{$search}%");
        });
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Automatically replicate data to the other database after save
        static::saved(function ($shipment) {
            try {
                // Get the current connection name
                $currentConnection = $shipment->getConnectionName() ?: config('database.default');
                
                // Determine the target connection
                $targetConnection = ($currentConnection === 'mysql') ? 'pgsql' : 'mysql';
                
                // Get the attributes to replicate
                $attributes = $shipment->getAttributes();
                
                // Convert timestamps to proper format for target database
                if (isset($attributes['created_at'])) {
                    $attributes['created_at'] = $shipment->created_at->format('Y-m-d H:i:s');
                }
                if (isset($attributes['updated_at'])) {
                    $attributes['updated_at'] = $shipment->updated_at->format('Y-m-d H:i:s');
                }
                
                // Check if the record exists in the target database
                $exists = DB::connection($targetConnection)
                    ->table('shipments')
                    ->where('id', $shipment->id)
                    ->exists();
                
                if ($exists) {
                    // Update existing record
                    DB::connection($targetConnection)
                        ->table('shipments')
                        ->where('id', $shipment->id)
                        ->update($attributes);
                } else {
                    // Insert new record
                    DB::connection($targetConnection)
                        ->table('shipments')
                        ->insert($attributes);
                }
                
                \Log::info("Auto-replicated shipment ID {$shipment->id} from {$currentConnection} to {$targetConnection}");
            } catch (\Exception $e) {
                \Log::error("Failed to auto-replicate shipment: " . $e->getMessage(), [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
        
        // Also handle deletion
        static::deleted(function ($shipment) {
            try {
                // Get the current connection name
                $currentConnection = $shipment->getConnectionName() ?: config('database.default');
                
                // Determine the target connection
                $targetConnection = ($currentConnection === 'mysql') ? 'pgsql' : 'mysql';
                
                // Delete from target database
                DB::connection($targetConnection)
                    ->table('shipments')
                    ->where('id', $shipment->id)
                    ->delete();
                
                \Log::info("Auto-deleted shipment ID {$shipment->id} from {$targetConnection}");
            } catch (\Exception $e) {
                \Log::error("Failed to auto-delete shipment: " . $e->getMessage(), [
                    'shipment_id' => $shipment->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }
}
