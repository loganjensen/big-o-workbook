<?php

namespace App\Http\Controllers\BigO;

class ShowONLogNController extends ShowBigOComplexityController
{
    protected function getSlug(): string
    {
        return 'o-n-log-n';
    }
}
