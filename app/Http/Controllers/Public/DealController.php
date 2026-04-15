<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Deal;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::where('status', true)
            ->where(function($q) {
                $now = now();
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) {
                $now = now();
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('priority', 'desc')
            ->get();
            
        return view('public.deals.index', compact('deals'));
    }

    public function show($slug)
    {
        $deal = Deal::where('slug', $slug)->with(['products', 'categories'])->firstOrFail();
        if (!$deal->isValid()) {
            abort(404, 'Deal has expired or is inactive.');
        }

        return view('public.deals.show', compact('deal'));
    }
}
