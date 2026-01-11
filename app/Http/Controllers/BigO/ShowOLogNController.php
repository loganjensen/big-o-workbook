<?php

namespace App\Http\Controllers\BigO;

class ShowOLogNController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-log-n';
    }
}
