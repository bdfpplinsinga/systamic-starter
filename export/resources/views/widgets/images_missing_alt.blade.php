<ui-widget title="Afbeeldingen zonder alt-tekst" icon="alt" href="{{ $browseUrl }}">
    @if ($total === 0)
        <div class="flex grow flex-col items-center justify-center gap-2 px-4 py-6 text-center text-sm text-gray-600 dark:text-gray-400">
            <p>Alle afbeeldingen hebben een alt-tekst.</p>
        </div>
    @else
        <p class="px-4.5 pt-3 text-sm text-gray-600 dark:text-gray-400">
            {{ $total === 1 ? '1 afbeelding mist' : $total.' afbeeldingen missen' }} een alt-tekst. Schermlezers en zoekmachines kunnen ze dan niet beschrijven.
        </p>
        <ul class="divide-y divide-gray-200 px-4.5 py-2 dark:divide-gray-700">
            @foreach ($assets as $asset)
                <li>
                    <a href="{{ $asset->editUrl() }}" class="flex items-center gap-3 py-2 text-sm hover:text-blue-600">
                        <img src="{{ $asset->thumbnailUrl('small') }}" alt="" class="size-10 shrink-0 rounded-sm bg-gray-100 {{ $asset->isSvg() ? 'object-contain p-1' : 'object-cover' }} dark:bg-gray-800" loading="lazy">
                        <span class="truncate">{{ $asset->path() }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @if ($total > $assets->count())
            <p class="px-4.5 pb-3 text-sm text-gray-600 dark:text-gray-400">En nog {{ $total - $assets->count() }} meer.</p>
        @endif
    @endif
</ui-widget>
