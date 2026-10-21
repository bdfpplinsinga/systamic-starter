<?php

class StarterKitPostInstall
{
    /**
     * Pagina's uit optionele modules. De core-trees kennen ze niet, dus als de module
     * is geïnstalleerd hangen we ze hier in de paginaboom en het hoofdmenu.
     */
    protected array $modulePages = [
        'nieuws' => 'Nieuws toegevoegd op /nieuws.',
        'vacatures' => 'Vacatures toegevoegd op /vacatures.',
        'diensten' => 'Diensten toegevoegd op /diensten.',
        'team' => 'Team toegevoegd op /team.',
        'blokken' => 'Demopagina toegevoegd op /blokken.',
    ];

    public function handle($console)
    {
        $this->publishCookieDialogAssets($console);
        $this->removeStaticRobotsTxt();

        foreach ($this->modulePages as $entry => $message) {
            if ($this->addPageToTrees($entry)) {
                $console->line($message);
            }
        }

        $this->buildSearchIndex($console);

        $console->newLine();
        $console->info('Systamic is geïnstalleerd.');
        $console->line('Volgende stappen:');
        $console->line('  1. Pas de design tokens aan in resources/css/site.css (@theme).');
        $console->line('  2. php please make:user   (account voor het control panel)');
        $console->line('  3. npm install && composer dev');
        $console->line('  4. Vul in het control panel de globals in: Bedrijfsgegevens, Social media en Algemeen.');
        $console->line('  5. Zet MAIL_* in .env, zodat het contactformulier e-mail verstuurt.');
        $console->line('  6. Vul de privacyverklaring en algemene voorwaarden in.');
        $console->line('  7. Bekijk /styleguide (alleen lokaal) voor alle componenten.');
    }

    /**
     * Cookie Dialog publiceert zijn frontend-bestanden niet vanzelf. Draait in een apart
     * proces, omdat de addon in dit proces nog niet geladen is. Bestaande content wordt niet overschreven.
     */
    protected function publishCookieDialogAssets($console): void
    {
        $result = \Illuminate\Support\Facades\Process::path(base_path())
            ->run([PHP_BINARY, 'artisan', 'vendor:publish', '--tag=statamic-cookie-dialog']);

        if ($result->failed()) {
            $console->warn('Cookie Dialog assets niet gepubliceerd. Draai: php artisan vendor:publish --tag=statamic-cookie-dialog');
        }
    }

    /**
     * robots.txt komt uit een route (alleen productie mag geïndexeerd worden). Het statische
     * bestand van Laravel zou die route overschaduwen.
     */
    protected function removeStaticRobotsTxt(): void
    {
        if (file_exists($path = public_path('robots.txt'))) {
            unlink($path);
        }
    }

    /**
     * Vult de zoekindex met de meegeleverde content. Daarna houdt Statamic hem zelf bij.
     */
    protected function buildSearchIndex($console): void
    {
        $result = \Illuminate\Support\Facades\Process::path(base_path())
            ->run([PHP_BINARY, 'please', 'search:update', '--all']);

        if ($result->failed()) {
            $console->warn('Zoekindex niet opgebouwd. Draai: php please search:update --all');
        }
    }

    protected function addPageToTrees(string $entry): bool
    {
        if (! file_exists(base_path("content/collections/pages/{$entry}.md"))) {
            return false;
        }

        $this->appendToTree(base_path('content/trees/collections/pages.yaml'), $entry, "  -\n    entry: {$entry}\n");
        $this->appendToTree(base_path('content/trees/navigation/main.yaml'), $entry, "  -\n    id: nav-{$entry}\n    entry: {$entry}\n");

        return true;
    }

    protected function appendToTree(string $path, string $entry, string $item): void
    {
        if (! file_exists($path) || str_contains(file_get_contents($path), "entry: {$entry}\n")) {
            return;
        }

        file_put_contents($path, rtrim(file_get_contents($path))."\n".$item);
    }
}
