<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransferController extends Controller
{
    public function internalTransfer(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id'   => 'required|integer|different:from_warehouse_id',
            'products'          => 'required|array|min:1',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity'   => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $transferId = DB::table('inventory_transfers')->insertGetId([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id'   => $validated['to_warehouse_id'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            foreach ($validated['products'] as $item) {
                DB::table('inventory_transfer_items')->insert([
                    'transfer_id' => $transferId,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Internal transfer successful',
                'transfer_id' => $transferId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Transfer failed', 'error' => $e->getMessage()], 500);
        }
    }
}
