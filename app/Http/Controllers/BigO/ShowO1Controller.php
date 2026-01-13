<?php

namespace App\Http\Controllers\BigO;

class ShowO1Controller extends ShowBigOComplexityController
{
    /**
     * Provide the slug identifying the O(1) complexity page.
     *
     * @return string The slug "o-1".
     */
    protected function getSlug(): string
    {
        return 'o-1';
    }
}
