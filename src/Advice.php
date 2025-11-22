<?php
declare(strict_types=1);

final class Advice
{
    /** Etichete „frumoase” pentru fiecare check id */
    public static function label(string $id): string
    {
        static $L = [
            // Content & UX
            'word_count_800'            => 'Cuvinte ≥ 800',
            'intro_mentions_topic'      => 'Intro menționează tema',
            'h1_single'                 => 'H1 unic',
            'headings_hierarchy'        => 'Ierarhie H2/H3',
            'lists_tables'              => 'Liste/Tabele în conținut',
            'images_in_body'            => 'Imagini în corp',
            'img_alt_ratio_80'          => 'ALT ≥ 80%',
            'lazyload_images'           => 'Lazy-load imagini',
            'video_present'             => 'Video/Iframe prezent',
            'date_published'            => 'Dată publicare',
            'date_modified'             => 'Dată actualizare',
            'author_visible_or_schema'  => 'Autor vizibil/Schema',

            // Structură & Indexare
            'indexable'                 => 'Indexabil (fără noindex)',
            'canonical_present'         => 'Canonical prezent',
            'canonical_valid'           => 'Canonical valid/self',
            'url_clean'                 => 'URL curat',
            'internal_links_present'    => '≥ 1 link intern în articol',
            'external_links_present'    => '≥ 1 link extern în articol',

            // Metadate & Rich
            'title_length_ok'           => 'Title lungime OK',
            'meta_description_ok'       => 'Meta description OK',
            'og_minimal'                => 'Open Graph minim',
            'twitter_card_large'        => 'Twitter Card (large)',
            'schema_article_recommended'=> 'JSON-LD Article recomandat',

            // Localizare RO — de bază
            'lang_ro'                   => 'lang="ro"/"ro-RO"',
            'og_locale_or_inLanguage_ro'=> 'OG locale / inLanguage „ro”',
            'date_format_ro'            => 'Dată în format RO',
            'hreflang_pairs'            => 'Hreflang (dacă există)',

            // Local audit – EXTRA (id încep cu local_)
            'local_tel_click'           => 'Telefon click-to-call',
            'local_tel_prefix_local'    => 'Prefix local (+40 2x/3x)',
            'local_address_visible'     => 'Adresă vizibilă',
            'local_directions_link'     => 'Link „Direcții” (hărți)',
            'local_opening_hours'       => 'Program/Orar',
            'local_schema_localbusiness'=> 'Schema LocalBusiness',
            'local_schema_postal'       => 'Schema PostalAddress',
            'local_schema_tel'          => 'Schema telephone',
            'local_schema_geo'          => 'Schema geo (lat/long)',
            'local_schema_sameas'       => 'sameAs/hasMap',
            'local_schema_area'         => 'areaServed/serviceArea',
            'local_schema_rating'       => 'aggregateRating/review',
            'local_city_detected'       => 'Oraș detectat',
            'local_city_in_title'       => 'Oraș în Title',
            'local_city_in_h1'          => 'Oraș în H1',
            'local_city_in_slug'        => 'Oraș în URL slug',
            'local_city_in_intro'       => 'Oraș în intro',
            'local_map_embed'           => 'Embed hartă',
            'local_alt_has_city'        => 'ALT imagini cu oraș',
            'local_locator'             => 'Store locator / pagini locații',
            'local_whatsapp'            => 'WhatsApp click-to-chat',
        ];
        return $L[$id] ?? ('Test: ' . $id);
    }

    /** Recomandare pentru fiecare test picat */
    public static function tip(string $id, ?string $note = ''): string
    {
        switch ($id) {
            // Content & UX
            case 'word_count_800': return 'Extinde articolul la minimum 800 de cuvinte. Adaugă secțiuni noi (întrebări frecvente, exemple, studii de caz) fără umplutură.';
            case 'intro_mentions_topic': return 'Include termenul principal în primele 100 de cuvinte. Reformulează introducerea ca să conțină exact expresia-țintă.';
            case 'h1_single': return 'Folosește un singur H1 per pagină, restul devin H2/H3.';
            case 'headings_hierarchy': return 'Păstrează ierarhia fără a sări niveluri (H2 → H3). Adaugă cel puțin 3 subcapitole (H2/H3).';
            case 'lists_tables': return 'Transformă paragrafele grele în liste buletate sau tabele pentru scanabilitate.';
            case 'images_in_body': return 'Adaugă cel puțin o imagine contextuală în corpul articolului.';
            case 'img_alt_ratio_80': return 'Completează atributul ALT descriptiv pentru imagini (≥80%). Evită keyword stuffing.';
            case 'lazyload_images': return 'Activează lazy-loading (`loading="lazy"` sau plugin de optimizare).';
            case 'video_present': return 'Încorporează un video/iframe relevant (ex. tutorial, demo).';
            case 'date_published': return 'Afișează data publicării cu `<time datetime="...">` sau meta `article:published_time`.';
            case 'date_modified': return 'Expune data actualizării (`dateModified` în schema sau `<time>`).';
            case 'author_visible_or_schema': return 'Afișează autorul sau declară-l în JSON-LD (name/URL).';

            // Structură & Indexare
            case 'indexable': return 'Elimină `noindex` din meta robots dacă vrei să fie indexată.';
            case 'canonical_present': return 'Adaugă `<link rel="canonical" href="...">` spre versiunea preferată.';
            case 'canonical_valid': return 'Setează canonical pe același domeniu și URL canonic al paginii.';
            case 'url_clean': return 'Curăță slug-ul: scurt, descriptiv, fără spații/parametri inutili.';
            case 'internal_links_present': return 'Leagă spre 1–3 pagini interne relevante din corpul articolului.';
            case 'external_links_present': return 'Adaugă cel puțin un link extern de autoritate (surse, standarde).';

            // Metadate & Rich
            case 'title_length_ok': return 'Țintește 35–65 caractere. Mută termenul principal la început.';
            case 'meta_description_ok': return 'Scrie 120–170 caractere, convingător, diferit de titlu, cu USP.';
            case 'og_minimal': return 'Completează OG: `og:title`, `og:description`, `og:image` (≥1200px), `og:url`.';
            case 'twitter_card_large': return 'Setează `twitter:card=summary_large_image` și definește titlu/descriere/imagine.';
            case 'schema_article_recommended': return 'Adaugă JSON-LD Article/BlogPosting (headline, image, author, datePublished, dateModified).';

            // Localizare RO (de bază)
            case 'lang_ro': return 'Setează `lang="ro"` pe `<html>` și/sau `inLanguage: "ro-RO"` în schema.';
            case 'og_locale_or_inLanguage_ro': return 'Setează `og:locale=ro_RO` sau `inLanguage: "ro-RO"` în JSON-LD.';
            case 'date_format_ro': return 'Afișează date în format RO (ex. „12 noiembrie 2025”).';
            case 'hreflang_pairs': return 'Adaugă link-uri `rel="alternate" hreflang="..."` pentru versiunile lingvistice disponibile.';

            // Local – EXTRA
            case 'local_tel_click': return 'Folosește link `tel:` pe numărul de telefon (click-to-call pe mobil).';
            case 'local_tel_prefix_local': return 'Asigură-te că prefixul telefonului corespunde zonei (ex. +40 21 București).';
            case 'local_address_visible': return 'Afișează adresa completă (stradă, număr, oraș, județ).';
            case 'local_directions_link': return 'Adaugă link „Direcții” către Google Maps/Apple Maps.';
            case 'local_opening_hours': return 'Afișează programul (L–D) și marchează-l în schema `openingHoursSpecification`.';
            case 'local_schema_localbusiness': return 'Adaugă `@type: LocalBusiness` (sau subtip: Dentist, Restaurant etc).';
            case 'local_schema_postal': return 'Include `PostalAddress` (streetAddress, addressLocality, addressRegion, postalCode).';
            case 'local_schema_tel': return 'Include `telephone` în schema LocalBusiness.';
            case 'local_schema_geo': return 'Include `geo` (latitude/longitude) corecte.';
            case 'local_schema_sameas': return 'Declară `sameAs` (GMB, Facebook, Instagram) și `hasMap` cu link hartă.';
            case 'local_schema_area': return 'Definește `areaServed`/`serviceArea` pt. zonele acoperite.';
            case 'local_schema_rating': return 'Include `aggregateRating`/`review` dacă există recenzii reale.';
            case 'local_city_detected': return 'Menționează explicit orașul în conținut.';
            case 'local_city_in_title': return 'Adaugă orașul în `<title>` când e relevant local.';
            case 'local_city_in_h1': return 'Include orașul în H1 dacă pagina țintește o zonă.';
            case 'local_city_in_slug': return 'Include orașul în slug (ex. /serviciu-bucuresti/).';
            case 'local_city_in_intro': return 'Menționează orașul în introducere, natural.';
            case 'local_map_embed': return 'Încorporează o hartă (iframe) cu locația exactă.';
            case 'local_alt_has_city': return 'Folosește orașul în ALT acolo unde are sens (ex. „instalație AC București”).';
            case 'local_locator': return 'Creează pagini individuale per locație și/sau un „store locator”.';
            case 'local_whatsapp': return 'Adaugă buton WhatsApp (click-to-chat) pentru conversii pe mobil.';

            default:
                return 'Verificare specifică: ' . $id . ($note ? (' — ' . $note) : '');
        }
    }
}
