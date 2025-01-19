<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;


class OptimizeClearController extends Controller
{
    public function optimizeClear(Request $request)
    {
        try {
            // Execute Artisan commands
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('optimize:clear');

            return response()->json([
                'status' => true,
                'message' => 'Caches and configuration cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to clear caches and configuration: ' . $e->getMessage()
            ], 500);
        }
    }
}
