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
            'schema_article_recommended'=> 'JSON-LD Article recomandat',

            // Localizare RO — de bază
            'lang_ro'                   => 'lang="ro"/"ro-RO"',
            'og_locale_or_inLanguage_ro'=> 'OG locale / inLanguage „ro”',
            'date_format_ro'            => 'Dată în format RO',
            'hreflang_pairs'            => 'Hreflang (dacă există)',

            // Verificări moderne adiționale (2025+)
            'faq_schema_present'        => 'Schema FAQPage prezentă',
            'html_valid'                => 'HTML fără erori majore',
            'meta_robots_ok'            => 'Meta robots configurat corect',
            'image_dimensions_defined'  => 'Dimensiuni definite pentru imagini',
            'fonts_preload'             => 'Fonturi principale preîncărcate',
            'cls_risky_elements'        => 'Elemente cu risc de CLS controlat',
            'schema_breadcrumbs'        => 'Schema BreadcrumbList',
            'schema_image_required_fields' => 'Imagini din schema cu width/height',

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

    /**
     * Regula / pragul necesar pentru a considera testul trecut.
     * Folosită pentru a afișa „* ...” în UI și în email.
     */
    public static function rule(string $id): ?string
    {
        switch ($id) {
            // Content & UX
            case 'word_count_800':
                return 'Numărul de cuvinte trebuie să fie de cel puțin 800 în articolul analizat.';
            case 'intro_mentions_topic':
                return 'Termenul principal trebuie să apară în primele aproximativ 100 de cuvinte ale textului.';
            case 'h1_single':
                return 'Trebuie să existe exact un singur tag H1 relevant pe pagină.';
            case 'headings_hierarchy':
                return 'Structura H2/H3 nu trebuie să sară niveluri și ar trebui să existe cel puțin 3 subcapitole (H2/H3).';
            case 'lists_tables':
                return 'În conținut trebuie să existe cel puțin o listă (ul/ol) sau un tabel.';
            case 'images_in_body':
                return 'În corpul articolului trebuie să existe cel puțin o imagine contextuală reală.';
            case 'img_alt_ratio_80':
                return 'Cel puțin 80% dintre imaginile relevante trebuie să aibă atribut ALT completat.';
            case 'lazyload_images':
                return 'Cel puțin o imagine (ideal toate) trebuie să folosească lazy-load (loading="lazy" sau echivalent).';
            case 'date_published':
                return 'Trebuie expusă data publicării, fie vizibilă, fie în meta/JSON-LD.';
            case 'date_modified':
                return 'Trebuie expusă data ultimei actualizări (dateModified/meta/JSON-LD).';
            case 'author_visible_or_schema':
                return 'Autorul trebuie să fie vizibil în pagină sau definit în schema (author).';

            // Structură & Indexare
            case 'indexable':
                return 'Pagina nu trebuie să aibă meta robots cu „noindex” sau directive echivalente.';
            case 'canonical_present':
                return 'Trebuie să existe un link rel="canonical" către versiunea preferată a paginii.';
            case 'canonical_valid':
                return 'Canonical-ul trebuie să fie pe același domeniu și să indice URL-ul canonic corect.';
            case 'url_clean':
                return 'Slug-ul URL trebuie să fie scurt, fără parametri inutili și fără spații sau caractere problematice.';
            case 'internal_links_present':
                return 'Trebuie să existe cel puțin un link intern relevant către altă pagină din site.';
            case 'external_links_present':
                return 'Trebuie să existe cel puțin un link extern către o sursă de încredere.';

            // Metadate & Rich
            case 'title_length_ok':
                return 'Titlul SEO ar trebui să aibă între ~35 și ~65 de caractere.';
            case 'meta_description_ok':
                return 'Meta description ar trebui să aibă între ~120 și ~170 de caractere.';
            case 'og_minimal':
                return 'Trebuie definite cel puțin og:title, og:description, og:image și og:url.';
            case 'schema_article_recommended':
                return 'Trebuie implementată schema Article/BlogPosting cu headline, image, author, datePublished și ideal dateModified.';

            // Localizare RO — de bază
            case 'lang_ro':
                return 'Atributul lang pe <html> ( și/sau inLanguage în schema) trebuie setat pe ro sau ro-RO.';
            case 'og_locale_or_inLanguage_ro':
                return 'Trebuie setat og:locale=ro_RO sau inLanguage="ro-RO" în JSON-LD.';
            case 'date_format_ro':
                return 'Datele afișate în pagină ar trebui să folosească numele lunilor în limba română.';
            case 'hreflang_pairs':
                return 'Pentru versiunile lingvistice alternative trebuie definite link-uri rel="alternate" hreflang="...".';

            // Verificări moderne adiționale
            case 'faq_schema_present':
                return 'Trebuie să existe un obiect JSON-LD @type FAQPage, cu questions/acceptedAnswer care corespund întrebărilor reale din conținut.';
            case 'html_valid':
                return 'Markup-ul HTML nu trebuie să conțină erori majore (tag-uri neînchise, atribute invalide, id-uri duplicate) care pot afecta randarea și interpretarea de către crawleri.';
            case 'meta_robots_ok':
                return 'Meta robots nu trebuie să conțină noindex/nofollow/none pentru paginile care ar trebui să se indexeze.';
            case 'image_dimensions_defined':
                return 'Imaginile din conținut trebuie să aibă atribute width și height definite pentru a preveni CLS.';
            case 'fonts_preload':
                return 'Fonturile web critice ar trebui preîncărcate (link rel="preload") pentru a reduce flicker-ul textului.';
            case 'cls_risky_elements':
                return 'Elementele instabile (bannere, pop-up-uri, embed-uri) trebuie să aibă spațiu rezervat în layout pentru a evita schimbări bruște (CLS mare).';
            case 'schema_breadcrumbs':
                return 'Pentru articole și pagini structurale este recomandată schema BreadcrumbList pentru a expune ierarhia paginii.';
            case 'schema_image_required_fields':
                return 'Imaginile folosite în schema articolului trebuie să aibă definite URL, width și height.';

            // Local – EXTRA (SEO local)
            case 'local_tel_click':
                return 'În pagină trebuie să existe un link „tel:” pentru numărul de telefon (click-to-call).';
            case 'local_tel_prefix_local':
                return 'Numărul de telefon trebuie să fie un număr românesc valid (prefix 0, 40 sau 0040 urmat de 2x/3x/7x).';
            case 'local_address_visible':
                return 'Adresa fizică (stradă, oraș, județ) trebuie vizibilă în pagină.';
            case 'local_directions_link':
                return 'Trebuie să existe un link de tip „Direcții” către Google Maps / Apple Maps / Waze etc.';
            case 'local_opening_hours':
                return 'Programul de funcționare trebuie afișat în text sau definit în schema (openingHoursSpecification).';
            case 'local_schema_localbusiness':
                return 'Trebuie implementat un obiect JSON-LD @type LocalBusiness (sau subtip).';
            case 'local_schema_postal':
                return 'Schema trebuie să includă PostalAddress cu streetAddress, addressLocality, addressRegion, postalCode.';
            case 'local_schema_tel':
                return 'Schema LocalBusiness trebuie să includă câmpul telephone.';
            case 'local_schema_geo':
                return 'Schema trebuie să conțină coordonate geo (latitude și longitude).';
            case 'local_schema_sameas':
                return 'Schema trebuie să includă sameAs și/sau hasMap (profiluri și hartă).';
            case 'local_schema_area':
                return 'Trebuie definită aria deservită (areaServed/serviceArea) în schema locală.';
            case 'local_schema_rating':
                return 'Pentru recenzii trebuie folosit aggregateRating sau review în JSON-LD.';
            case 'local_city_detected':
                return 'Numele orașului afacerii trebuie să apară în pagină (titlu, H1, URL sau conținut).';
            case 'local_city_in_title':
                return 'Numele orașului ar trebui inclus în <title> pentru paginile locale.';
            case 'local_city_in_h1':
                return 'Numele orașului ar trebui inclus în H1 pentru paginile locale.';
            case 'local_city_in_slug':
                return 'Slug-ul URL ar trebui să conțină numele orașului (ex. /serviciu-bucuresti/).';
            case 'local_city_in_intro':
                return 'Introducerea textului trebuie să menționeze orașul în mod natural.';
            case 'local_map_embed':
                return 'Trebuie încorporată o hartă (iframe) cu locația exactă (ex. Google Maps).';
            case 'local_alt_has_city':
                return 'Cel puțin o imagine relevantă ar trebui să aibă numele orașului în atributul ALT.';
            case 'local_locator':
                return 'Site-ul ar trebui să aibă pagini distincte per locație și/sau un store locator.';
            case 'local_whatsapp':
                return 'Ar trebui să existe un link/buton WhatsApp click-to-chat pentru contact rapid.';

            default:
                return null;
        }
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
            case 'schema_article_recommended': return 'Adaugă JSON-LD Article/BlogPosting (headline, image, author, datePublished, dateModified).';

            // Localizare RO (de bază)
            case 'lang_ro': return 'Setează `lang="ro"` pe `<html>` și/sau `inLanguage: "ro-RO"` în schema.';
            case 'og_locale_or_inLanguage_ro': return 'Setează `og:locale=ro_RO` sau `inLanguage: "ro-RO"` în JSON-LD.';
            case 'date_format_ro': return 'Afișează date în format RO (ex. „12 noiembrie 2025”).';
            case 'hreflang_pairs': return 'Adaugă link-uri `rel="alternate" hreflang="..."` pentru versiunile lingvistice disponibile.';

            // Verificări moderne adiționale
            case 'faq_schema_present': return 'Identifică secțiunea de întrebări frecvente reală din pagină și adaugă schema FAQPage cu question/acceptedAnswer. Publică doar întrebări reale, nu keyword stuffing.';
            case 'html_valid': return 'Rulează pagina printr-un validator HTML (sau raport devtools) și corectează tag-urile neînchise, atributele invalide și id-urile duplicate. Păstrează DOM-ul curat pentru crawleri.';
            case 'meta_robots_ok': return 'Verifică meta robots pentru noindex/nofollow/none pe paginile importante și elimină-le acolo unde vrei trafic organic.';
            case 'image_dimensions_defined': return 'Adaugă width și height pe imaginile inline (sau utilizând CSS cu aspect-ratio) ca browserul să poată rezerva spațiul și să reduci CLS.';
            case 'fonts_preload': return 'Identifică fonturile custom critice (primary text / headings) și adaugă `link rel="preload"` pentru fișierele WOFF2 relevante.';
            case 'cls_risky_elements': return 'Rezervă spațiu pentru bannere, pop-up-uri, notificări și embed-uri (Google Maps, video) folosind înălțimi fixe sau aspect-ratio, ca să eviți săriturile de layout.';
            case 'schema_breadcrumbs': return 'Implementează JSON-LD BreadcrumbList cu poziție, nume și URL pentru fiecare nivel (Home > categorie > articol). Ajută Google să înțeleagă ierarhia.';
            case 'schema_image_required_fields': return 'În JSON-LD Article/BlogPosting, asigură-te că imaginile au URL complet, width și height. Ideal, folosește imagini mari (min. 1200px lățime).';

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
