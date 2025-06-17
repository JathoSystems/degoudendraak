# Introductie
Hoi wij hebben de Gouden Draak gemaakt in Laravel 12. Weekend.

Copyright 2025

# Cron setup voor automatische dagelijkse verkooprapporten

## Overzicht
Dit document beschrijft hoe je de automatische dagelijkse verkooprapporten instelt voor De Gouden Draak Laravel applicatie.

## Laravel Scheduler Configuratie

De Laravel scheduler is al geconfigureerd in `bootstrap/app.php`:

```php
->withSchedule(function ($schedule) {
    // Generate daily sales report every day at 6:00 AM
    $schedule->command('sales:daily-report')->dailyAt('06:00');
})
```

## Cron Setup op de Server

Om de Laravel scheduler te activeren, moet je één cron entry toevoegen aan je server:

### 1. Crontab bewerken
```bash
crontab -e
```

### 2. Voeg deze regel toe:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

**Vervang `/path/to/your/project` met het absolute pad naar je Laravel project.**

Bijvoorbeeld:
```bash
* * * * * cd /var/www/html/degoudendraak && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Voor ontwikkeling (lokaal)
Voor lokale ontwikkeling kun je de Laravel scheduler handmatig starten:
```bash
php artisan schedule:work
```

## Functies van het Systeem

### Automatische Rapportgeneratie
- **Tijd**: Elke dag om 06:00
- **Datum**: Rapport wordt gegenereerd voor de vorige dag
- **Locatie**: Excel bestanden worden opgeslagen in `storage/app/public/daily-reports/`
- **Database**: Rapportgegevens worden opgeslagen in `daily_sales_reports` tabel
- **Email**: Automatische verzending naar alle admin gebruikers

### Email Notificaties
- **Automatisch**: Elke dag na rapportgeneratie
- **Ontvangers**: Alle gebruikers met `is_admin = 1`
- **Bijlage**: Excel rapport wordt meegestuurd
- **Content**: HTML email met samenvatting en top 10 producten
- **Queue**: Emails worden via jobs verwerkt voor betere performance

### Handmatige Rapportgeneratie
Admins kunnen ook handmatig rapporten genereren via:
- Web interface: `/reports/daily-sales`
- Command line: `php artisan sales:daily-report [datum]`
- Command zonder email: `php artisan sales:daily-report [datum] --no-email`

### Excel Rapport Inhoud
Elk rapport bevat:
- **Samenvatting**: Totale omzet, aantal bestellingen, BTW berekeningen
- **Product overzicht**: Per menu item met verkochte hoeveelheden en omzet
- **Formattering**: Professionele styling met kleuren en borders

### Toegang tot Rapporten
- **Wie**: Alleen admins (users met `is_admin = 1`)
- **Waar**: Dashboard → "Dagelijkse Rapporten" of direct via `/reports/daily-sales`
- **Functies**: Bekijken, downloaden van Excel bestanden, email versturen

## Email Configuratie

### Laravel Mail Setup
Configureer email instellingen in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="De Gouden Draak"
```

### Queue Configuratie
Voor email verzending via queues:

```env
QUEUE_CONNECTION=database
```

Run queue migrations en worker:
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

### Email Testing
Test email functionaliteit:
```bash
# Test manual email sending
php artisan sales:daily-report 2025-06-17

# Test without email
php artisan sales:daily-report 2025-06-17 --no-email
```

## Troubleshooting

### Scheduler werkt niet
1. Controleer cron configuratie: `crontab -l`
2. Controleer Laravel logs: `storage/logs/laravel.log`
3. Test handmatig: `php artisan schedule:run`

### Rapporten worden niet gegenereerd
1. Controleer of er sales data is voor de datum
2. Controleer storage permissies: `storage/app/public/daily-reports/`
3. Test command handmatig: `php artisan sales:daily-report`

### Excel bestanden kunnen niet worden gedownload
1. Controleer storage link: `php artisan storage:link`
2. Controleer bestandspermissies in `storage/app/public/`

### Emails worden niet verzonden
1. Controleer email configuratie in `.env`
2. Controleer queue worker: `php artisan queue:work`
3. Test email instellingen: `php artisan tinker` → `Mail::raw('test', function($msg) { $msg->to('test@example.com'); });`
4. Controleer admin users: `User::where('is_admin', true)->get()`

## Bestandsstructuur

```
storage/
├── app/
│   └── public/
│       └── daily-reports/
│           ├── daily-sales-report-2025-06-17.xlsx
│           ├── daily-sales-report-2025-06-18.xlsx
│           └── ...
└── logs/
    └── laravel.log
```

## Command Usage

```bash
# Genereer rapport voor gisteren (standaard)
php artisan sales:daily-report

# Genereer rapport voor specifieke datum
php artisan sales:daily-report 2025-06-17

# Bekijk help
php artisan sales:daily-report --help
```

## Automatisering Alternatieven

### 1. Email Verzending (Geïmplementeerd)
Het systeem verstuurt automatisch emails naar alle admin gebruikers met het Excel rapport als bijlage.

### 2. Database Cleanup (Optioneel)
Je kunt het systeem uitbreiden om oude rapporten automatisch te verwijderen na een bepaalde periode:

```php
// In bootstrap/app.php scheduler
$schedule->command('reports:cleanup')->monthly();
```

### 3. Backup Strategie
Zorg voor regelmatige backups van de `storage/app/public/daily-reports/` directory.

# Dagelijkse Verkooprapporten - De Gouden Draak

## Overzicht
Dit systeem genereert automatisch dagelijkse verkooprapporten in Excel formaat voor De Gouden Draak restaurant. De rapporten bevatten gedetailleerde verkoop statistieken en worden automatisch gegenereerd en per email verzonden naar admin gebruikers.

## ✨ Hoofdfuncties

### 🤖 Automatische Rapportgeneratie
- **Scheduling**: Elke dag om 06:00 automatisch
- **Datum**: Rapport voor de vorige dag
- **Formaat**: Excel (.xlsx) met professionele opmaak
- **Opslag**: `storage/app/public/daily-reports/`

### 📧 Email Notificaties
- **Automatisch**: Na elke rapportgeneratie
- **Ontvangers**: Alle admin gebruikers
- **Bijlage**: Excel rapport
- **Content**: HTML email met samenvatting en top 10 producten
- **Queue systeem**: Achtergrond verwerking voor betere performance

### 🎯 Rapport Inhoud
- **Verkoop Samenvatting**:
    - Totale omzet (incl. en excl. BTW)
    - Aantal bestellingen
    - Gemiddelde bestelling
    - BTW berekening (6%)

- **Product Analyse**:
    - Verkoop per menu item
    - Hoeveelheden en omzet per product
    - Categorie indeling
    - Gedetailleerde pricing

## 🚀 Installatie & Setup

### 1. Dependencies
```bash
composer require phpoffice/phpspreadsheet
```

### 2. Database Migraties
```bash
php artisan migrate
```

### 3. Storage Setup
```bash
php artisan storage:link
```

### 4. Queue Setup (voor emails)
```bash
php artisan queue:table
php artisan migrate
```

### 5. Cron Configuratie
Voeg toe aan crontab:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 6. Email Configuratie
Configureer `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="De Gouden Draak"

QUEUE_CONNECTION=database
```

## 🖥️ Gebruikersinterface

### Admin Dashboard
- **Locatie**: `/reports/daily-sales`
- **Toegang**: Alleen admins (`is_admin = 1`)
- **Functies**:
    - Bekijk alle rapporten
    - Download Excel bestanden
    - Verstuur handmatige emails
    - Genereer rapporten voor specifieke data

### Rapportoverzicht
- **Paginatie**: 20 rapporten per pagina
- **Sortering**: Nieuwste eerst
- **Status indicators**: Bestand beschikbaar/ontbreekt
- **Acties**: Bekijken, Downloaden, Email versturen

### Gedetailleerde Rapportweergave
- **Samenvatting cards**: Omzet, bestellingen, gemiddelden
- **BTW breakdown**: Detailed tax calculations
- **Product tabel**: Alle verkochte items met statistieken
- **Download/Email knoppen**: Direct toegang tot acties

## 🔧 Command Line Tools

### Rapport Genereren
```bash
# Voor gisteren (standaard)
php artisan sales:daily-report

# Voor specifieke datum
php artisan sales:daily-report 2025-06-17

# Zonder email notificaties
php artisan sales:daily-report 2025-06-17 --no-email
```

### Rapporten Bekijken
```bash
# Laatste 10 rapporten
php artisan sales:list-reports

# Laatste 5 rapporten
php artisan sales:list-reports --limit=5
```

### Queue Worker (voor emails)
```bash
php artisan queue:work
```

## 📊 Excel Rapport Details

### Structuur
1. **Header**: Restaurant naam, datum, generatie tijd
2. **Samenvatting**: Totalen, BTW berekening
3. **Product tabel**: Gedetailleerde verkoop per item

### Styling
- **Kleuren**: Blauwe headers, alternerende rijen
- **Borders**: Professionele tabelstructuur
- **Auto-sizing**: Automatische kolom breedte
- **Formatting**: Nederlandse nummer/datum formats

### Data
- **Menu nummers**: Inclusief toevoegingen
- **Categorieën**: Gerecht types
- **Prijzen**: Per stuk en totaal
- **Hoeveelheden**: Verkochte aantallen

## 📧 Email Template

### HTML Design
- **Responsive**: Mobile-friendly layout
- **Branding**: Restaurant kleuren en logo
- **Cards**: Visual summary blocks
- **Tabel**: Top 10 verkochte producten
- **Professional**: Clean, modern design

### Content
- **Samenvatting**: Key metrics in visual cards
- **Top Products**: Best performing items
- **Links**: Direct links to admin dashboard
- **Bijlage**: Excel rapport attached

## 🔐 Beveiliging & Toegang

### Admin Only
- **Middleware**: `AdminMiddleware` op alle routes
- **Database check**: `is_admin = 1` vereist
- **Email recipients**: Alleen admin users

### File Security
- **Storage**: Beveiligde storage directory
- **Access**: Via Laravel storage systeem
- **Permissions**: Correct file permissions

## 🛠️ Maintenance & Monitoring

### Logs
- **Laravel logs**: `storage/logs/laravel.log`
- **Email status**: Queue job status
- **Generation logs**: Command output

### Health Checks
```bash
# Test rapport generatie
php artisan sales:daily-report --no-email

# Check scheduled tasks
php artisan schedule:list

# Check queue status
php artisan queue:monitor
```

### File Management
- **Cleanup**: Consider automatic old file removal
- **Backup**: Regular backup of reports directory
- **Storage**: Monitor disk space usage

## 🎨 Customization

### Email Template
Bewerk: `resources/views/emails/daily-sales-report.blade.php`

### Excel Styling
Wijzig: `app/Console/Commands/GenerateDailySalesReport.php`

### Scheduler Timing
Wijzig: `bootstrap/app.php` - `withSchedule` sectie

### Report Content
Pas aan: SQL queries en data aggregation

## 📈 Performance

### Queue System
- **Background processing**: Email verzending
- **Non-blocking**: Rapport generatie blokkeert niet
- **Retry logic**: Automatic retry voor gefaalde jobs

### Caching
- **File storage**: Efficient file access
- **Database**: Optimized queries met eager loading
- **Memory**: Spreadsheet generation optimization

## 🚨 Troubleshooting

### Scheduler werkt niet
1. Check cron: `crontab -l`
2. Test manual: `php artisan schedule:run`
3. Check logs: `storage/logs/laravel.log`

### Emails niet verzonden
1. Check config: `.env` mail settings
2. Start queue: `php artisan queue:work`
3. Test email: Manual test via tinker
4. Check admins: `User::where('is_admin', true)->get()`

### Bestanden niet toegankelijk
1. Storage link: `php artisan storage:link`
2. Permissions: Check `storage/` directory
3. Path issues: Verify file paths

### Geen data in rapporten
1. Check sales data: Verify sales table
2. Date range: Confirm date parameters
3. Database connection: Test DB access

## 📝 Development Notes

### Architecture
- **Command pattern**: Console commands voor scheduling
- **Job queue**: Asynchrone email verwerking
- **Repository pattern**: Data access via models
- **Service layer**: Business logic separation

### Dependencies
- **PhpSpreadsheet**: Excel generation
- **Laravel Mail**: Email system
- **Carbon**: Date handling
- **Queue system**: Background processing

### Testing
- **Unit tests**: Command testing
- **Feature tests**: HTTP endpoint testing
- **Manual testing**: CLI command verification

---

## 📞 Support

Voor vragen of problemen:
1. Check deze documentatie
2. Bekijk Laravel logs
3. Test commands handmatig
4. Controleer email configuratie

**Gemaakt voor De Gouden Draak Restaurant Management System**
