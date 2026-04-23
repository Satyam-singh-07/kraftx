<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'featured', 'min_price', 'max_price', 'sort']);
        $products = $this->productRepository->getAllPaginated($filters);
        // dd($products);
        return view('admin.products.index', compact('products', 'filters'));
    }

    public function create()
    {
        $collections = Collection::all();

        return view('admin.products.create', compact('collections'));
    }

    public function store(ProductRequest $request)
    {




        try {
            $dto = ProductDTO::fromRequest(
                $request->validated(),
                $request->file('main_image'),
                $request->file('gallery_images') ?? []
            );


            $product = $this->productService->createProduct($dto);

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            abort(404);
        }

        $collections = Collection::all();

        return view('admin.products.edit', compact('product', 'collections'));
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $dto = ProductDTO::fromRequest(
                $request->validated(),
                $request->file('main_image'),
                $request->file('gallery_images') ?? []
            );
            $this->productService->updateProduct($id, $dto);

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->productRepository->delete($id);
            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        try {
            foreach ($ids as $id) {
                $this->productRepository->delete($id);
            }
            return response()->json(['success' => true, 'message' => 'Products deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $success = $this->productService->toggleStatus($id);
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteImage(\App\Models\ProductImage $image)
    {
        try {
            // Only delete if it's not the primary image or if there are other images
            if ($image->is_primary && $image->product->images()->count() <= 1) {
                return response()->json(['success' => false, 'message' => 'Cannot delete the only image.'], 422);
            }

            // If deleting primary, make another one primary if exists
            if ($image->is_primary) {
                $nextImage = $image->product->images()->where('id', '!=', $image->id)->first();
                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            // Delete from storage
            if (!str_starts_with($image->image_path, 'assets/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
