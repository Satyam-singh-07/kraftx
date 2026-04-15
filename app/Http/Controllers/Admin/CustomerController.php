<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customers = User::where('role', 'customer')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'status' => 'boolean',
            'address' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'customer';
        
        User::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(User $customer)
    {
        if ($customer->role !== 'customer') abort(404);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        if ($customer->role !== 'customer') abort(404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => 'boolean',
            'address' => 'nullable|string',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') abort(404);
        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }
}
