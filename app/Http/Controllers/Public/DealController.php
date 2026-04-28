<?php

namespace App\Http\Controllers\Public;

use App\Helpers\SeoHelper;
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

        $seo = [
            'title' => 'Deals & Offers | ' . config('app.name', 'KraftX'),
            'description' => 'Browse active KraftX deals, limited-time offers, and seasonal promotions.',
            'canonical' => route('deals.index'),
            'type' => 'website',
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Deals', 'url' => route('deals.index')],
                ]),
                SeoHelper::itemListSchema('Deals', $deals, fn ($deal) => [
                    'url' => route('deals.show', $deal->slug),
                    'name' => $deal->title,
                ]),
            ],
        ];

        return view('public.deals.index', compact('deals', 'seo'));
    }

    public function show($slug)
    {
        $deal = Deal::where('slug', $slug)->with(['products.images', 'products.variants'])->firstOrFail();
        if (!$deal->isValid()) {
            abort(404, 'Deal has expired or is inactive.');
        }

        $seo = SeoHelper::forModel($deal, [
            'title' => ($deal->seoMeta?->meta_title ?: $deal->title) . ' | ' . config('app.name', 'KraftX'),
            'description' => $deal->seoMeta?->meta_description
                ?? $deal->description
                ?? 'Explore active offers from KraftX.',
            'canonical' => route('deals.show', $deal->slug),
            'type' => 'website',
            'image' => $deal->banner_image ? asset('storage/' . $deal->banner_image) : asset(config('seo.default_image')),
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'Deals', 'url' => route('deals.index')],
                    ['name' => $deal->title, 'url' => route('deals.show', $deal->slug)],
                ]),
                SeoHelper::itemListSchema($deal->title . ' Products', $deal->products, fn ($product) => [
                    'url' => route('product.show', $product->slug),
                    'name' => $product->name,
                ]),
            ],
        ]);

        return view('public.deals.show', compact('deal', 'seo'));
    }
}
