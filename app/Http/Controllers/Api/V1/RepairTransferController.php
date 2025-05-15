<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairTransferController extends Controller
{
    public function transferForRepair(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|integer',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $repairTransferId = DB::table('repair_transfers')->insertGetId([
                'warehouse_id' => $validated['warehouse_id'],
                'reason' => $validated['reason'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($validated['products'] as $item) {
                DB::table('repair_transfer_items')->insert([
                    'repair_transfer_id' => $repairTransferId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transfer for repair successful',
                'transfer_id' => $repairTransferId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Transfer for repair failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

