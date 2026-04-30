<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ReelController extends Controller
{
    public function index()
    {
        $reels = Reel::with('product')->orderBy('sort_order')->get();
        return view('admin.reels.index', compact('reels'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.reels.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'video_url' => 'required|url', // As requested: upload by link
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_id' => 'nullable|exists:products,id',
            'sort_order' => 'integer',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $validated['status'] = $request->boolean('status');
        Reel::create($validated);

        return redirect()->route('admin.reels.index')->with('success', 'Reel added successfully.');
    }

    public function edit(Reel $reel)
    {
        $products = Product::all();
        return view('admin.reels.edit', compact('reel', 'products'));
    }

    public function update(Request $request, Reel $reel)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'product_id' => 'nullable|exists:products,id',
            'sort_order' => 'integer',
            'status' => 'boolean',
        ]);

        $filesToDelete = [];
        if ($request->hasFile('thumbnail')) {
            if ($reel->thumbnail) {
                $filesToDelete[] = $reel->thumbnail;
            }
            $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $validated['status'] = $request->boolean('status');
        
        if ($reel->update($validated)) {
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file);
            }
            return redirect()->route('admin.reels.index')->with('success', 'Reel updated successfully.');
        }

        return back()->with('error', 'Failed to update reel.');
    }

    public function destroy(Reel $reel)
    {
        if ($reel->thumbnail) {
            Storage::disk('public')->delete($reel->thumbnail);
        }
        $reel->delete();
        return back()->with('success', 'Reel deleted successfully.');
    }

    protected function uploadThumbnail($file)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'reels/thumbnails/' . $filename;
        
        $img = Image::read($file);
        // Reels are vertical, so we scale to vertical dimensions (e.g., 400x711)
        $img->cover(400, 711);
        
        Storage::disk('public')->put($path, (string) $img->encode());
        return $path;
    }
}
