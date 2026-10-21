<?php

namespace App\Scopes;

use Statamic\Query\Scopes\Scope;

/**
 * Alleen vacatures zonder sluitingsdatum of met een sluitingsdatum vanaf vandaag.
 * Gebruik: {{ collection:jobs query_scope="open_jobs" }}
 */
class OpenJobs extends Scope
{
    public function apply($query, $values)
    {
        $query->where(fn ($query) => $query
            ->whereNull('closing_date')
            ->orWhere('closing_date', '>=', now()->startOfDay()));
    }
}
