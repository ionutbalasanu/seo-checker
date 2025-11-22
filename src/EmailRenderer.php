<?php
declare(strict_types=1);

final class EmailRenderer
{
    public static function renderHtml(string $url, array $data): string
    {
        $score = $data['score'] ?? [];
        $meta  = $score['meta'] ?? [];
        $title = (string)($meta['title'] ?? $url);

        $breakdown = $score['breakdown'] ?? [];
        $checks    = $score['checks'] ?? [];
        $context   = (string)($score['context'] ?? 'article');

        // scor global (on-page)
        $totalGlobal = isset($score['total_global'])
            ? (int)$score['total_global']
            : (int)($score['total'] ?? 0);

        // SEO local (din ArticleScorer::LOCAL_MAX + percent)
        $localInfo = $score['local'] ?? null;
        $localPoints  = (int)($localInfo['points']  ?? 0);
        $localMax     = (int)($localInfo['max']     ?? 0);
        $localPercent = (int)($localInfo['percent'] ?? 0);

        $source = (string)($data['source'] ?? '');

        $contentScore   = (int)($breakdown['content']   ?? 0);
        $structureScore = (int)($breakdown['structure'] ?? 0);
        $signalsScore   = (int)($breakdown['signals']   ?? 0);
        $localeScore    = (int)($breakdown['locale']    ?? 0);

        // procente pe categorii, ca în barchart-ul din UI
        $maxContent   = 40;
        $maxStructure = 25;
        $maxSignals   = 20;
        $maxLocale    = 15;

        $pctContent   = $maxContent   ? max(0, min(100, (int)round($contentScore   * 100 / $maxContent)))   : 0;
        $pctStructure = $maxStructure ? max(0, min(100, (int)round($structureScore * 100 / $maxStructure))) : 0;
        $pctSignals   = $maxSignals   ? max(0, min(100, (int)round($signalsScore   * 100 / $maxSignals)))   : 0;
        $pctLocale    = $maxLocale    ? max(0, min(100, (int)round($localeScore    * 100 / $maxLocale)))    : 0;

        $goodChecksGlobal = self::pickChecks($checks, false, 6);
        $badChecksGlobal  = self::pickChecks($checks, true, 6);

        $goodChecksLocal = self::pickChecksLocal($checks, false, 6);
        $badChecksLocal  = self::pickChecksLocal($checks, true, 6);

        $localCaption = 'SEO local este analizat separat și nu influențează scorul total (doar informativ).';
        if ($context === 'local') {
            $localCaption = 'Pentru această pagină, SEO local este inclus în scorul final (context „pagină locală”).';
        }

        $htmlTitle = self::eHtml($title);
        $htmlUrl   = self::eHtml($url);

        ob_start();
        ?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="utf-8">
  <title>Raport SEO</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f7fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:720px;background:transparent;">
          <!-- Badge / „logo” -->
          <tr>
            <td style="padding-bottom:12px;">
              <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#0f172a;color:#e5edff;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;">
                Novaweb SEO Checker
              </span>
            </td>
          </tr>

          <!-- Titlu + URL -->
          <tr>
            <td style="padding-bottom:6px;font-size:22px;font-weight:700;color:#0f172a;">
              Raport SEO pentru:
            </td>
          </tr>
          <tr>
            <td style="padding-bottom:2px;font-size:16px;font-weight:600;color:#0f172a;">
              <?php echo $htmlTitle; ?>
            </td>
          </tr>
          <tr>
            <td style="padding-bottom:16px;font-size:13px;color:#64748b;">
              <a href="<?php echo $htmlUrl; ?>" style="color:#2563eb;text-decoration:none;"><?php echo $htmlUrl; ?></a>
            </td>
          </tr>

          <!-- HERO CARD: scor + rezumat + bare pe categorii -->
          <tr>
            <td>
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#104172;border-radius:18px;padding:18px 18px 16px;color:#e5edff;box-shadow:0 18px 40px rgba(15,23,42,.40);">
                <tr>
                  <!-- stânga: scor + context -->
                  <td width="42%" style="vertical-align:top;padding-right:10px;">
                    <div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#bfdbfe;">
                      Scor SEO on-page
                    </div>
                    <div style="padding-top:6px;font-size:36px;font-weight:700;line-height:1;">
                      <?php echo (int)$totalGlobal; ?><span style="font-size:16px;color:#cbd5f5;">/100</span>
                    </div>
                    <div style="padding-top:6px;font-size:12px;color:#e5edff;">
                      Tip pagină:
                      <strong><?php echo $context === 'local' ? 'Pagină locală' : 'Articol obișnuit'; ?></strong>
                    </div>
                    <?php if ($localInfo !== null): ?>
                      <div style="padding-top:4px;font-size:12px;color:#c4f1c5;">
                        SEO local: <strong><?php echo $localPoints; ?>/<?php echo $localMax; ?></strong>
                        (<?php echo $localPercent; ?>%)
                      </div>
                    <?php endif; ?>
                    <div style="padding-top:8px;font-size:11px;color:#9ca3af;">
                      HTML analizat: <?php echo self::eHtml($source ?: 'rendered'); ?>
                    </div>
                  </td>

                  <!-- dreapta: rezumat + bare pe categorii -->
                  <td width="58%" style="vertical-align:top;padding-left:10px;border-left:1px solid rgba(148,163,184,.4);">
                    <!-- Rezumat scurt -->
                    <?php if ($badChecksGlobal): ?>
                      <div style="font-size:12px;color:#fee2e2;margin-bottom:4px;font-weight:600;">
                        De reparat cu prioritate:
                      </div>
                      <ul style="margin:0 0 8px 18px;padding:0;font-size:12px;color:#fee2e2;">
                        <?php foreach ($badChecksGlobal as $c): ?>
                          <li style="margin-bottom:2px;"><?php echo self::labelFor($c['id'], $c['note'], false); ?></li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>

                    <?php if ($goodChecksGlobal): ?>
                      <div style="font-size:12px;color:#bbf7d0;margin-bottom:4px;font-weight:600;">
                        Puncte forte:
                      </div>
                      <ul style="margin:0 0 8px 18px;padding:0;font-size:12px;color:#dcfce7;">
                        <?php foreach ($goodChecksGlobal as $c): ?>
                          <li style="margin-bottom:2px;"><?php echo self::labelFor($c['id'], $c['note'], true); ?></li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>

                    <!-- Bare pe categorii -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top:4px;">
                      <tr>
                        <td colspan="2" style="font-size:11px;color:#cbd5f5;padding-bottom:4px;">
                          Distribuția scorului pe categorii
                        </td>
                      </tr>

                      <?php
                      $cats = [
                          ['label' => 'Conținut & media',           'score' => $contentScore,   'max' => $maxContent,   'pct' => $pctContent],
                          ['label' => 'Structură & indexare',       'score' => $structureScore, 'max' => $maxStructure, 'pct' => $pctStructure],
                          ['label' => 'Metadate & rich snippets',   'score' => $signalsScore,   'max' => $maxSignals,   'pct' => $pctSignals],
                          ['label' => 'Localizare RO',              'score' => $localeScore,    'max' => $maxLocale,    'pct' => $pctLocale],
                      ];
                      foreach ($cats as $cat):
                        $pct = $cat['pct'];
                      ?>
                        <tr>
                          <td style="font-size:11px;color:#e5edff;padding:2px 6px 4px 0;white-space:nowrap;">
                            <?php echo self::eHtml($cat['label']); ?>
                          </td>
                          <td style="padding:2px 0 4px 0;">
                            <div style="font-size:11px;color:#bfdbfe;margin-bottom:2px;">
                              <?php echo (int)$cat['score']; ?>/<?php echo (int)$cat['max']; ?>
                            </div>
                            <div style="width:100%;height:6px;border-radius:999px;background:#0b2950;overflow:hidden;">
                              <div style="height:6px;border-radius:999px;background:linear-gradient(90deg,#6366f1,#22d3ee);width:<?php echo $pct; ?>%;"></div>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CARD SEO LOCAL separat -->
          <?php if ($localInfo !== null): ?>
            <tr>
              <td style="padding-top:16px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#ecfdf5;border-radius:14px;padding:14px 16px;border:1px solid #bbf7d0;">
                  <tr>
                    <td width="40%" style="vertical-align:top;padding-right:10px;">
                      <div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#16a34a;">
                        SEO local
                      </div>
                      <div style="padding-top:4px;font-size:24px;font-weight:700;color:#166534;line-height:1;">
                        <?php echo $localPercent; ?><span style="font-size:14px;color:#4b5563;">/100</span>
                      </div>
                      <div style="padding-top:4px;font-size:13px;color:#166534;">
                        Puncte locale: <strong><?php echo $localPoints; ?>/<?php echo $localMax; ?></strong>
                      </div>
                      <div style="padding-top:6px;font-size:11px;color:#6b7280;">
                        <?php echo self::eHtml($localCaption); ?>
                      </div>
                    </td>
                    <td width="60%" style="vertical-align:top;padding-left:10px;border-left:1px solid #bbf7d0;">
                      <?php if ($badChecksLocal): ?>
                        <div style="font-size:12px;color:#b91c1c;margin-bottom:4px;font-weight:600;">
                          De îmbunătățit (SEO local):
                        </div>
                        <ul style="margin:0 0 6px 18px;padding:0;font-size:12px;color:#b91c1c;">
                          <?php foreach ($badChecksLocal as $c): ?>
                            <li style="margin-bottom:2px;"><?php echo self::labelFor($c['id'], $c['note'], false); ?></li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>

                      <?php if ($goodChecksLocal): ?>
                        <div style="font-size:12px;color:#15803d;margin-bottom:4px;font-weight:600;">
                          Puncte forte (SEO local):
                        </div>
                        <ul style="margin:0 0 0 18px;padding:0;font-size:12px;color:#166534;">
                          <?php foreach ($goodChecksLocal as $c): ?>
                            <li style="margin-bottom:2px;"><?php echo self::labelFor($c['id'], $c['note'], true); ?></li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          <?php endif; ?>

          <!-- DETALII VERIFICĂRI GLOBAL -->
          <tr>
            <td style="padding-top:16px;">
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#ffffff;border-radius:14px;padding:14px 16px;border:1px solid #e2e8f0;">
                <tr>
                  <td style="font-size:13px;font-weight:600;color:#0f172a;padding-bottom:8px;">
                    Detalii verificări SEO on-page
                  </td>
                </tr>
                <tr>
                  <td style="font-size:12px;color:#0f172a;">
                    <?php echo self::renderChecksTable($checks, false); ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- DETALII VERIFICĂRI LOCAL -->
          <?php if ($localInfo !== null): ?>
            <tr>
              <td style="padding-top:12px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#ffffff;border-radius:14px;padding:14px 16px;border:1px solid #bbf7d0;">
                  <tr>
                    <td style="font-size:13px;font-weight:600;color:#166534;padding-bottom:8px;">
                      Detalii verificări SEO local
                    </td>
                  </tr>
                  <tr>
                    <td style="font-size:12px;color:#0f172a;">
                      <?php echo self::renderChecksTable($checks, true); ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          <?php endif; ?>

          <!-- FOOTER -->
          <tr>
            <td style="padding-top:12px;font-size:11px;color:#94a3b8;">
              Acest raport a fost generat automat de Novaweb SEO Checker.
              Rezultatele sunt orientative și nu înlocuiesc un audit SEO complet.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
        <?php
        return (string)ob_get_clean();
    }

    public static function renderText(string $url, array $data): string
    {
        $score = $data['score'] ?? [];
        $meta  = $score['meta'] ?? [];
        $title = (string)($meta['title'] ?? $url);

        $breakdown = $score['breakdown'] ?? [];
        $checks    = $score['checks'] ?? [];
        $context   = (string)($score['context'] ?? 'article');

        $totalGlobal = isset($score['total_global'])
            ? (int)$score['total_global']
            : (int)($score['total'] ?? 0);

        $localInfo = $score['local'] ?? null;
        $localPoints  = $localInfo['points']  ?? null;
        $localMax     = $localInfo['max']     ?? null;
        $localPercent = $localInfo['percent'] ?? null;

        $contentScore   = (int)($breakdown['content']   ?? 0);
        $structureScore = (int)($breakdown['structure'] ?? 0);
        $signalsScore   = (int)($breakdown['signals']   ?? 0);
        $localeScore    = (int)($breakdown['locale']    ?? 0);

        $goodChecksGlobal = self::pickChecks($checks, false, 6);
        $badChecksGlobal  = self::pickChecks($checks, true, 6);
        $goodChecksLocal  = self::pickChecksLocal($checks, false, 6);
        $badChecksLocal   = self::pickChecksLocal($checks, true, 6);

        $localCaption = 'SEO local este analizat separat și nu influențează scorul total (doar informativ).';
        if ($context === 'local') {
            $localCaption = 'Pentru această pagină, SEO local este inclus în scorul final (context „pagină locală”).';
        }

        $lines = [];
        $lines[] = 'Raport SEO pentru:';
        $lines[] = $title;
        $lines[] = $url;
        $lines[] = str_repeat('=', 48);
        $lines[] = '';
        $lines[] = sprintf('Scor SEO on-page: %d/100', $totalGlobal);
        $lines[] = sprintf('- Conținut: %d/40', $contentScore);
        $lines[] = sprintf('- Structură & Indexare: %d/25', $structureScore);
        $lines[] = sprintf('- Metadate & Snippets: %d/20', $signalsScore);
        $lines[] = sprintf('- Localizare RO: %d/15', $localeScore);
        $lines[] = '';

        if ($badChecksGlobal) {
            $lines[] = 'De îmbunătățit (global):';
            foreach ($badChecksGlobal as $c) {
                $lines[] = '  - '.self::labelForText($c['id'], $c['note'], false);
            }
            $lines[] = '';
        }

        if ($goodChecksGlobal) {
            $lines[] = 'Puncte forte (global):';
            foreach ($goodChecksGlobal as $c) {
                $lines[] = '  + '.self::labelForText($c['id'], $c['note'], true);
            }
            $lines[] = '';
        }

        if ($localInfo !== null) {
            $lines[] = str_repeat('-', 48);
            $lines[] = 'SEO Local';
            $lines[] = str_repeat('-', 48);
            $lines[] = sprintf('Scor SEO local: %d/100', (int)($localPercent ?? 0));
            $lines[] = sprintf('Puncte local: %d/%d', (int)($localPoints ?? 0), (int)($localMax ?? 0));
            $lines[] = $localCaption;
            $lines[] = '';

            if ($badChecksLocal) {
                $lines[] = 'De îmbunătățit (SEO local):';
                foreach ($badChecksLocal as $c) {
                    $lines[] = '  - '.self::labelForText($c['id'], $c['note'], false);
                }
                $lines[] = '';
            }
            if ($goodChecksLocal) {
                $lines[] = 'Puncte forte (SEO local):';
                foreach ($goodChecksLocal as $c) {
                    $lines[] = '  + '.self::labelForText($c['id'], $c['note'], true);
                }
                $lines[] = '';
            }
        }

        $lines[] = str_repeat('=', 48);
        $lines[] = 'Raport generat automat de Novaweb SEO Checker.';
        $lines[] = 'Rezultatele sunt orientative și nu înlocuiesc un audit complet.';
        $lines[] = '';

        return implode("\n", $lines);
    }

    /* ================== Helpers ================== */

    private static function eHtml(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Pick checks NON-local (id fără prefix "local_")
     * @param array<int,array<string,mixed>> $checks
     * @return array<int,array<string,mixed>>
     */
    private static function pickChecks(array $checks, bool $onlyFails, int $limit): array
    {
        $out = [];
        foreach ($checks as $c) {
            $id = (string)($c['id'] ?? '');
            if (strpos($id, 'local_') === 0) {
                continue;
            }
            $ok = !empty($c['ok']);
            if ($onlyFails && $ok) continue;
            if (!$onlyFails && !$ok) continue;
            $out[] = $c;
            if (count($out) >= $limit) break;
        }
        return $out;
    }

    /**
     * Pick checks DOAR local_*.
     * @param array<int,array<string,mixed>> $checks
     * @return array<int,array<string,mixed>>
     */
    private static function pickChecksLocal(array $checks, bool $onlyFails, int $limit): array
    {
        $out = [];
        foreach ($checks as $c) {
            $id = (string)($c['id'] ?? '');
            if (strpos($id, 'local_') !== 0) {
                continue;
            }
            $ok = !empty($c['ok']);
            if ($onlyFails && $ok) continue;
            if (!$onlyFails && !$ok) continue;
            $out[] = $c;
            if (count($out) >= $limit) break;
        }
        return $out;
    }

    /**
     * Etichetă text pentru HTML (global/local)
     */
    private static function labelFor(string $id, ?string $note, bool $positive): string
    {
        $label = self::mapLabel($id);
        if ($note) {
            $label .= ' — '.$note;
        }
        return self::eHtml($label);
    }

    /**
     * Etichetă text pentru plain text
     */
    private static function labelForText(string $id, ?string $note, bool $positive): string
    {
        $label = self::mapLabel($id);
        if ($note) {
            $label .= ' — '.$note;
        }
        return $label;
    }

    /**
     * Mapare id verificare -> text scurt în română
     */
    private static function mapLabel(string $id): string
    {
        switch ($id) {
            // Content & UX
            case 'word_count_800':             return 'Număr de cuvinte ≥ 800';
            case 'intro_mentions_topic':       return 'Intro-ul menționează tema principală';
            case 'h1_single':                  return 'Un singur H1 pe pagină';
            case 'headings_hierarchy':         return 'Ierarhie bună H2/H3';
            case 'lists_tables':               return 'Liste / tabele în conținut';
            case 'images_in_body':             return 'Imagini în corpul articolului';
            case 'img_alt_ratio_80':           return '≥ 80% dintre imagini au ALT';
            case 'lazyload_images':            return 'Imaginile folosesc lazy-load';
            case 'video_present':              return 'Video sau iframe în conținut';
            case 'date_published':             return 'Dată publicare vizibilă / în schema';
            case 'date_modified':              return 'Dată actualizare vizibilă / în schema';
            case 'author_visible_or_schema':   return 'Autor vizibil sau definit în schema';

            // Structură & Indexare
            case 'indexable':                  return 'Pagina este indexabilă (fără noindex)';
            case 'canonical_present':          return 'Canonical definit';
            case 'canonical_valid':            return 'Canonical valid (self sau domeniu corect)';
            case 'url_clean':                  return 'URL curat și scurt';
            case 'internal_links_present':     return 'Linkuri interne către alte pagini';
            case 'external_links_present':     return 'Linkuri externe către surse de încredere';

            // Meta & snippets
            case 'title_length_ok':            return 'Title cu lungime optimă';
            case 'meta_description_ok':        return 'Meta description cu lungime bună';
            case 'og_minimal':                 return 'Open Graph complet (title/description/image/url)';
            case 'twitter_card_large':         return 'Twitter Card (summary_large_image)';
            case 'schema_article_recommended': return 'Schema „Article/BlogPosting” recomandată';

            // Localizare clasică (limbă)
            case 'lang_ro':                    return 'Pagina este setată pe limba română (lang="ro")';
            case 'og_locale_or_inLanguage_ro': return 'OG locale / inLanguage setate pe RO';
            case 'date_format_ro':             return 'Date afișate în format românesc';
            case 'hreflang_pairs':             return 'Hreflang configurat';

            // SEO local extins
            case 'local_tel_click':            return 'Telefon click-to-call';
            case 'local_tel_prefix_local':     return 'Telefon cu prefix local românesc';
            case 'local_address_visible':      return 'Adresă fizică vizibilă în pagină';
            case 'local_directions_link':      return 'Link „Direcții” către hărți';
            case 'local_opening_hours':        return 'Program/orar afișat sau în schema';
            case 'local_schema_localbusiness': return 'Schema LocalBusiness implementată';
            case 'local_schema_postal':        return 'Schema PostalAddress';
            case 'local_schema_tel':           return 'Telefon definit în schema';
            case 'local_schema_geo':           return 'Coordonate geografice în schema';
            case 'local_schema_sameas':        return 'Legături spre profiluri / hartă (sameAs/hasMap)';
            case 'local_schema_area':          return 'Arie deservită (areaServed/serviceArea)';
            case 'local_schema_rating':        return 'Recenzii / note (aggregateRating)';
            case 'local_city_detected':        return 'Orașul afacerii este recunoscut în pagină';
            case 'local_city_in_title':        return 'Orașul apare în title';
            case 'local_city_in_h1':           return 'Orașul apare în H1';
            case 'local_city_in_slug':         return 'Orașul apare în URL';
            case 'local_city_in_intro':        return 'Orașul apare în introducere';
            case 'local_map_embed':            return 'Hartă embed (Google Maps)';
            case 'local_alt_has_city':         return 'ALT imagini conține numele orașului';
            case 'local_locator':              return 'Store locator / pagini pentru mai multe locații';
            case 'local_whatsapp':             return 'WhatsApp click-to-chat';

            default:
                return $id;
        }
    }

    /**
     * Tabel HTML pentru verificări (global vs local, în funcție de $localOnly)
     */
    private static function renderChecksTable(array $checks, bool $localOnly): string
    {
        $rows = [];
        foreach ($checks as $c) {
            $id   = (string)($c['id']   ?? '');
            $ok   = !empty($c['ok']);
            $note = $c['note'] ?? '';

            $isLocal = (strpos($id, 'local_') === 0);
            if ($localOnly && !$isLocal) continue;
            if (!$localOnly && $isLocal) continue;

            $label = self::mapLabel($id);
            if ($note) {
                $label .= ' — '.$note;
            }

            $rows[] = sprintf(
                '<tr><td style="padding:2px 6px 2px 0;font-size:12px;width:16px;">%s</td><td style="padding:2px 0;font-size:12px;">%s</td></tr>',
                $ok ? '✅' : '⚠️',
                self::eHtml($label)
            );
        }

        if (!$rows) {
            return '<div style="font-size:12px;color:#64748b;">Nicio verificare relevantă.</div>';
        }

        return '<table width="100%" cellpadding="0" cellspacing="0" role="presentation">'.implode('', $rows).'</table>';
    }
}
