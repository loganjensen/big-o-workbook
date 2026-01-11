<?php

namespace App\Http\Controllers\BigO;

class ShowONController extends ShowBigOComplexityController
{
    /**
     * Provide the controller's slug identifying the O(n) complexity.
     *
     * @return string The slug "o-n".
     */
    protected function getSlug(): string
    {
        return 'o-n';
    }
}