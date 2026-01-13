<?php

namespace App\Http\Controllers\BigO;

class ShowOLogNController extends ShowBigOComplexityController
{
    /**
     * Provide the slug identifying the O(log n) complexity page.
     *
     * @return string The slug "o-log-n".
     */
    protected function getSlug(): string
    {
        return 'o-log-n';
    }
}
