# Säterinportti — Ostopolku (mu-plugin)

Mu-plugin Säterinportin ostopolun /liity-sivulle ja kassavirralle.
4-sivun pohja: paketinvalinta → yhteystiedot → maksu → vahvistus.

Versio: 0.6.0 · Tila: prototyyppi, valmis testattavaksi staging-ympäristössä.

---

## 🎮 Klikkaile prototyyppiä selaimessa

**[Avaa interaktiivinen demo →](https://playground.wordpress.net/?blueprint-url=https://cdn.jsdelivr.net/gh/tleskinen/kuntokeskus-sateri@main/_playground/blueprint.json)**

Demo pyörii kokonaan selaimessasi WordPress Playgroundin avulla — ei tarvita
asennusta eikä rekisteröitymistä. Käynnistys kestää noin 10–15 sekuntia,
minkä jälkeen voit selata /liity → /jatka → /maksu → /vahvistus -flow:ta
oikealla WordPress-ympäristöllä. Kaikki muutokset ovat lokaaleja eivätkä
tallennu mihinkään.

---

## Mitä mu-plugin tarjoaa

**4 sivupohjaa, jotka mu-plugin asentaa WordPressiin automaattisesti:**

| Slug | Sivupohja | Sisältö |
|------|-----------|---------|
| `/liity` | `page-liity.php` | Scope-valinta (Sali / Sali + jumpat), 3 commitment-korttia (Etuasiakkuus, Vuosisäästö, Joustojäsenyys), tutustumistarjous, ryhmäliikunta-modaali, FAQ |
| `/jatka` | `page-rekisterointi.php` | Vaihe 1: kirjautumisvalinta + yhteystiedot |
| `/maksu` | `page-maksu.php` | Vaihe 2: upsellit (InBody, PT, Intensed-paita) + maksutapa-valinta |
| `/vahvistus` | `page-vahvistus.php` | Tilaus vahvistettu — PIN-koodi, avainkortin nouto, seuraavat askeleet |

**Datamalli (Säterinportin todelliset hinnat):**

Scope `Kuntosali` × commitment `Etuasiakkuus` = 39 €/kk · 12 kk sopimus, kk-laskutus.
Scope `Kuntosali + jumpat` × commitment `Vuosisäästö` = 550 € (45,83 €/kk) · vuosikortti.
Jne. Hinnat määritelty `class-packages.php`:ssä — voit yliajaa filtterillä `saterinportti_ostopolku_pricing`.

**Tarjous-mekaniikka:**

Tutustumistarjous (esim. "Treenit huhtikuun loppuun 1 €") asetetaan
WP-adminissa (Asetukset → Säterinportti Ostopolku) ja vaikuttaa sopimustyypeissä
joiden ID:t lisätään `applies_to`-listaan. Tarjous näkyy kortilla yliviivattuna
alkuperäisenä hintana + asteriski + alaviite "Jatkuu X €/kk tarjouksen jälkeen".

**Ryhmäliikunta:**

Yhteinen tuntikatalog (12 tuntia mukaan lukien REBELS) renderöidään modaalissa.
Avataan joko *(i)*-info-ikonilla "Sali + jumpat" -valinnan kohdalla TAI alaosan
"Katso tuntivalikoima →" -napilla.

**Risk-reversal kortilla:**

Jokaisen kortin ensimmäisenä feature-rivinä on commitment-tieto:
- Etuasiakkuus → "12 kk sitoumus, jatkuu joustavasti"
- Vuosisäästö → "12 kk, maksu ennakkoon"
- Joustojäsenyys → "Peruutus milloin vain"

Sama fonttipaino muiden featureiden kanssa, ei korostettu — vain järjestys nostaa
sen ensimmäiseksi.

---

## Asennus

WP-installaatioon `wp-content/mu-plugins/`:

```
wp-content/
  mu-plugins/
    saterinportti-ostopolku/              ← tämä kansio kokonaisena
    saterinportti-ostopolku-loader.php    ← yhden rivin loader
```

`saterinportti-ostopolku-loader.php` -sisältö:

```php
<?php
require_once __DIR__ . '/saterinportti-ostopolku/saterinportti-ostopolku.php';
```

(Tämä tarvitaan koska WP ei lataa mu-pluginien alikansioita automaattisesti.)

### Sivujen automaattinen luonti

1. Asennuksen jälkeen mene WP-admin → **Asetukset → Säterinportti Ostopolku**
2. Yläosassa näkyy taulukko 4 sivusta. Puuttuvat näkyvät punaisella
3. Klikkaa **"Luo puuttuvat sivut"** — WordPress luo /liity, /jatka, /maksu ja
   /vahvistus -sivut oikeilla sivupohjilla.

Sivuja voi käydä editoimassa sivustolla normaalin WP-tavalla — sisältö tulee
sivupohjasta, eli editorin sisältö-osio jätetään tyhjäksi.

### Sivujen luonti käsin (vaihtoehto)

Jos haluat hallita slugeja itse:

1. WP-admin → Sivut → Lisää uusi
2. Otsikko: `Liity`, `Yhteystiedot`, `Maksu`, `Tilaus vahvistettu` jne.
3. Vasemmasta oikealla paneelista valitse **Sivupohja**:
   - "Säterinportti — Liity jäseneksi"
   - "Säterinportti — Yhteystiedot"
   - "Säterinportti — Maksu"
   - "Säterinportti — Tilaus vahvistettu"

Voit nimetä slugit miten haluat — sivupohjien tunnistus toimii myös eksplisiittisesti.

---

## Konfigurointi

WP-admin → **Asetukset → Säterinportti Ostopolku**:

**Tarjous:**
- *Aktiivinen* — kytkee yliviivauksen ja asteriskin korteille
- *Otsikko* — näkyy alaviitteessä ("Treenit huhtikuun loppuun")
- *1. kk hinta (€)* — esim. 1 tai 29
- *Koskee sopimustyyppejä* — checkboxit Etuasiakkuus / Vuosisäästö / Joustojäsenyys
- *Avainkortti veloituksetta* — näkyy yhteenvedossa 0 €:nä (norm. 15 €)
- *Tarjouksen kesto (teksti)* — esim. "huhtikuun loppuun" → menee alaviitteeseen

**FitnessBooker / verkkokauppa:**
- *Pohja-URL* — käytetään fiboproduct- ja add-to-cart-linkeissä. Tyhjä → /verkkokauppa/.

---

## Muunneltavissa filterillä

Kaikki data on filtteröitävissä, joten teema voi yliajaa ilman koodimuutosta plugiinissa:

```php
add_filter( 'saterinportti_ostopolku_pricing', function ( $pricing ) {
    $pricing['gym']['etuasiakkuus']['monthly'] = '42'; // päivitä hinta
    return $pricing;
} );
```

Filterit:
- `saterinportti_ostopolku_scopes`        — scope-valinta (Sali / Sali + jumpat)
- `saterinportti_ostopolku_commitments`   — sopimustyypit (Etuasiakkuus, jne.)
- `saterinportti_ostopolku_pricing`       — hinnat per scope × commitment
- `saterinportti_ostopolku_classes`       — ryhmäliikunta-tuntien lista
- `saterinportti_ostopolku_upsells`       — upsell-tuotteet /maksu-sivulla
- `saterinportti_ostopolku_payment_methods` — maksutapa-valinnat
- `saterinportti_ostopolku_fb_base_url`   — pohja-URL ostopolulle

---

## Brändivärin override teemassa

Jos haluat kiinnittää brändivärit teema-CSS:ssä, override muuttujat:

```css
:root {
    --sp-accent: #1f5862;       /* nykyinen petrol */
    --sp-accent-dark: #103e47;
    --sp-accent-soft: #e8f0f1;
    --sp-accent-pill-bg: #c5dde0;
    --sp-accent-pill-ink: #103e47;
}
```

CSS:ää ei ole pakotettu fontille — perii teemasta automaattisesti.

---

## Mitä EI VIELÄ ole

- **WooCommerce-integraatio** — "Maksa"-nappi vie /vahvistus-sivulle suoraan,
  ei käytä WC-kassaa. Tuotanto-asennukseen "Maksa" pitää muuttaa muotoon
  `/checkout/?add-to-cart=<wc_product_id>` ja jokaiseen scope × commitment
  -kombinaatioon mappaa oikea WC product ID.
- **FitnessBooker-API** — uuden asiakkaan luonti hoidetaan WC:n kautta;
  Tooltipin/FB:n integraatio jatkaa nykyistä toimintaansa.
- **Liikuntaedut päästä päähän** — Smartum/Epassi/Edenred näkyvät
  maksutapa-valintana, mutta integraatio nykyiseen toimintamalliin pitää tehdä
  WC-tasolla.
- **Lomakkeiden submit** — /jatka- ja /maksu-sivuilla lomakkeet ovat staattisia
  prototyyppejä. Tuotantoon "Jatka maksuun" pitää tallentaa data joko
  WC-customer-objektiksi tai sessioon.
- **State-säilytys** — käyttäjän valinnat (scope, commitment, upsellit)
  säilytetään sessionStoragessa selaimessa. Toimii sivuvaiheiden yli, mutta
  ei kestä selaimen sulkemista.

---

## Kansiorakenne

```
saterinportti-ostopolku/
├── saterinportti-ostopolku.php       ← mu-plugin entry
├── README.md
├── includes/
│   ├── class-plugin.php              ← bootstrap
│   ├── class-packages.php            ← scope/commitment/pricing/tarjous-data
│   ├── class-fiboproduct.php         ← legacy fiboproduct-säilytys (varareitti)
│   ├── class-page-template.php       ← multi-slug routing + auto-page-creation
│   ├── class-assets.php              ← CSS/JS enqueue + JS-data
│   └── class-admin-settings.php      ← Asetukset-sivu + tarjous-tila
├── templates/
│   ├── page-liity.php                ← /liity
│   ├── page-rekisterointi.php        ← /jatka
│   ├── page-maksu.php                ← /maksu
│   ├── page-vahvistus.php            ← /vahvistus
│   └── partial-liity-cards.php       ← (deprecated, JS hoitaa)
└── assets/
    ├── css/liity.css                 ← petrol-paletti, kaikki sivupohjat
    └── js/liity.js                   ← interaktiot kaikilla 4 sivulla
```

---

## Testaus

Asennuksen jälkeen:

1. Avaa `/liity` — pitäisi näkyä scope-valinta + 3 commitment-korttia
2. Vaihda scopea — hinnat päivittyvät
3. Klikkaa info-ikonia "jumpat"-sanan vieressä — modaali avautuu
4. Klikkaa "Jatka ostamaan" — siirtyy /jatka-sivulle
5. Vaihda kirjautumisvalintaa — lomake muuttuu
6. Klikkaa "Jatka maksuun" — siirtyy /maksu-sivulle
7. Klikkaa upsellejä — Yhteenveto + Maksa-nappi päivittyvät reaaliajassa
8. Klikkaa "Maksa X €" — siirtyy /vahvistus-sivulle

Visuaalinen testi: kaikki 4 sivua käyttävät samaa petrol-paletia, sama
typografia (perii teemasta), sama yleinen rakenne.

---

## Tunnetut puutteet

- Tarjouksessa olevat sopimustyypit määritetään globaalisti — kaikki tarjoukset
  käyttävät samaa "1 €/kk ensimmäinen" -mekaniikkaa. Eri tarjouksia eri
  tasoille ei ole tuettu (esim. "BASIC tutustumistarjous" + "PLUS kausikortti-ale"
  yhtä aikaa).
- Vuosi-tarjous (Vuosisäästö-kortin tarjoushinta) ei ole datamallissa erikseen.
  Säterinportti tekee tarjousvuosikortit erillisinä SKUna (esim.
  "TARJOUS: Kuntosali 12kk vuosikortti 350€"), ne näkyvät vain verkkokaupassa
  eikä /liity-sivulla.
- Lomakkeen validointi puuttuu — submit-buttonin klikkaus etenee aina
  riippumatta lomakkeen tilasta. Tuotantoon vaaditaan validointi.
