<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $newsletters = Newsletter::latest()->paginate(20);
        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return back()->with('success', 'Newsletter subscription deleted successfully.');
    }
}
