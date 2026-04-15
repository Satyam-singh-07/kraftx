<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Category;
use App\Services\DealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class DealController extends Controller
{
    public function __construct(protected DealService $dealService) {}

    public function index()
    {
        $deals = Deal::orderBy('priority', 'desc')->get();
        return view('admin.deals.index', compact('deals'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.deals.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:deals,slug',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'boolean',
            'priority' => 'integer',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $this->uploadImage($request->file('banner_image'));
        }

        $this->dealService->createDeal($validated);

        return redirect()->route('admin.deals.index')->with('success', 'Deal created successfully.');
    }

    public function edit(Deal $deal)
    {
        $products = Product::all();
        return view('admin.deals.edit', compact('deal', 'products'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:deals,slug,' . $deal->id,
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'boolean',
            'priority' => 'integer',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        if ($request->hasFile('banner_image')) {
            if ($deal->banner_image) {
                Storage::disk('public')->delete($deal->banner_image);
            }
            $validated['banner_image'] = $this->uploadImage($request->file('banner_image'));
        }

        $this->dealService->updateDeal($deal, $validated);

        return redirect()->route('admin.deals.index')->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        if ($deal->banner_image) {
            Storage::disk('public')->delete($deal->banner_image);
        }
        $this->dealService->deleteDeal($deal);
        return back()->with('success', 'Deal deleted successfully.');
    }

    protected function uploadImage($file)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'deals/' . $filename;
        $img = Image::read($file);
        $img->scale(width: 1200); // Standard deal banner size
        Storage::disk('public')->put($path, (string) $img->encode());
        return $path;
    }
}
