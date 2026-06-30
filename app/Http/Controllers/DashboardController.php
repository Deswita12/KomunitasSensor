<?php

namespace App\Http\Controllers;

use App\Models\SensorDevice;

class DashboardController extends Controller
{
    public function index()
    {
        $deviceIds = SensorDevice::activeDeviceIds();

        return view('data', compact('deviceIds'));
    }
    // DashboardController.php
    public function dashboard()
    {
        return view('data.dashboard');
    }

    public function info()
    {
        return view('data.info');
    }
}