<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.unique' => 'This email is already subscribed to our newsletter.',
        ]);

        Newsletter::create([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Thank you for subscribing to our newsletter!',
        ]);
    }
}
