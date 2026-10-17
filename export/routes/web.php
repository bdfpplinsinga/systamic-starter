<?php

use Illuminate\Support\Facades\Route;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

// Overzicht van alle componenten, alleen lokaal beschikbaar.
if (app()->isLocal()) {
    Route::statamic('styleguide', 'styleguide', ['title' => 'Styleguide']);
}
