<?php

namespace App\Http\Controllers;

use App\Mail\HelpRequestSubmitted;
use App\Models\HelpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HelpRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $helpRequest = HelpRequest::create($validated + ['status' => 'new']);

        Mail::to(config('mail.help_notification_email'))
            ->send(new HelpRequestSubmitted($helpRequest));

        return response()->json(['success' => true]);
    }
}