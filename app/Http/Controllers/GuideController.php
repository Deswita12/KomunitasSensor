<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\GuideStep;

class GuideController extends Controller
{
    public function index()
    {
        $steps = GuideStep::with('toolItems')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $faqs = Faq::where('is_active', true)->orderBy('order')->get();

        return view('panduan', compact('steps', 'faqs'));
    }
}