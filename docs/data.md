# Databronnen

Waar de vaste gegevens van Schoolladder vandaan komen, en hoe je ze opnieuw ophaalt.
Elke dataset krijgt hier zijn eigen kopje, zodat je later niet hoeft te raden waar een
tabel vandaan kwam of welke versie erin zit.

## De database opbouwen

Twee bestanden, in deze volgorde:

```bash
mysql -h 127.0.0.1 -u root schoolladder < database.sql
mysql -h 127.0.0.1 -u root schoolladder < database-subjects.sql
```

`database.sql` is het hele schema plus de vaste lijsten die niet van een school afhangen:
puntencategorieën, zones en badges. `database-subjects.sql` zet de 356 officiële
schoolvakken erin.

**Waarom twee bestanden?** Omdat ze een andere levensloop hebben. Het schema verandert
wanneer jij iets bouwt; de vakkenlijst verandert wanneer Edustandaard een nieuw schooljaar
vaststelt. Zou de vakkendata in `database.sql` staan, dan gooit elke schema-export die
weer weg. Andersom net zo.

### Een tabel toevoegen

`database.sql` houdt de phpMyAdmin-indeling aan: eerst alle `CREATE TABLE` op
alfabetische volgorde, dan de vaste lijsten, en daarna drie blokken met de sleutels, de
`AUTO_INCREMENT` en de foreign keys. Een tabel toevoegen betekent dus op vier plekken
iets bijzetten, niet alleen bovenaan.

Exporteer je liever opnieuw uit phpMyAdmin, dan blijft die indeling vanzelf staan — maar
let op dat je de vakken er niet bij exporteert, want die horen in `database-subjects.sql`.

Controleer daarna of het bestand een lege database nog echt opbouwt:

```bash
mysql -h 127.0.0.1 -u root -e "DROP DATABASE schoolladder; CREATE DATABASE schoolladder CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -h 127.0.0.1 -u root schoolladder < database.sql
mysql -h 127.0.0.1 -u root schoolladder < database-subjects.sql
```

## Vakken

De complete officiële lijst van Nederlandse schoolvakken zit in de database.

|             |                                                                |
| ----------- | -------------------------------------------------------------- |
| Bron        | Edustandaard, waardenlijst **Vakken en leergebieden po en vo** |
| Versie      | schooljaar 2026-2027, v1.0                                     |
| Opgehaald   | 22 september 2026                                              |
| Tabellen    | `subjects` (356 rijen), `subject_levels` (864 rijen)           |
| Seedbestand | `database-subjects.sql`                                        |

De lijst wordt beheerd door een groep met VO-raad, PO-Raad, OCW, Inspectie, SLO en DUO,
en wordt per schooljaar opnieuw vastgesteld. Er is **geen API**: het is een xlsx-download.
Daarom is hij één keer omgezet naar SQL en zit hij nu gewoon in het project.

### Wat erin zit

`subjects` heeft per vak de naam, de afkorting, de Edu-V-prefix, het leergebied, het type
vak (profielvak, beroepsgericht keuzevak, …), de roepnaam en de wettelijke vaknaam.

`subject_levels` zegt op welk niveau een vak bestaat en met welke examencode: `bb`, `kb`,
`gl`, `tl`, `havo` of `vwo`, met het examenjaar vanaf en tot en met en het bezemklasjaar.
Dát is de tabel die de vraag "mag een havoleerling dit vak kiezen" beantwoordt — staat er
geen havo-regel bij, dan wordt het vak daar niet aangeboden.

### Drie dingen om te weten

- **Vaknaam en prefix zijn geen sleutel.** De officiële lijst bevat dubbelen: `it` is
  zowel Italiaans als informatietechnologie, en een paar vmbo-prefixes dragen twee namen.
  Koppel dus altijd op `subjects.id` en niet op naam of prefix.
- **De onderste drie regels van het werkblad zijn een kleurenlegenda**, geen vakken. Eén
  ervan loopt door de codekolommen heen en leverde zes vakcodes met de tekst "vmbo" op.
  Die zijn eruit gefilterd op een leeg leergebied; vandaar 359 regels in de bron en 356
  vakken in de database.
- **`subjects.name` was `varchar(30)`.** De langste officiële vaknaam is 107 tekens, dus
  de kolom is verbreed naar 150. Zonder dat werd het merendeel stilletjes afgekapt.
- **Acht examencodes zijn aangevuld met een voorloopnul.** In het werkblad staan de codes
  van bedrijfseconomie, haven en vervoer, en rijn, binnen- en kustvaart als getal in
  plaats van als tekst, waardoor hun nul verdwenen is: 400, 691 en 692. Alle 856 andere
  codes zijn vier tekens lang, dus die drie staan hier als 0400, 0691 en 0692. Dit is de
  enige plek waar de database bewust afwijkt van de bron.

### Een nieuw schooljaar

`database-subjects.sql` is een momentopname van 2026-2027 en is herhaalbaar te
importeren: hij leegt eerst wat hij de vorige keer schreef en laat het schema de tweede
keer met rust. Stelt Edustandaard een nieuwe lijst vast, dan moet de xlsx opnieuw worden
omgezet. Het omzetscript is er niet meer — voor dit project is die lijst klaar — dus dat
is dan handwerk of een nieuw scriptje. De kolomindeling van het werkblad staat hieronder,
zodat je niet opnieuw hoeft uit te zoeken wat waar staat:

| Kolom | Wat |
| --- | --- |
| A | Leergebied (leeg = legendarij, overslaan) |
| B | Vaknaam |
| C | Prefix |
| E | Type vak |
| F | Wettelijke vaknaam |
| G | Roepnaam |
| H | Afkorting |
| I, J, K, L | vakcode bb, kb, gl, tl — met M, N, O als examenjaar vanaf, t/m en bezemklas |
| P | vakcode havo — met Q, R, S |
| T | vakcode vwo — met U, V, W |

De jaarkolommen bevatten ook losse kopteksten als "vmbo" en "havo"; alleen waarden van
vier cijfers zijn een jaartal.

### Links

- [Waardenlijsten RIO-vo, schooljaar 2026-2027](https://www.edustandaard.nl/standaard_afspraken/waardelijsten-rio-vo/waardenlijsten-rio-vo-schooljaar-2026-2027/) — hier staat de download
- [Waardenlijst Vakken en leergebieden po en vo 2026-2027 v1.0 (xlsx)](https://www.edustandaard.nl/app/uploads/2026/07/Waardenlijst-Vakken-en-leergebieden-po-en-vo-schooljaar-2026-2027-v1.0.xlsx)
- [Toelichting bij die lijst (pdf)](https://www.edustandaard.nl/app/uploads/2026/07/Toelichting-vakken-en-leergebieden-po-en-vo-schooljaar-2026-2027-v1.0.pdf)
- [Beheergroep waardenlijsten RIO](https://www.edustandaard.nl/standaard_werkgroepen/beheergroep-waardenlijsten-rio/) — wie de lijst vaststelt
- [OnderwijsBegrippenKader](https://vocabulairebank.edustandaard.nl/) — bredere begrippen, leerdoelen en curriculum
- [DUO Open Onderwijsdata: examens vmbo, havo en vwo](https://duo.nl/open_onderwijsdata/voortgezet-onderwijs/examens/examens-vmbo-havo-vwo.jsp) — alternatieve bron, csv per schooljaar

## Schoolgegevens

Hiervoor bestaat geen bron. Elke school vult dit zelf, en zolang het leeg is toont het
dashboard netjes zijn lege toestand.

| Tabel | Wat erin hoort |
| --- | --- |
| `classes` | de klassen, met het niveau dat ze volgen |
| `students` | welke gebruiker in welke klas zit |
| `student_subjects` | welke vakken een leerling volgt |
| `periods` | de blokken van een schooljaar, met begin- en einddatum |
| `events` | het rooster: vak, docent, lokaal, tijd |
| `grades` | cijfers, eventueel gekoppeld aan een blok |

`student_subjects` en `periods` zijn er omdat je er anders omheen moet werken.
Zonder het eerste leid je iemands vakken af uit het rooster van zijn klas, en dat klopt
niet zodra de een Duits doet waar de ander Frans doet. Zonder het tweede is "dit blok" op
het dashboard een woord zonder betekenis.
