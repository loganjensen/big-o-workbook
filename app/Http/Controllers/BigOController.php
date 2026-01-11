<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class BigOController extends Controller
{
    /**
     * Display the Big-O overview page with all complexity types.
     *
     * Renders the BigO/Index Inertia component and supplies the configured complexities.
     *
     * @return \Inertia\Response The Inertia response rendering the Big-O overview.
     */
    public function index(): Response
    {
        return Inertia::render('BigO/Index', [
            'complexities' => config('big-o.complexities'),
        ]);
    }
}