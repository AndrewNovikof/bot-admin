<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $bots = $request->user()->bots()->get()->load(['user', 'team']);
        return Inertia::render('Dashboard', [
            'bots' => $bots
        ]);
    }
}
