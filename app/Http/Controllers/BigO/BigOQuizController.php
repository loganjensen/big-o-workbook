<?php

namespace App\Http\Controllers\BigO;

use App\Http\Controllers\Controller;
use App\Services\BigOQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class BigOQuizController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private BigOQuizService $quizService
    ) {}

    /**
     * Get cached quiz or generate a new one for the given slug.
     *
     * @param  string  $slug  The Big-O complexity slug
     */
    public function show(string $slug): JsonResponse
    {
        if (! $this->isValidSlug($slug)) {
            return response()->json([
                'error' => 'Invalid Big-O complexity slug',
            ], 404);
        }

        try {
            $quiz = $this->quizService->getCachedOrGenerate($slug);

            return response()->json($quiz);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Regenerate quiz for the given slug (clears cache and generates new).
     *
     * @param  string  $slug  The Big-O complexity slug
     */
    public function regenerate(string $slug): JsonResponse
    {
        if (! $this->isValidSlug($slug)) {
            return response()->json([
                'error' => 'Invalid Big-O complexity slug',
            ], 404);
        }

        try {
            $quiz = $this->quizService->regenerate($slug);

            return response()->json($quiz);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if the given slug is valid.
     *
     * @param  string  $slug  The slug to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidSlug(string $slug): bool
    {
        $validSlugs = Collection::make(config('big-o.complexities'))
            ->pluck('slug')
            ->toArray();

        return in_array($slug, $validSlugs);
    }
}
