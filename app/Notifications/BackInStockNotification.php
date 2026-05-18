<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class BackInStockNotification extends Notification
{
    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $primaryImage = $this->product->images->firstWhere('is_primary', true) ?? $this->product->images->first();
        $imageUrl = $primaryImage
            ? (str_starts_with($primaryImage->image_path, 'assets/')
                ? asset($primaryImage->image_path)
                : asset(Storage::url($primaryImage->image_path)))
            : asset('assets/images/product/product-placeholder.jpg');

        return (new MailMessage)
            ->subject($this->product->name . ' is back in stock')
            ->markdown('emails.notifications.back-in-stock', [
                'product' => $this->product,
                'imageUrl' => $imageUrl,
                'price' => $this->product->sale_price ?? $this->product->price,
                'productUrl' => route('product.show', $this->product->slug),
                'logoUrl' => asset('assets/images/logo/logo.png'),
            ]);
    }
}
