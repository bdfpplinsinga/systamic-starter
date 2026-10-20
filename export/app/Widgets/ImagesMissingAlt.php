<?php

namespace App\Widgets;

use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Widgets\Widget;

/**
 * Dashboardwidget met afbeeldingen zonder alt-tekst, zodat redacteuren die kunnen aanvullen.
 * Config in config/statamic/cp.php: container (standaard assets), limit (standaard 5) en exclude (mappen).
 */
class ImagesMissingAlt extends Widget
{
    public function html()
    {
        $container = AssetContainer::find($this->config('container', 'assets'));

        if (! $container) {
            return '';
        }

        // Voorbeeldafbeeldingen van blokken zijn alleen voor het control panel en hebben geen alt nodig.
        $exclude = $this->config('exclude', ['set-previews']);

        $assets = Asset::query()
            ->where('container', $container->handle())
            ->get()
            ->filter(fn ($asset) => $asset->isImage() || $asset->isSvg())
            ->reject(fn ($asset) => collect($exclude)->contains(fn ($folder) => str_starts_with($asset->path(), rtrim($folder, '/').'/')))
            ->filter(fn ($asset) => blank($asset->get('alt')))
            ->sortByDesc(fn ($asset) => $asset->lastModified())
            ->values();

        return view('widgets.images_missing_alt', [
            'total' => $assets->count(),
            'assets' => $assets->take((int) $this->config('limit', 5)),
            'browseUrl' => $container->showUrl(),
        ]);
    }
}
