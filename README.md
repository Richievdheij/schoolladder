# Schoolladder

Schoolladder is een dystopisch onderwijssysteem. Dit is een schoolproject voor CMGT.

## Stack

Alleen PHP, HTML, CSS, JavaScript en MySQL. Geen framework, geen build-stap, geen npm,
geen Composer.

- PHP 8.1 of nieuwer
- MySQL of MariaDB
- Plain CSS met custom properties (de tokens)
- Plain JavaScript, geen jQuery

## Aan de slag

Clone het project in je Herd-map (of gebruik `herd park` op de map erboven) en open
<http://schoolladder.test>. Zonder Herd werkt `php -S localhost:8000` ook, met één
verschil: die server valt voor elke onbekende URL terug op de `index.php` in de root.
Een pagina die nog niet bestaat toont dan het dashboard in plaats van een 404.

### Database

1. Start MySQL in Herd en open phpMyAdmin.
2. Maak een database `schoolladder` aan met collatie `utf8mb4_general_ci`.
3. Controleer de gegevens bovenin `includes/config.php`.

Gebruik de `db()` helper. Die verbindt pas bij de eerste aanroep, dus een pagina zonder
database werkt ook als MySQL niet draait. Waarden uit een formulier of URL gaan **nooit**
in de query zelf, maar altijd in een prepared statement:

```php
$statement = db()->prepare('SELECT * FROM leerlingen WHERE id = ?');
$statement->execute([$_GET['id']]);
```

## Mappenstructuur

```
schoolladder/
├── index.php          de homepage
├── <pagina>/          elke pagina is een map met een index.php erin
├── includes/
│   ├── config.php     instellingen, database, helpers (e, db, icon)
│   ├── data/          vaste lijsten, zonder HTML
│   └── *.php          de herbruikbare stukken opmaak
├── css/
│   ├── tokens.css     kleuren, fonts, maten. Het enige bestand met hexcodes
│   ├── base.css       element-defaults en layouthelpers
│   ├── components.css gedeelde bouwstenen (.icon, .card, .page__head)
│   ├── components/    één bestand per component, gelijk aan de naam in includes/
│   └── pages/         CSS die bij één pagina hoort
├── js/
├── images/icons/      losse SVG-iconen (Lucide, ISC-licentie)
└── pages/template.php kopieer dit voor een nieuwe pagina
```

Twee vuistregels: **een map met `index.php` per pagina**, zodat de URL `/grades/` is en
je geen rewrite-regels nodig hebt. En **data staat in `includes/data/`, opmaak ernaast**,
dus aan het pad zie je waar je naar kijkt.

## Een pagina maken

Kopieer `pages/template.php` naar `<naam>/index.php`. De uitleg staat in dat bestand
zelf; dit zijn de regels eromheen.

- **Eén variabele: `$page`.** Kleine letters, Engels, gelijk aan de mapnaam en aan de
  sleutel in `includes/data/pages.php`. Daar hangt de active state aan én de titel in de
  browsertab, want die pakt het label uit die lijst. Zo staat `Cijfers` met hoofdletter
  op precies één plek.
- **Zet je pagina ook in die lijst**, anders is hij alleen via de URL te bereiken.
- **Tel de `../` in de requires na.** Eén map diep is `/../`, twee mappen `/../../`.
- **De `<h1>` schrijf je zelf**, want die staat niet altijd op dezelfde plek: in
  `.page__head` boven de inhoud, of in een kaart zoals de homepage doet. Hij mag iets
  anders zeggen dan de browsertab.
- Een pagina is een compleet HTML-document. Geen wrapper, geen `$content`: wat je opent,
  sluit je in hetzelfde bestand.

## CSS

| Bestand                  | Waarvoor                                                                           |
| ------------------------ | ---------------------------------------------------------------------------------- |
| `tokens.css`             | Kleuren, fonts, maten. Het enige bestand met hexcodes.                             |
| `base.css`               | Element-defaults en de layouthelpers: `.container`, `.section`, `.stack`, `.grid`. |
| `components.css`         | Gedeelde bouwstenen die meerdere componenten gebruiken: `.icon`, `.card`, `.page__head`. |
| `css/components/<naam>.css` | De stijl van één component. De naam is gelijk aan het PHP-bestand in `includes/`. |
| `css/pages/<pagina>.css` | Alles wat maar op één pagina voorkomt.                                             |

Een component heeft dus twee bestanden met dezelfde naam: `includes/navbar.php` en
`css/components/navbar.css`. Zoek je waar iets gestyled wordt, dan weet je het pad al.
Nieuwe component? Link 'm er ook bij in `includes/header.php` — dat gebeurt niet
vanzelf, want componenten staan op elke pagina en niet op één.

**Je pagina krijgt automatisch z'n eigen CSS.** Zet een bestand in `css/pages/` met
dezelfde naam als je `$page` en het wordt erbij geladen. Geen bestand, geen request — je
hoeft er dus geen lege aan te maken.

### Klassenamen

BEM: `.card` is het blok, `.card__head` een onderdeel, `.card__head--compact` een
variant. Een toestand is een `is-`-klasse die JavaScript omzet, zoals `.is-active`.

**Op je eigen pagina is de paginanaam het blok.** Bouw je de instellingen, dan begint
alles wat je toevoegt met `.settings` en staat het in `css/pages/settings.css`. Zo raak
je nooit per ongeluk een andere pagina. Hergebruik `.card` en `.container` in plaats van
een eigen variant te verzinnen; heb je er iets extra's op nodig, zet je eigen klasse
ernaast: `class="card settings__panel"`. Namen zeggen wát iets is, niet hoe het eruitziet.

### Afspraken

Mobile first. Elke waarde in `tokens.css` is de telefoonwaarde; grotere schermen krijgen
een override in een media query. Schrijf dus alleen `min-width`, en alleen op deze vier
stappen: 480, 640, 900 en 1200.

- Overschrijf tokens alleen in `tokens.css` zelf. Zet je `:root { --gutter: 40px }` in
  `components.css`, dan wint dat van alle breakpoints en is de waarde overal hetzelfde.
- Elke `z-index` komt uit de `--z-*`-tokens. Verzin er geen zelf, en zet er nooit een
  `9999` neer.
- Knoppen en `select` zijn minimaal 44px hoog (`--tap-target`). Kleiner nodig? Zet
  `min-height: 0` op dat ene component.
- `a:hover` staat achter `@media (hover: hover)`, want op touch blijft een hover-state
  plakken na een tik. Je eigen `:hover` in een component werkt gewoon zoals je verwacht.

## Navigatie

Er zijn drie navigaties en ze lezen alle drie dezelfde lijst uit
`includes/data/pages.php`:

| Waar       | Wat het toont                                                                 |
| ---------- | ----------------------------------------------------------------------------- |
| Menupaneel | Alles: profiel, de pagina's, en een Account-blok. Vult het hele scherm.       |
| Bottom nav | De vijf items met `'bottom' => true`. Alleen onder 900px.                     |
| Topbar     | Diezelfde vijf naast het logo. Alleen vanaf 900px, waar de bottom nav weg is. |

Eén regel toevoegen aan die lijst en alle drie weten het. De active state werkt op de
sleutel en niet op de URL, want een URL verandert zodra er een querystring bij komt of
het project verhuist.

Die lijst staat bewust niet in `navbar.php`: drie bestanden lezen hem, dus de bottom nav
zou de hele navbar moeten includen en die dan een tweede keer renderen. Ook bewust geen
JSON (geen commentaar mogelijk, en parsen bij elk verzoek) en geen database (een query
per pagina, en het menu ligt plat als MySQL niet draait). Zodra het menu per rol
verschilt is dát het moment om naar de database te gaan.

### Iconen

Haal ze van [Lucide](https://lucide.dev) en zet ze in `images/icons/` onder hun eigen
Lucide-naam, dus naar wat ze afbeelden en niet naar waar ze toevallig gebruikt worden.
Aanroepen met `<?= icon('trophy') ?>`; dat zet de SVG rechtstreeks in de pagina, zodat
`stroke="currentColor"` meekleurt met de tekst ernaast en met de active state.

Twee dingen moeten in het bestand kloppen: `stroke="currentColor"` en `class="icon"` —
zonder die class wint de `height: auto` uit `base.css` van de afmetingen in de SVG.
Typ je de naam verkeerd, dan staat dat als HTML-commentaar in de broncode zolang `DEBUG`
aan staat.

## Goed om te weten

- **Zonder JavaScript opent het menupaneel niet.** De links staan wel gewoon in de HTML.
  De uitklapsectie erin is een `<details>` en werkt dus wél zonder JS.
- Het paneel vult het hele scherm, dus er is geen overlay nodig. De focus blijft erin
  doordat de rest van de pagina `inert` krijgt.
- **Het logo werkt niet op `theme-light`.** De wordmark is lichtlavendel en valt weg op
  een lichte achtergrond. Ga je dat thema gebruiken, exporteer dan een donkere variant.
- Er is nog geen login. `includes/data/user.php` is een placeholder die straks de
  gebruiker uit `$_SESSION` moet leveren.
