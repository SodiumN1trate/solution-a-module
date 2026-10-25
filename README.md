# RVT Web izstrādes konkurss 2025 — A moduļa risinājums

Šis repozitorijs satur **Rīgas Valsts tehnikuma 2025. gada Web izstrādes konkursa A moduļa etalonrisinājumu**, kas izstrādāts, izmantojot PHP un Laravel.

Konkursa mērķis bija noskaidrot, kurš Rīgas Valsts tehnikuma audzēknis pārstāvēs tehnikumu **SkillsLatvia 2026** konkursā Web tehnoloģiju prasmju konkursā.

## Mana loma konkursā

2025. gadā piedalījos Rīgas Valsts tehnikuma Web izstrādes konkursā kā **eksperts**.

Mani pienākumi bija:

- **A moduļa (backend)** konkursa uzdevuma izstrāde.
- **B moduļa (frontend)** konkursa uzdevuma izstrāde.
- A moduļa etalonrisinājuma izstrāde, lai konkursanti to varētu izmantot B moduļa izpildē.
- Dalība konkursantu iesniegto darbu vērtēšanā.

## Par projektu

A moduļa uzdevums paredzēja izstrādāt REST API vienkāršās izslēgšanas turnīru (*Single Elimination Bracket*) pārvaldībai.

Šajā repozitorijā ir mans A moduļa etalonrisinājums, kas izstrādāts, balstoties uz konkursa uzdevuma PDF specifikāciju.

API nodrošina:

- Turnīru izveidi un saraksta iegūšanu.
- Komandu reģistrēšanu turnīros.
- Turnīra komandu saraksta iegūšanu.
- Izslēgšanas spēļu pāru ģenerēšanu.
- Spēļu rezultātu ievadi un atjaunināšanu.
- Turnīra izslēgšanas spēļu tabulas iegūšanu.

Risinājums tika sagatavots kā gatavs backend, lai konkursanti B modulī varētu koncentrēties uz frontend izstrādi un integrāciju ar API.

## Izmantotās tehnoloģijas

- PHP
- Laravel 12
- REST API
- Eloquent ORM
- MySQL
- Docker / Laravel Sail

## API galapunkti

| Metode | Galapunkts | Apraksts |
|---|---|---|
| GET | `/api/v1/tournaments` | Iegūt turnīru sarakstu |
| POST | `/api/v1/tournaments` | Izveidot turnīru |
| POST | `/api/v1/tournaments/{id}/teams` | Pievienot komandu turnīram |
| GET | `/api/v1/tournaments/{id}/teams` | Iegūt turnīra komandas |
| POST | `/api/v1/tournaments/{id}/generate-bracket` | Ģenerēt spēļu pārus |
| GET | `/api/v1/tournaments/{id}/bracket` | Iegūt turnīra spēļu tabulu |
| PATCH | `/api/v1/matches/{match_id}` | Atjaunināt spēles rezultātu |

## Izstrādes laiks

**Aptuveni 1,5–2 stundas.**

Etalonrisinājums tika izstrādāts ierobežotā laikā, ievērojot A moduļa uzdevuma prasības un API specifikāciju.

## Konkursa uzdevums

Risinājums izstrādāts, balstoties uz konkursa A moduļa uzdevumu, kurā definēta API funkcionalitāte, pieprasījumu un atbilžu struktūra, validācijas nosacījumi un kļūdu apstrāde.

[Apskatīt A moduļa uzdevumu (PDF)](./Modulis%20A.pdf)
