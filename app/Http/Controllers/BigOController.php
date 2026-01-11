<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class BigOController extends Controller
{
    /**
     * Display the Big-O overview page with all complexity types.
     */
    public function index(): Response
    {
        return Inertia::render('BigO/Index', [
            'complexities' => config('big-o.complexities'),
        ]);
    }
}
