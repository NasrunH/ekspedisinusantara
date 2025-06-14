<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shipment::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $shipments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('shipments.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get next available ID
        $nextId = $this->getNextId();
        return view('shipments.create', compact('nextId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|unique:shipments',
            'tracking_number' => 'required|string|max:255|unique:shipments',
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_phone' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'recipient_phone' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_transit,delivered',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare data - weight is converted from kg to grams (integer)
            $data = $request->except(['_token']);
            $data['weight'] = (int)($request->weight * 1000); // Convert kg to grams as integer
            
            // Laravel akan otomatis mengatur created_at dan updated_at dengan timestamp lengkap

            $shipment = Shipment::create($data);

            DB::commit();

            return redirect()->route('shipments.index')
                ->with('success', 'Pengiriman berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pengiriman: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return view('shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        return view('shipments.edit', compact('shipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'required|string|max:255|unique:shipments,tracking_number,' . $shipment->id,
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_phone' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'recipient_phone' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_transit,delivered',
        ], [
            'tracking_number.required' => 'Nomor resi wajib diisi.',
            'tracking_number.unique' => 'Nomor resi sudah digunakan.',
            'sender_name.required' => 'Nama pengirim wajib diisi.',
            'sender_address.required' => 'Alamat pengirim wajib diisi.',
            'sender_phone.required' => 'Telepon pengirim wajib diisi.',
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'recipient_address.required' => 'Alamat penerima wajib diisi.',
            'recipient_phone.required' => 'Telepon penerima wajib diisi.',
            'weight.required' => 'Berat barang wajib diisi.',
            'weight.numeric' => 'Berat harus berupa angka.',
            'weight.min' => 'Berat minimal 0.01 kg.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare data - weight is converted from kg to grams (integer)
            $data = $request->except(['_token', '_method']);
            $data['weight'] = (int)($request->weight * 1000); // Convert kg to grams as integer
            
            // Laravel akan otomatis mengupdate updated_at dengan timestamp lengkap

            $shipment->update($data);

            DB::commit();

            return redirect()->route('shipments.show', $shipment)
                ->with('success', 'Pengiriman berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengiriman: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        try {
            DB::beginTransaction();

            $shipment->delete();

            DB::commit();

            return redirect()->route('shipments.index')
                ->with('success', 'Pengiriman berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengiriman: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint untuk tracking
     */
    public function track(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');
        
        if (!$trackingNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor resi diperlukan'
            ], 400);
        }

        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Pengiriman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tracking_number' => $shipment->tracking_number,
                'status' => $shipment->status,
                'status_label' => $shipment->status_label,
                'sender_name' => $shipment->sender_name,
                'recipient_name' => $shipment->recipient_name,
                'weight' => number_format($shipment->weight / 1000, 2), // Convert grams to kg
                'created_at' => $shipment->formatted_created_at,
                'updated_at' => $shipment->formatted_updated_at,
            ]
        ]);
    }

    /**
     * Sync data between databases - UPDATED FOR RAILWAY STRUCTURE
     */
    public function syncDatabases(Request $request)
    {
        try {
            // Log untuk debugging
            Log::info('Sync request received', [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'is_ajax' => $request->ajax()
            ]);

            // Test koneksi MySQL
            try {
                $mysqlTest = DB::connection('mysql')->select('SELECT 1 as test');
                Log::info('MySQL connection test passed');
            } catch (\Exception $e) {
                Log::error('MySQL connection failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi MySQL gagal: ' . $e->getMessage()
                ], 500);
            }

            // Test koneksi PostgreSQL
            try {
                $pgsqlTest = DB::connection('pgsql')->select('SELECT 1 as test');
                Log::info('PostgreSQL connection test passed');
            } catch (\Exception $e) {
                Log::error('PostgreSQL connection failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi PostgreSQL gagal: ' . $e->getMessage()
                ], 500);
            }

            // Ambil data dari MySQL
            $mysqlShipments = DB::connection('mysql')->table('shipments')->get();
            Log::info('MySQL data retrieved', ['count' => $mysqlShipments->count()]);

            $syncCount = 0;
            $errors = [];

            // Sinkronisasi ke PostgreSQL
            foreach ($mysqlShipments as $shipment) {
                try {
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
                    
                    $syncCount++;
                    Log::info('Synced record', ['id' => $shipment->id]);
                    
                } catch (\Exception $e) {
                    $error = "Error sync ID {$shipment->id}: " . $e->getMessage();
                    $errors[] = $error;
                    Log::error($error);
                }
            }

            $response = [
                'success' => true,
                'message' => 'Sinkronisasi berhasil',
                'synced_records' => $syncCount,
                'total_records' => $mysqlShipments->count(),
                'device' => env('DEVICE_NAME', 'unknown')
            ];

            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }

            Log::info('Sync completed', $response);
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Sync failed with exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next available ID
     */
    private function getNextId()
    {
        try {
            $lastShipment = Shipment::orderBy('id', 'desc')->first();
            return $lastShipment ? $lastShipment->id + 1 : 1;
        } catch (\Exception $e) {
            return 1;
        }
    }
}
