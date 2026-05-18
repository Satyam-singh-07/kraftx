<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductNotifyRequest;
use App\Models\User;
use App\Services\OrderLinkingService;
use App\Services\ProductDemandService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected OrderLinkingService $orderLinkingService,
        protected ProductDemandService $productDemandService
    ) {
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $this->orderLinkingService->linkGuestOrders($user);
        $orders = $this->ordersQuery($user)->latest()->take(5)->get();

        return view('account.dashboard', [
            'seo' => $this->seo('Account'),
            'orders' => $orders,
            'stats' => [
                'pending' => $this->ordersQuery($user)->whereIn('status', ['pending', 'processing'])->count(),
                'cancelled' => $this->ordersQuery($user)->where('status', 'cancelled')->count(),
                'total' => $this->ordersQuery($user)->count(),
            ],
        ]);
    }

    public function orders(Request $request): View
    {
        $this->orderLinkingService->linkGuestOrders($request->user());

        return view('account.orders', [
            'seo' => $this->seo('Your Orders'),
            'orders' => $this->ordersQuery($request->user())->latest()->paginate(10),
        ]);
    }

    public function addresses(Request $request): View
    {
        return view('account.addresses', [
            'seo' => $this->seo('My Address'),
            'user' => $request->user(),
        ]);
    }

    public function notifyProducts(Request $request): View
    {
        return view('account.notify-products', [
            'seo' => $this->seo('My Notify Products'),
            'notifyRequests' => $this->productDemandService->requestsForUser($request->user()),
        ]);
    }

    public function removeNotifyProduct(Request $request, ProductNotifyRequest $notifyRequest): RedirectResponse
    {
        Gate::authorize('delete', $notifyRequest);

        $notifyRequest->delete();

        return back()->with('success', 'Notify request removed.');
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Address updated successfully.');
    }

    public function settings(Request $request): View
    {
        return view('account.settings', [
            'seo' => $this->seo('Settings'),
            'user' => $request->user(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    private function ordersQuery(User $user)
    {
        return $this->orderLinkingService->scopeForUser(
            Order::with('items.product'),
            $user
        );
    }

    private function seo(string $title): array
    {
        return [
            'title' => $title.' | '.config('app.name', 'KraftX'),
            'description' => 'Manage your KraftX account.',
            'robots' => 'noindex,follow',
        ];
    }
}
