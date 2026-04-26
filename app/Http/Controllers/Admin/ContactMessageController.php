<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_read']);

        $messages = ContactMessage::query()
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $search = trim($filters['search']);
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_read']) && $filters['is_read'] !== '', function ($query) use ($filters) {
                $query->where('is_read', (bool) $filters['is_read']);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contact-messages.index', compact('messages', 'filters'));
    }

    public function markRead(ContactMessage $message)
    {
        $message->update([
            'is_read' => !$message->is_read,
        ]);

        return back()->with('success', 'Message status updated successfully.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}
