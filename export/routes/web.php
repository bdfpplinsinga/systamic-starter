<?php

use Illuminate\Support\Facades\Route;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

// robots.txt per omgeving: alleen productie mag geïndexeerd worden.
Route::statamic('robots.txt', 'robots', ['layout' => null, 'content_type' => 'text']);

// SEO Pro heeft een eigen sitemap; zonder SEO Pro een eenvoudige fallback.
if (! class_exists(\Statamic\SeoPro\ServiceProvider::class)) {
    Route::statamic('sitemap.xml', 'sitemap', ['layout' => null, 'content_type' => 'xml']);
}

// Zoekresultaten. Het zoekveld in de header verwijst hierheen.
Route::statamic('zoeken', 'search', ['title' => 'Zoeken']);

// RSS-feed van het nieuws, alleen als de module Nieuws is geïnstalleerd.
if (file_exists(resource_path('views/news/feed.antlers.html'))) {
    Route::statamic('nieuws/feed', 'news/feed', ['layout' => null, 'content_type' => 'rss']);
}

// Overzicht van alle componenten, alleen lokaal beschikbaar.
if (app()->isLocal()) {
    Route::statamic('styleguide', 'styleguide', ['title' => 'Styleguide']);
}
