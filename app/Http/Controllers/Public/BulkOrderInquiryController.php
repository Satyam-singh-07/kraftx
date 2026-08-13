<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkOrderInquiryRequest;
use App\Mail\BulkOrderAdminNotificationMail;
use App\Mail\BulkOrderCustomerConfirmationMail;
use App\Models\BulkOrderInquiry;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BulkOrderInquiryController extends Controller
{
    public function store(BulkOrderInquiryRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->status, 404);

        $inquiry = BulkOrderInquiry::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'product_url' => route('product.show', $product->slug),
            'name' => trim($request->string('name')->toString()),
            'phone' => trim($request->string('phone')->toString()),
            'email' => strtolower(trim($request->string('email')->toString())),
            'quantity' => (int) $request->integer('quantity'),
            'message' => $request->filled('message') ? trim($request->string('message')->toString()) : null,
            'status' => 'new',
        ]);

        try {
            Mail::to($inquiry->email, $inquiry->name)
                ->sendNow(new BulkOrderCustomerConfirmationMail($inquiry));
        } catch (Throwable $exception) {
            Log::error('Bulk order customer email notification failed', [
                'inquiry_id' => $inquiry->id,
                'message' => $exception->getMessage(),
            ]);
        }

        foreach ($this->adminEmails() as $adminEmail) {
            try {
                Mail::to($adminEmail)->sendNow(new BulkOrderAdminNotificationMail($inquiry));
            } catch (Throwable $exception) {
                Log::error('Bulk order admin email notification failed', [
                    'inquiry_id' => $inquiry->id,
                    'recipient' => $adminEmail,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return back()
            ->with('bulk_order_success', 'Thank you. Your bulk order request has been received. Our team will contact you shortly.')
            ->with('bulk_order_inquiry_id', $inquiry->id);
    }

    protected function adminEmails(): array
    {
        return array_values(array_filter(array_map('trim', [
            config('seo.support_email'),
            'sanjayadav448@gmail.com',
            'swatidayal2004@gmail.com',
            'satyamsingh962572@gmail.com',
        ])));
    }
}
