<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\BannerImageOptimizer;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(
        protected BannerImageOptimizer $bannerImageOptimizer
    ) {}

    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'status' => 'boolean',
            'placement' => 'required|string',
        ]);

        $desktopImage = $request->file('image');
        $mobileImage = $request->file('mobile_image');
        unset($validated['image'], $validated['mobile_image']);
        $validated['image'] = '';
        $validated['status'] = $request->boolean('status');

        $banner = Banner::create($validated);
        $imageData = $this->bannerImageOptimizer->storeDesktopUpload($banner, $desktopImage);

        if ($mobileImage) {
            $imageData['mobile_image'] = $this->bannerImageOptimizer->storeMobileUpload($banner, $mobileImage);
        }

        $banner->update($imageData);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'status' => 'boolean',
            'placement' => 'required|string',
        ]);

        $oldImage = $banner->image;
        $oldMobileImage = $banner->mobile_image;

        if ($request->hasFile('image')) {
            $validated = array_merge(
                $validated,
                $this->bannerImageOptimizer->storeDesktopUpload($banner, $request->file('image'))
            );
            $this->bannerImageOptimizer->deleteLegacyPath($oldImage);
            $this->bannerImageOptimizer->deleteLegacyPath($oldMobileImage);
        }
        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image'] = $this->bannerImageOptimizer->storeMobileUpload($banner, $request->file('mobile_image'));
            $this->bannerImageOptimizer->deleteLegacyPath($oldMobileImage);
        }

        $validated['status'] = $request->boolean('status');
        
        if ($banner->update($validated)) {
            return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
        }

        return back()->with('error', 'Failed to update banner.');
    }

    public function destroy(Banner $banner)
    {
        $this->bannerImageOptimizer->deleteVariants($banner);
        $banner->delete();
        return back()->with('success', 'Banner deleted successfully.');
    }
}
