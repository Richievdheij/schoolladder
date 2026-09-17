# Schoolladder

Schoolladder is een dystopisch onderwijssysteem.
Dit is een schoolproject voor CMGT.

## Stack

Alleen PHP, HTML, CSS, JavaScript en MySQL. Geen framework, geen build-stap, geen npm, geen
Composer.

- PHP 8.1 of nieuwer
- MySQL of MariaDB
- Plain CSS met custom properties (de tokens)
- Plain JavaScript, geen jQuery

## Aan de slag

Clone het project in je Herd-map (of gebruik `herd park` op de map erboven) en open
<http://schoolladder.test>.

Zonder Herd werkt de ingebouwde server van PHP ook:

```bash
php -S localhost:8000
```

### Database

1. Start MySQL in Herd en open phpMyAdmin.
2. Maak een database `schoolladder` aan met collatie `utf8mb4_general_ci`.
3. Controleer bovenin `includes/config.php` of de gegevens kloppen. De standaard is de
   Herd-standaard: host `127.0.0.1`, poort `3306`, gebruiker `root`, geen wachtwoord.

Gebruik de verbinding met de `db()` helper. Die verbindt pas bij de eerste aanroep, dus een
pagina zonder database werkt ook als MySQL niet draait.

```php
$leerlingen = db()
    ->query('SELECT naam, rang FROM leerlingen ORDER BY rang')
    ->fetchAll();
```

Waarden uit een formulier of URL zet je nooit in de query zelf, maar altijd in een prepared
statement:

```php
$statement = db()->prepare('SELECT * FROM leerlingen WHERE id = ?');
$statement->execute([$_GET['id']]);
$leerling = $statement->fetch();
```

## Mappenstructuur

```
schoolladder/
├── index.php            Homepage
├── includes/
│   ├── config.php       Instellingen, database en de helpers (url, e, db)
│   ├── header.php       <head>, fonts, stylesheets en het begin van de body
│   └── footer.php       Einde van de body en het script
├── css/
│   ├── tokens.css       Kleuren, fonts, maten. Het enige bestand met hexcodes
│   ├── base.css         Reset, basisstijlen voor h1-h6, p, a, plus .container en .grid
│   ├── components.css   Componenten die op meerdere pagina's terugkomen
│   └── pages/           CSS die bij één pagina hoort
├── js/
│   └── main.js          Draait op elke pagina
├── images/              Logo, favicon en ander beeld
└── pages/
    └── template.php     Kopieer dit bestand voor een nieuwe pagina
```
