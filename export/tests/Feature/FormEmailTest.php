<?php

namespace Tests\Feature;

use Statamic\Facades\Antlers;
use Statamic\Facades\Form;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
use Statamic\Forms\Email;
use Tests\TestCase;

/**
 * De formuliermail moet naar het e-mailadres uit Bedrijfsgegevens. Statamic legt de velden van de
 * inzending óver de globals heen: een veld met dezelfde handle als een global (zoals `company`)
 * maakt `company:email` leeg, en dan gaat de mail naar MAIL_FROM_ADDRESS. Zie docs/mail.md.
 */
class FormEmailTest extends TestCase
{
    public function test_geen_formulierveld_heet_als_een_global(): void
    {
        $globals = GlobalSet::all()->map->handle()->all();

        foreach (Form::all() as $form) {
            $fields = $form->blueprint()->fields()->all()->keys()->all();

            $this->assertSame([], array_values(array_intersect($fields, $globals)), "Formulier {$form->handle()} heeft een veld met de naam van een global.");
        }
    }

    public function test_formuliermail_gaat_naar_het_adres_uit_bedrijfsgegevens(): void
    {
        $expected = GlobalSet::findByHandle('company')->inDefaultSite()->get('email');
        $this->assertNotEmpty($expected);

        foreach (Form::all() as $form) {
            $data = $form->blueprint()->fields()->all()->map(fn () => 'test')->all();
            $data['email'] = 'bezoeker@example.com';

            foreach ($form->email() as $config) {
                $email = new Email($form->makeSubmission()->data($data), $config, Site::default());
                $email->build();

                $this->assertSame($expected, $email->to[0]['address'] ?? null, "Formulier {$form->handle()} mailt naar het verkeerde adres.");
                $this->assertSame('bezoeker@example.com', $email->replyTo[0]['address'] ?? null);
            }
        }
    }

    /** Keuzevelden (checkboxes, select) komen als lijst van value/label binnen; de mail toont de labels. */
    public function test_keuzes_staan_met_hun_label_in_de_mail(): void
    {
        $html = (string) Antlers::parse(file_get_contents(resource_path('views/emails/form_submission.antlers.html')), [
            'fields' => [[
                'display' => 'Diensten',
                'fieldtype' => 'checkboxes',
                'value' => [['value' => 'a', 'label' => 'Alarmsystemen'], ['value' => 'b', 'label' => 'Camerabewaking']],
            ]],
        ]);

        $this->assertStringContainsString('Alarmsystemen, Camerabewaking', $html);
        $this->assertStringNotContainsString('Array', $html);
    }
}
