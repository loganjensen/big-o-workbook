<?php

namespace App\Http\Controllers\BigO;

class ShowONFactorialController extends ShowBigOComplexityController
{
    /**
     * Return the route slug for the O(n) vs factorial Big-O view.
     *
     * @return string The slug 'o-n-factorial'.
     */
    protected function getSlug(): string
    {
        return 'o-n-factorial';
    }
}
