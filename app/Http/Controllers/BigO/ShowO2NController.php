<?php

namespace App\Http\Controllers\BigO;

class ShowO2NController extends ShowBigOComplexityController
{
    /**
     * Provide the slug used to identify this controller's complexity page.
     *
     * @return string The slug string `'o-2-n'`.
     */
    protected function getSlug(): string
    {
        return 'o-2-n';
    }
}
