<?php

namespace App\Http\Controllers\BigO;

class ShowO2NController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-2-n';
    }
}
