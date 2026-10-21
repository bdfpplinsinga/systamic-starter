<?php

namespace App\Search;

/**
 * Filter voor de zoekindex: alleen gepubliceerde entries met een eigen pagina.
 * Collecties zonder route (zoals Team) komen zo niet in de resultaten.
 */
class HasUrl
{
    public function handle($item): bool
    {
        return $item->status() === 'published' && filled($item->url());
    }
}
