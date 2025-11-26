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

        // scor global (on-page pur)
        $totalGlobal = isset($score['total_global'])
            ? (int)$score['total_global']
            : (int)($score['total'] ?? 0);

        // scor combinat (același ca în UI / donut)
        $totalCombined = isset($score['total'])
            ? (int)$score['total']
            : $totalGlobal;

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

        // combin top priorități globale + locale (max 6)
        $topPriorities = array_slice(array_merge($badChecksGlobal, $badChecksLocal), 0, 6);

        $htmlTitle = self::eHtml($title);
        $htmlUrl   = self::eHtml($url);

        // Logo:
        // 1) dacă trimiți $data['logo_url'], îl folosește
        // 2) altfel încearcă envget('NOVAWEB_LOGO_URL') (din bootstrap.php)
        $logoUrl = 'https://novaweb.ro/wp-content/uploads/2025/11/logo_white.avif';
        if (!empty($data['logo_url'])) {
            $logoUrl = (string)$data['logo_url'];
        } elseif (function_exists('envget')) {
            $envLogo = (string)(envget('NOVAWEB_LOGO_URL', '') ?? '');
            if ($envLogo !== '') {
                $logoUrl = $envLogo;
            }
        }

        ob_start();
        ?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="utf-8">
  <title>Raport SEO - Novaweb</title>
</head>
<body style="margin:0;padding:0;background-color:#e5e7eb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:760px;background:#f9fafb;border-radius:18px;box-shadow:0 18px 40px rgba(15,23,42,.16);overflow:hidden;">

          <!-- HEADER + HERO -->
          <tr>
            <td style="padding:0;background:#020617;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <!-- header strip -->
                <tr>
                  <td style="padding:14px 20px 10px 20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td valign="middle" align="left">
                          <?php if ($logoUrl !== ''): ?>
                            <img src="<?php echo self::eHtml($logoUrl); ?>" alt="Novaweb"
                                 style="display:block;height:30px;width:auto;">
                          <?php else: ?>
                            <div style="font-size:18px;font-weight:700;color:#e5e7eb;">Novaweb</div>
                          <?php endif; ?>
                        </td>
                        <td valign="middle" align="right">
                          <span style="display:inline-block;padding:4px 10px;border-radius:999px;background:#111827;color:#bfdbfe;font-size:10px;font-weight:600;letter-spacing:.09em;text-transform:uppercase;">
                            Audit SEO automat
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

                <!-- hero content -->
                <tr>
                  <td style="padding:0 20px 18px 20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%"
                           style="background:radial-gradient(circle at top,#00103d73 0,#020617 52%);border-radius:14px;padding:16px 16px 14px 16px;">
                      <tr>
                        <!-- stânga: titlu + URL -->
                        <td valign="top" align="left" style="padding-right:10px;">
                          <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#9ca3af;margin-bottom:4px;">
                            Raport SEO pentru pagină
                          </div>
                          <div style="font-size:16px;font-weight:600;color:#f9fafb;margin-bottom:4px;">
                            <?php echo $htmlTitle; ?>
                          </div>
                          <div style="font-size:12px;color:#bfdbfe;margin-bottom:6px;word-break:break-all;">
                            <a href="<?php echo $htmlUrl; ?>" style="color:#bfdbfe;text-decoration:none;"><?php echo $htmlUrl; ?></a>
                          </div>
                        </td>

                        <!-- dreapta: scor -->
                        <td valign="top" align="right" style="padding-left:12px;">
                          <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td align="right">
                                <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#9ca3af;margin-bottom:4px;">
                                  Scor SEO total
                                </div>
                                <div style="font-size:40px;font-weight:700;color:#f9fafb;line-height:1;">
                                  <?php echo (int)$totalCombined; ?><span style="font-size:18px;color:#9ca3af;">/100</span>
                                </div>
                              </td>
                            </tr>
                            <tr>
                              <td align="right" style="padding-top:8px;">
                                <div style="font-size:12px;color:#e5e7eb;margin-bottom:2px;">
                                  Tip pagină:
                                  <strong><?php echo $context === 'local'
                                      ? 'Pagină locală (on-page + local)'
                                      : 'Articol general'; ?></strong>
                                </div>
                                <?php if ($localInfo !== null): ?>
                                  <div style="font-size:12px;color:#bbf7d0;margin-bottom:2px;">
                                    Scor on-page (global): <strong><?php echo $totalGlobal; ?>/100</strong> &nbsp;·&nbsp;
                                    SEO local: <strong><?php echo $localPoints; ?>/<?php echo $localMax; ?></strong> (<?php echo $localPercent; ?>%)
                                  </div>
                                <?php else: ?>
                                  <div style="font-size:12px;color:#cbd5f5;margin-bottom:2px;">
                                    Scor on-page (global): <strong><?php echo $totalGlobal; ?>/100</strong>
                                  </div>
                                <?php endif; ?>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

              </table>
            </td>
          </tr>

          <!-- REZUMAT PE CATEGORII -->
          <tr>
            <td style="padding:18px 22px 6px 22px;background:#f9fafb;">
              <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px;">
                Rezumat pe categorii
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 22px 16px 22px;background:#f9fafb;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <?php
                  $cats = [
                      ['label' => 'Conținut & media',         'score' => $contentScore,   'max' => $maxContent,   'pct' => $pctContent],
                      ['label' => 'Structură & indexare',     'score' => $structureScore, 'max' => $maxStructure, 'pct' => $pctStructure],
                      ['label' => 'Metadate & rich snippets', 'score' => $signalsScore,   'max' => $maxSignals,   'pct' => $pctSignals],
                      ['label' => 'Localizare RO',            'score' => $localeScore,    'max' => $maxLocale,    'pct' => $pctLocale],
                  ];
                  foreach ($cats as $cat):
                  ?>
                  <td valign="top" style="width:25%;padding:4px 4px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#ffffff;border-radius:12px;border:1px solid #e5e7eb;padding:8px 8px 6px 8px;">
                      <tr>
                        <td style="font-size:11px;color:#6b7280;padding-bottom:2px;">
                          <?php echo self::eHtml($cat['label']); ?>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:14px;font-weight:600;color:#111827;padding-bottom:2px;">
                          <?php echo (int)$cat['score']; ?>/<?php echo (int)$cat['max']; ?>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:11px;color:#4b5563;">
                          <?php echo (int)$cat['pct']; ?>% acoperire
                        </td>
                      </tr>
                    </table>
                  </td>
                  <?php endforeach; ?>
                </tr>
              </table>
              <div style="font-size:11px;color:#6b7280;margin-top:6px;">
                <?php echo self::eHtml($localCaption); ?>
              </div>
            </td>
          </tr>

          <!-- PRIORITĂȚI PRINCIPALE -->
          <?php if ($topPriorities): ?>
          <tr>
            <td style="padding:0 22px 6px 22px;background:#f9fafb;">
              <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px;">
                Priorități principale (de reparat cu prioritate)
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 22px 16px 22px;background:#f9fafb;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#ffffff;border-radius:12px;border:1px solid #fee2e2;padding:10px 10px 8px 10px;">
                <?php foreach ($topPriorities as $c): ?>
                  <?php
                    $id   = (string)($c['id'] ?? '');
                    $note = (string)($c['note'] ?? '');
                    $label = self::mapLabel($id);
                    $tip   = '';
                    if (class_exists('Advice')) {
                        try {
                            $tip = \Advice::tip($id, $note);
                        } catch (\Throwable $e) {
                            $tip = '';
                        }
                    }
                  ?>
                  <tr>
                    <td style="font-size:12px;color:#b91c1c;padding:4px 4px 2px 0;">
                      <span style="display:inline-block;min-width:22px;padding:2px 7px;border-radius:999px;background:#fee2e2;color:#b91c1c;font-size:10px;font-weight:600;text-transform:uppercase;">
                        Fix
                      </span>
                    </td>
                    <td style="font-size:12px;color:#111827;padding:4px 0 2px 0;">
                      <strong><?php echo self::eHtml($label); ?></strong>
                      <?php if ($note): ?>
                        <span style="color:#4b5563;"> — <?php echo self::eHtml($note); ?></span>
                      <?php endif; ?>
                      <?php if ($tip): ?>
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">
                          Recomandare: <?php echo self::eHtml($tip); ?>
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </table>
            </td>
          </tr>
          <?php endif; ?>

          <!-- DETALII VERIFICĂRI GLOBAL -->
          <tr>
            <td style="padding:0 22px 6px 22px;background:#f9fafb;">
              <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px;">
                Detalii verificări SEO on-page
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 22px 16px 22px;background:#f9fafb;">
              <?php echo self::renderChecksTable($checks, false); ?>
            </td>
          </tr>

          <!-- DETALII VERIFICĂRI LOCAL -->
          <?php if ($localInfo !== null): ?>
          <tr>
            <td style="padding:0 22px 6px 22px;background:#f9fafb;">
              <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px;">
                Detalii verificări SEO local
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 22px 16px 22px;background:#f9fafb;">
              <?php echo self::renderChecksTable($checks, true); ?>
            </td>
          </tr>
          <?php endif; ?>

          <!-- CTA / Marketing -->
          <tr>
            <td style="padding:0 22px 18px 22px;background:#f9fafb;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#ffffff;border-radius:14px;border:1px solid #dbeafe;padding:14px 14px 12px 14px;">
                <tr>
                  <td valign="top" style="padding-right:8px;">
                    <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:4px;">
                      Ai nevoie de ajutor pentru implementarea recomandărilor?
                    </div>
                    <div style="font-size:12px;color:#4b5563;margin-bottom:10px;line-height:1.6;">
                      Echipa <strong>Novaweb</strong> te poate ajuta cu optimizarea conținutului,
                      implementarea schemelor, SEO local (Google Maps) și strategia generală
                      de creștere organică.
                    </div>
                    <a href="https://novaweb.ro/contact/" target="_blank" rel="noopener"
                       style="display:inline-block;padding:9px 18px;border-radius:999px;background:linear-gradient(135deg,#2563eb,#38bdf8);color:#ffffff;font-size:13px;font-weight:600;text-decoration:none;">
                      Programează o discuție cu Novaweb
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="padding:10px 22px 18px 22px;background:#f9fafb;border-top:1px solid #e5e7eb;">
              <div style="font-size:11px;color:#6b7280;line-height:1.5;">
                Acest raport a fost generat automat de <strong>Novaweb Audit SEO One-Page</strong>.
                Rezultatele sunt orientative și nu înlocuiesc un audit SEO complet.
                Pentru un plan personalizat de optimizare, intră pe
                <a href="https://novaweb.ro/contact/" target="_blank" rel="noopener" style="color:#2563eb;text-decoration:none;">novaweb.ro/contact</a>.
              </div>
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

        // la fel ca în HTML: scor combinat
        $totalGlobal = isset($score['total_global'])
            ? (int)$score['total_global']
            : (int)($score['total'] ?? 0);
        $totalCombined = isset($score['total'])
            ? (int)$score['total']
            : $totalGlobal;

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
        $lines[] = str_repeat('=', 60);
        $lines[] = '';
        $lines[] = sprintf('Scor SEO total: %d/100', $totalCombined);
        $lines[] = sprintf('- Scor on-page (global): %d/100', $totalGlobal);
        $lines[] = sprintf('- Conținut & media: %d/40', $contentScore);
        $lines[] = sprintf('- Structură & Indexare: %d/25', $structureScore);
        $lines[] = sprintf('- Metadate & Snippets: %d/20', $signalsScore);
        $lines[] = sprintf('- Localizare RO: %d/15', $localeScore);
        $lines[] = '';

        if ($localInfo !== null) {
            $lines[] = sprintf('- SEO local: %d/%d (%d%%)', (int)($localPoints ?? 0), (int)($localMax ?? 0), (int)($localPercent ?? 0));
        }
        $lines[] = '';

        if ($badChecksGlobal) {
            $lines[] = 'Fix (global):';
            foreach ($badChecksGlobal as $c) {
                $tip = class_exists('Advice') ? \Advice::tip((string)$c['id'], (string)($c['note'] ?? '')) : '';
                $lines[] = '  - '.self::labelForText($c['id'], $c['note'], false);
                if ($tip) {
                    $lines[] = '      Recomandare: '.$tip;
                }
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
            $lines[] = str_repeat('-', 60);
            $lines[] = 'SEO Local';
            $lines[] = str_repeat('-', 60);
            $lines[] = sprintf('Scor SEO local: %d/100', (int)($localPercent ?? 0));
            $lines[] = sprintf('Puncte local: %d/%d', (int)($localPoints ?? 0), (int)($localMax ?? 0));
            $lines[] = $localCaption;
            $lines[] = '';

            if ($badChecksLocal) {
                $lines[] = 'Fix (SEO local):';
                foreach ($badChecksLocal as $c) {
                    $tip = class_exists('Advice') ? \Advice::tip((string)$c['id'], (string)($c['note'] ?? '')) : '';
                    $lines[] = '  - '.self::labelForText($c['id'], $c['note'], false);
                    if ($tip) {
                        $lines[] = '      Recomandare: '.$tip;
                    }
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

        $lines[] = str_repeat('=', 60);
        $lines[] = 'Raport generat automat de Novaweb Audit SEO One-Page.';
        $lines[] = 'Pentru ajutor la implementare, intră pe https://novaweb.ro/contact/.';
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
     * Mapare id verificare -> text scurt, modern, în română
     */
    private static function mapLabel(string $id): string
    {
        switch ($id) {
            // Conținut & UX
            case 'word_count_800':             return 'Dimensiune text (cuvinte) — recomandat ≥ 800';
            case 'intro_mentions_topic':       return 'Introducere care menționează subiectul principal';
            case 'h1_single':                  return 'Titlu principal (H1) unic pe pagină';
            case 'headings_hierarchy':         return 'Structură clară a subtitlurilor H2/H3';
            case 'lists_tables':               return 'Liste și tabele pentru scanare ușoară';
            case 'images_in_body':             return 'Imagini relevante în conținut';
            case 'img_alt_ratio_80':           return 'Atribute ALT pentru imagini (≥ 80% completate)';
            case 'lazyload_images':            return 'Lazy-load activ pentru imagini';
            case 'date_published':             return 'Dată de publicare vizibilă sau în schema';
            case 'date_modified':              return 'Dată de actualizare vizibilă sau în schema';
            case 'author_visible_or_schema':   return 'Autor vizibil sau definit în schema';

            // Structură & Indexare
            case 'indexable':                  return 'Pagină indexabilă (fără noindex în meta robots)';
            case 'canonical_present':          return 'Canonical prezent în pagină';
            case 'canonical_valid':            return 'Canonical valid (self / domeniu corect)';
            case 'url_clean':                  return 'URL curat, scurt și descriptiv';
            case 'internal_links_present':     return 'Linkuri interne relevante în conținut/pagină';
            case 'external_links_present':     return 'Linkuri externe către surse de încredere';
            case 'meta_robots_ok':             return 'Meta robots configurat corect';
            case 'html_valid':                 return 'HTML fără erori majore de structură';
            case 'image_dimensions_defined':   return 'Imagini cu dimensiuni (width/height) definite';
            case 'fonts_preload':              return 'Fonturi principale preîncărcate (preload)';
            case 'cls_risky_elements':         return 'Elemente cu risc de CLS controlat';

            // Metadate & rich snippets
            case 'title_length_ok':            return 'Titlu (title) cu lungime optimă';
            case 'meta_description_ok':        return 'Meta description cu lungime optimă';
            case 'og_minimal':                 return 'Open Graph complet (titlu, descriere, imagine, URL)';
            case 'schema_article_recommended': return 'Schema Article / BlogPosting implementată';
            case 'faq_schema_present':         return 'Schema FAQPage pentru întrebări frecvente';
            case 'schema_breadcrumbs':         return 'Schema BreadcrumbList (firimituri)';
            case 'schema_image_required_fields': return 'Imagini din schema cu width/height';

            // Localizare RO (limbă)
            case 'lang_ro':                    return 'Limbă setată pe română (lang="ro" / "ro-RO")';
            case 'og_locale_or_inLanguage_ro': return 'Localizare „ro-RO” în OG sau inLanguage';
            case 'date_format_ro':             return 'Date afișate în format românesc';
            case 'hreflang_pairs':             return 'Hreflang configurat pentru versiuni lingvistice';

            // SEO local extins
            case 'local_tel_click':            return 'Telefon click-to-call (link tel:)';
            case 'local_tel_prefix_local':     return 'Telefon cu prefix local românesc corect';
            case 'local_address_visible':      return 'Adresă fizică clară și vizibilă în pagină';
            case 'local_directions_link':      return 'Link „Direcții” către Google Maps / Waze';
            case 'local_opening_hours':        return 'Program / orar afișat sau în schema';
            case 'local_schema_localbusiness': return 'Schema LocalBusiness implementată';
            case 'local_schema_postal':        return 'Schema PostalAddress completă';
            case 'local_schema_tel':           return 'Telefon definit în schema LocalBusiness';
            case 'local_schema_geo':           return 'Coordonate geografice (lat/long) în schema';
            case 'local_schema_sameas':        return 'Legături sameAs / hasMap către profiluri și hartă';
            case 'local_schema_area':          return 'Arie deservită (areaServed / serviceArea)';
            case 'local_schema_rating':        return 'Recenzii și rating (aggregateRating / review)';
            case 'local_city_detected':        return 'Orașul afacerii detectat în pagină';
            case 'local_city_in_title':        return 'Orașul inclus în titlu (title)';
            case 'local_city_in_h1':           return 'Orașul inclus în titlul principal (H1)';
            case 'local_city_in_slug':         return 'Orașul inclus în URL / slug';
            case 'local_city_in_intro':        return 'Orașul menționat în introducere';
            case 'local_map_embed':            return 'Hartă embed (Google Maps) în pagină';
            case 'local_alt_has_city':         return 'ALT imagini care includ numele orașului';
            case 'local_locator':              return 'Store locator / pagini separate pentru locații';
            case 'local_whatsapp':             return 'Buton WhatsApp click-to-chat';

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
            $fullLabel = $label;
            if ($note) {
                $fullLabel .= ' — '.$note;
            }

            if ($ok) {
                $badgeBg   = '#dcfce7';
                $badgeText = '#166534';
                $badgeLabel= 'OK';
            } else {
                $badgeBg   = '#fee2e2';
                $badgeText = '#b91c1c';
                $badgeLabel= 'Fix';
            }

            $tip = '';
            if (class_exists('Advice')) {
                try {
                    $tip = \Advice::tip($id, (string)$note);
                } catch (\Throwable $e) {
                    $tip = '';
                }
            }

            $rows[] = sprintf(
                '<tr>
                   <td style="padding:6px 8px 4px 0;font-size:11px;width:30px;white-space:nowrap;">
                     <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:%s;color:%s;font-size:10px;font-weight:600;text-transform:uppercase;">%s</span>
                   </td>
                   <td style="padding:6px 8px 4px 0;font-size:12px;color:#111827;">%s</td>
                   <td style="padding:6px 0 4px 0;font-size:11px;color:#4b5563;">%s</td>
                 </tr>',
                $badgeBg,
                $badgeText,
                $badgeLabel,
                self::eHtml($fullLabel),
                $tip ? self::eHtml($tip) : '&nbsp;'
            );
        }

        if (!$rows) {
            return '<div style="font-size:12px;color:#6b7280;">Nicio verificare relevantă pentru această secțiune.</div>';
        }

        return '<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;background:#ffffff;border-radius:12px;border:1px solid #e5e7eb;padding:6px 8px 4px 8px;">'
            . implode('', $rows)
            . '</table>';
    }
}
