<?php

namespace App\Http\Controllers\BigO;

class ShowONSquaredController extends ShowBigOComplexityController
{
    /**
     * Get the slug that identifies the O(n²) complexity page.
     *
     * @return string The slug value `'o-n-squared'`.
     */
    protected function getSlug(): string
    {
        return 'o-n-squared';
    }
}
