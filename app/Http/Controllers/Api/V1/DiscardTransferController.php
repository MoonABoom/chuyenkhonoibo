<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscardTransferController extends Controller
{
    public function discard(Request $request)
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
            $discardId = DB::table('discard_transfers')->insertGetId([
                'warehouse_id' => $validated['warehouse_id'],
                'reason' => $validated['reason'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($validated['products'] as $item) {
                DB::table('discard_transfer_items')->insert([
                    'discard_transfer_id' => $discardId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Discard transfer successful',
                'discard_id' => $discardId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Discard transfer failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

