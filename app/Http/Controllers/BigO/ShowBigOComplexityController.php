<?php

namespace App\Http\Controllers\BigO;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

abstract class ShowBigOComplexityController extends Controller
{
    /**
     * Get the slug for this complexity.
     */
    abstract protected function getSlug(): string;

    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        $slug = $this->getSlug();
        $data = $this->loadComplexityData($slug);

        return Inertia::render('BigO/Show', [
            'complexity' => $data,
            'slug' => $slug,
            'allComplexities' => config('big-o.complexities'),
        ]);
    }

    /**
     * Load complexity data from JSON file.
     */
    protected function loadComplexityData(string $slug): array
    {
        $path = resource_path("data/big-o/{$slug}.json");

        if (! file_exists($path)) {
            abort(404, 'Big-O complexity page not found.');
        }

        $json = file_get_contents($path);

        return json_decode($json, true);
    }
}
