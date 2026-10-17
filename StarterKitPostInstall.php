<?php

class StarterKitPostInstall
{
    public function handle($console)
    {
        $this->addDemoPageToTrees($console);

        $console->newLine();
        $console->info('Systamic is geïnstalleerd.');
        $console->line('Volgende stappen:');
        $console->line('  1. Pas de design tokens aan in resources/css/site.css (@theme).');
        $console->line('  2. php please make:user   (account voor het control panel)');
        $console->line('  3. npm install && composer dev');
        $console->line('  4. Bekijk /styleguide (alleen lokaal) voor alle componenten.');
    }

    /**
     * De demopagina zit in een optionele module, dus de core-trees kennen hem niet.
     * Als de module is geïnstalleerd, hangen we hem hier in de paginaboom en het hoofdmenu.
     */
    protected function addDemoPageToTrees($console): void
    {
        if (! file_exists(base_path('content/collections/pages/blokken.md'))) {
            return;
        }

        $this->appendToTree(base_path('content/trees/collections/pages.yaml'), "  -\n    entry: blokken\n");
        $this->appendToTree(base_path('content/trees/navigation/main.yaml'), "  -\n    id: nav-blokken\n    entry: blokken\n");

        $console->line('Demopagina toegevoegd op /blokken.');
    }

    protected function appendToTree(string $path, string $item): void
    {
        if (! file_exists($path) || str_contains(file_get_contents($path), 'entry: blokken')) {
            return;
        }

        file_put_contents($path, rtrim(file_get_contents($path))."\n".$item);
    }
}
