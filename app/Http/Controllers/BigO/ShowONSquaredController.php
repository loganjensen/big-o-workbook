<?php

namespace App\Http\Controllers\BigO;

class ShowONSquaredController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-n-squared';
    }
}
