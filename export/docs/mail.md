# Mail van de website (formulieren)

De formulieren (zoals `contact`) mailen elke inzending naar de klant: naar het e-mailadres uit **Bedrijfsgegevens** (`company:email`), met de bezoeker als `reply_to`. Er gaat geen mail naar de bezoeker zelf. De mail wordt direct verstuurd (`QUEUE_CONNECTION=sync`), dus er is geen queue-worker nodig.

Aan de code hoeft niets te veranderen. Alleen `.env` op de server bepaalt hoe de mail de deur uit gaat. Staat daar nog `MAIL_MAILER=log`, dan wordt er niets verzonden.

## Aanpak Buro Toast: één Mailgun-account voor alle sites

Omdat de formulieren alleen de klant zelf mailen, hoeft de afzender niet het domein van de klant te zijn. Daarom versturen alle sites via één gratis Mailgun-account van Buro Toast, met één afzenddomein: `mg.burotoast.nl`.

- **Gratis abonnement:** 1 eigen domein en 100 mails per dag. Die 100 geldt voor **alle sites samen**. Voor contactformulieren is dat ruim genoeg; een spamgolf telt wel mee (de honeypot houdt het meeste tegen).
- **Regio EU:** het account staat in de EU-regio (AVG), dus de SMTP-host is `smtp.eu.mailgun.org`.
- **Niet doen:** geen extra gratis accounts aanmaken om de limiet van één domein te omzeilen. Dat is tegen de voorwaarden van Mailgun. Wordt de limiet te krap, stap dan over op een betaald abonnement; aan de sites verandert niets.

### Eenmalig (al gedaan als `mg.burotoast.nl` in Mailgun staat)

1. Voeg in Mailgun het domein `mg.burotoast.nl` toe (een subdomein, zodat de gewone mail op burotoast.nl er geen last van heeft).
2. Zet de DNS-records die Mailgun geeft (SPF, DKIM en eventueel MX/CNAME voor tracking) bij `burotoast.nl` en wacht tot Mailgun het domein als geverifieerd toont.

### Per nieuwe site

1. **SMTP-gebruiker aanmaken.** In Mailgun bij het domein `mg.burotoast.nl` → SMTP credentials: een eigen gebruiker per site, bijvoorbeeld `<klant>@mg.burotoast.nl`. Zo kun je één site intrekken zonder de rest te raken. Bewaar het wachtwoord in de wachtwoordmanager, nooit in git of chat.
2. **`.env` op de server vullen:**

   ```dotenv
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.eu.mailgun.org
   MAIL_PORT=587
   MAIL_USERNAME=<klant>@mg.burotoast.nl
   MAIL_PASSWORD=...
   MAIL_FROM_ADDRESS="noreply@mg.burotoast.nl"
   MAIL_FROM_NAME="Website <Klantnaam>"
   ```

   Daarna `php artisan config:cache` (staat meestal al in het deployscript).
3. **Ontvanger controleren.** In het control panel bij Globals → Bedrijfsgegevens staat het e-mailadres waar de formulieren naartoe gaan. Valt dat leeg, dan gaat de mail naar `MAIL_FROM_ADDRESS`, en dat is niet de bedoeling.
4. **Testen.** Verstuur het contactformulier op de live site. Controleer of de mail binnenkomt (ook in de spammap) en of "beantwoorden" naar het adres van de bezoeker gaat. In Mailgun onder Logs zie je per bericht of het is afgeleverd.
5. **Klant informeren.** De mail komt van "Website <klant>" met `noreply@mg.burotoast.nl` als afzender. Vraag de klant die afzender als veilig te markeren, zodat de mails niet in de spam belanden.

In de DNS van de klant hoeft voor deze aanpak niets te veranderen.

## Valkuil: veldnamen

Statamic legt de velden van een inzending óver de globals heen voordat het `to` invult. Een formulierveld met de handle van een global (`company`, `social`, `general`, `scripts`) maakt `company:email` leeg, en dan gaat de mail naar `MAIL_FROM_ADDRESS` (`noreply@…`) in plaats van naar de klant. Noem een bedrijfsnaamveld dus `company_name`, nooit `company`. `tests/Feature/FormEmailTest.php` bewaakt dit: draai `php artisan test` na het toevoegen van een formulier.

## Wanneer anders

- **De site mailt ook naar bezoekers** (bijvoorbeeld een bevestiging): dan hoort de afzender het domein van de klant te zijn. Gebruik dan een maildienst met dat domein geverifieerd (SPF en DKIM in de DNS van de klant), bijvoorbeeld een betaald Mailgun-abonnement of een account op naam van de klant.
- **De klant wil zelf eigenaar zijn of verstuurt nieuwsbrieven:** een eigen account op naam van de klant.
- **Via de mailbox van de klant (SMTP van de mailprovider):** kan, maar is minder betrouwbaar. Bij Microsoft 365 afraden: inloggen op SMTP met een wachtwoord wordt daar afgebouwd, dus het verzenden kan zomaar stoppen.
