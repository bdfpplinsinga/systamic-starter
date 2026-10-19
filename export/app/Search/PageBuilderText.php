<?php

namespace App\Search;

/**
 * Maakt van een page builder (replicator) platte tekst voor de zoekindex.
 * Pakt alleen tekstvelden, zodat instellingen als achtergrond of anker niet doorzocht worden.
 */
class PageBuilderText
{
    protected array $keys = ['eyebrow', 'heading', 'text', 'title', 'description', 'question', 'answer', 'quote', 'name', 'role', 'label', 'value', 'features', 'tab'];

    public function handle($value, $field, $searchable)
    {
        if (! is_array($value)) {
            return null;
        }

        $words = [];

        array_walk_recursive($value, function ($item, $key) use (&$words) {
            if (is_string($item) && in_array($key, $this->keys, true)) {
                $words[] = strip_tags($item);
            }
        });

        return trim(preg_replace('/\s+/', ' ', implode(' ', $words)));
    }
}
