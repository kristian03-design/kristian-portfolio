<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        if ($request->filled('website')) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Your message has been sent successfully.']);
            }

            return back()->with('success', 'Your message has been sent successfully.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $duplicateKey = 'contact:duplicate:' . hash('sha256', strtolower($validated['email']) . '|' . trim($validated['message']));

        if (! Cache::add($duplicateKey, true, now()->addMinutes(15))) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Your message has already been received.']);
            }

            return back()->with('success', 'Your message has already been received.');
        }

        Message::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Your message has been sent successfully.']);
        }

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
