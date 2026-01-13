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
     * Render the Big-O complexity page for the controller's slug.
     *
     * Loads the complexity data for the current slug and returns an Inertia response
     * rendering the 'BigO/Show' view with the following props: `complexity`, `slug`,
     * and `allComplexities`.
     *
     * @return Response An Inertia response that renders the Big-O detail view.
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
     * Load complexity data for the given slug from the resource data/big-o JSON file.
     *
     * @param  string  $slug  Slug identifying the complexity JSON file (filename without the `.json` extension).
     * @return array Associative array decoded from the JSON file.
     */
    protected function loadComplexityData(string $slug): array
    {
        $path = resource_path("data/big-o/{$slug}.json");

        if (! file_exists($path)) {
            abort(404, 'Big-O complexity page not found.');
        }

        $json = file_get_contents($path);

        if ($json === false) {
            abort(500, "Failed to read Big-O complexity data file: {$slug}.json");
        }

        $data = json_decode($json, true);

        if ($data === null || json_last_error() !== JSON_ERROR_NONE) {
            abort(500, 'Failed to parse Big-O complexity data: '.json_last_error_msg());
        }

        return $data;
    }
}
