<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        Message::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Your message has been sent successfully.']);
        }

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
