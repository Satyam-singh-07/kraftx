@component('account.partials.shell', ['seo' => $seo])
    <style>
        .notify-products-grid {
            display: grid;
            gap: 18px;
        }

        .notify-product-card {
            display: grid;
            grid-template-columns: 128px 1fr;
            gap: 18px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .notify-product-card:hover {
            transform: translateY(-2px);
            border-color: #d9d1c3;
            box-shadow: 0 14px 36px rgba(17, 17, 17, .08);
        }

        .notify-product-card__image {
            display: block;
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            overflow: hidden;
            background: #f7f3ed;
        }

        .notify-product-card__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .notify-product-card__body {
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
        }

        .notify-product-card__top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 16px;
        }

        .notify-product-card__name {
            color: #111;
            font-weight: 600;
            line-height: 1.35;
        }

        .notify-product-card__meta {
            margin: 6px 0 0;
            color: var(--text-3);
            font-size: 13px;
        }

        .notify-product-card__price {
            white-space: nowrap;
            color: #111;
            font-weight: 700;
        }

        .notify-product-card__badges,
        .notify-product-card__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .notify-badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .notify-badge--available { background: #e8f7ee; color: #16733b; }
        .notify-badge--unavailable { background: #fff0f0; color: #b42318; }
        .notify-badge--notified { background: #edf2ff; color: #3153b7; }
        .notify-badge--pending { background: #fff8e6; color: #9a6700; }
        .notify-badge--stock { background: #f4f4f5; color: #3f3f46; }

        .notify-product-card__remove {
            color: #b42318;
        }

        .notify-empty {
            padding: 56px 24px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #faf8f4;
        }

        @media (max-width: 575px) {
            .notify-product-card {
                grid-template-columns: 96px 1fr;
                gap: 12px;
                padding: 12px;
            }

            .notify-product-card__top {
                flex-direction: column;
                gap: 6px;
            }
        }
    </style>

    <h4 class="account-title">My Notify Products</h4>

    @if($notifyRequests->count())
        <div class="notify-products-grid">
            @foreach($notifyRequests as $notifyRequest)
                <x-notify-product-card :notify-request="$notifyRequest" />
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifyRequests->links() }}
        </div>
    @else
        <div class="notify-empty text-center">
            <h6 class="mb-8">No notify products yet</h6>
            <p class="cl-text-2 mb-20">Products you subscribe to will appear here.</p>
            <a href="{{ route('products.index') }}" class="tf-btn animate-btn">Browse Products</a>
        </div>
    @endif
@endcomponent
