<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class BannerController extends Controller
{
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

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }
        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image'] = $this->uploadImage($request->file('mobile_image'));
        }

        $validated['status'] = $request->boolean('status');
        Banner::create($validated);

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

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $this->uploadImage($request->file('image'));
        }
        if ($request->hasFile('mobile_image')) {
            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            $validated['mobile_image'] = $this->uploadImage($request->file('mobile_image'));
        }

        $validated['status'] = $request->boolean('status');
        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        if ($banner->mobile_image) {
            Storage::disk('public')->delete($banner->mobile_image);
        }
        $banner->delete();
        return back()->with('success', 'Banner deleted successfully.');
    }

    protected function uploadImage($file)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'banners/' . $filename;

        // Banners are usually wide, so scale down to 1920 width if larger
        $img = Image::decode($file)->scaleDown(width: 1920);

        Storage::disk('public')->put(
            $path,
            (string) $img->encodeUsingFileExtension($file->getClientOriginalExtension(), quality: 80)
        );
        return $path;
    }
}
