<?php

namespace App\Services;

use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DealService
{
    public function createDeal(array $data)
    {
        DB::beginTransaction();
        try {
            $deal = Deal::create($data);

            if (!empty($data['product_ids'])) {
                $deal->products()->sync($data['product_ids']);
            }

            Cache::forget('active_deals');

            DB::commit();
            return $deal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDeal(Deal $deal, array $data)
    {
        DB::beginTransaction();
        try {
            $deal->update($data);

            if (isset($data['product_ids'])) {
                $deal->products()->sync($data['product_ids']);
            }

            Cache::forget('active_deals');

            DB::commit();
            return $deal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteDeal(Deal $deal)
    {
        $deal->delete();
        Cache::forget('active_deals');
        return true;
    }
}
