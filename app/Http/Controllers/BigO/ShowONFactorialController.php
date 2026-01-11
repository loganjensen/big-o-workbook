<?php

namespace App\Http\Controllers\BigO;

class ShowONFactorialController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-n-factorial';
    }
}
