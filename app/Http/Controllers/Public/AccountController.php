<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $orders = $this->ordersQuery($user->id)->latest()->take(5)->get();

        return view('account.dashboard', [
            'seo' => $this->seo('Account'),
            'orders' => $orders,
            'stats' => [
                'pending' => $this->ordersQuery($user->id)->whereIn('status', ['pending', 'processing'])->count(),
                'cancelled' => $this->ordersQuery($user->id)->where('status', 'cancelled')->count(),
                'total' => $this->ordersQuery($user->id)->count(),
            ],
        ]);
    }

    public function orders(Request $request): View
    {
        return view('account.orders', [
            'seo' => $this->seo('Your Orders'),
            'orders' => $this->ordersQuery($request->user()->id)->latest()->paginate(10),
        ]);
    }

    public function addresses(Request $request): View
    {
        return view('account.addresses', [
            'seo' => $this->seo('My Address'),
            'user' => $request->user(),
        ]);
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

    private function ordersQuery(int $userId)
    {
        return Order::with('items.product')
            ->where('user_id', $userId);
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
