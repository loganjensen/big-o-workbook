<?php

namespace App\Http\Controllers\BigO;

class ShowONController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-n';
    }
}
