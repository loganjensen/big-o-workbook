<?php

namespace App\Http\Controllers\BigO;

class ShowONLogNController extends ShowBigOComplexityController
{
    /**
     * Provide the route slug that identifies the O(n log n) complexity view.
     *
     * @return string The slug "o-n-log-n".
     */
    protected function getSlug(): string
    {
        return 'o-n-log-n';
    }
}