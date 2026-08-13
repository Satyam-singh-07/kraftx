<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkOrderInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulkOrderInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status']);
        $inquiries = BulkOrderInquiry::with('product')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('product_name', 'like', "%{$search}%")
                        ->orWhere('product_sku', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bulk-orders.index', compact('inquiries', 'filters'));
    }

    public function show(BulkOrderInquiry $bulkOrder): View
    {
        return view('admin.bulk-orders.show', ['inquiry' => $bulkOrder->load('product')]);
    }

    public function updateStatus(Request $request, BulkOrderInquiry $bulkOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,contacted,quoted,converted,closed'],
        ]);

        $bulkOrder->update([
            'status' => $validated['status'],
            'responded_at' => in_array($validated['status'], ['contacted', 'quoted', 'converted', 'closed'], true)
                ? ($bulkOrder->responded_at ?: now())
                : null,
        ]);

        return back()->with('success', 'Bulk inquiry status updated.');
    }

    public function destroy(BulkOrderInquiry $bulkOrder): RedirectResponse
    {
        $bulkOrder->delete();

        return redirect()->route('admin.bulk-orders.index')->with('success', 'Bulk inquiry deleted.');
    }
}
