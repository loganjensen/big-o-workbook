<?php

namespace App\Http\Controllers\BigO;

class ShowO1Controller extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-1';
    }
}
