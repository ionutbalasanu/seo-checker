<!doctype html>
<html lang="ro">

<head>
  <meta charset="utf-8">
  <title>SEO Checker — Novaweb</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
  :root {
    --brand-green: #22c55e;
    --brand-amber: #fbbf24;
    --brand-red: #ef4444;
    --ring-track: #e2e8f0;
    --text-strong: #0f172a;
    --muted: #64748b;
  }

  /* Background general + text implicit */
  body {
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
      "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    /* important: fără gradient, ca să nu se vadă chenarul albastru în iframe */
    background: transparent;
    color: #111827;
  }

  .hero {
    padding: 48px 0 24px;
  }

  .card {
    border: 1px solid rgba(148, 163, 184, 0.45);
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.28);
    border-radius: 22px;
  }

  .kpi {
    font-size: 2.25rem;
    line-height: 1;
    color: var(--text-strong);
  }

  .kv {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 0.45rem 0.8rem;
    margin-bottom: 4px;
    border-radius: 6px;
    border: 1px solid transparent;
    font-size: 0.9rem;
  }

  .kv .note {
    color: #4b5563;
    font-size: 0.85rem;
  }

  .kv-ok {
    background: rgba(22, 163, 74, 0.08);
    border-color: rgba(22, 163, 74, 0.25);
    color: #14532d;
  }

  .kv-warn {
    background: rgba(245, 158, 11, 0.08);
    border-color: rgba(245, 158, 11, 0.25);
    color: #92400e;
  }

  .kv-bad {
    background: rgba(239, 68, 68, 0.08);
    border-color: rgba(239, 68, 68, 0.25);
    color: #991b1b;
  }

  .pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 999px;
    font-size: 13px;
    flex-shrink: 0;
  }

  .pill.ok {
    background: #22c55e;
    color: #ffffff;
  }

  .pill.bad {
    background: #ef4444;
    color: #ffffff;
  }

  .pill.neutral {
    background: rgba(148, 163, 184, 0.4);
    color: #111827;
  }


  /* Donut modern */
  .donut-card {
    padding: 18px;
  }

  .donut-wrap {
    display: flex;
    align-items: center;
    gap: 22px;
  }

  .radial-progress {
    position: relative;
    width: 140px;
    height: 140px;
  }

  .radial-progress svg {
    width: 140px;
    height: 140px;
    overflow: visible;
  }

  .rp-track {
    stroke: var(--ring-track);
    stroke-width: 12;
    fill: none;
  }

  .rp-bar {
    stroke-width: 12;
    fill: none;
    stroke-linecap: round;
    filter: url(#rp-glow);
  }

  .rp-num {
    font: 700 34px/1 ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto,
      "Helvetica Neue", Arial;
    fill: var(--text-strong);
  }

  .rp-sub {
    font: 600 12px/1 ui-sans-serif, system-ui;
    fill: var(--muted);
  }

  .legend {
    display: flex;
    gap: 22px;
    align-items: center;
    flex-wrap: wrap;
  }

  .legend .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
  }

  .dot.pass {
    background: #22c55e;
  }

  .dot.warn {
    background: #fbbf24;
  }

  .dot.fail {
    background: #ef4444;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(6px);
    }

    to {
      opacity: 1;
      transform: none;
    }
  }

  .donut-card {
    animation: fadeInUp 0.4s ease both;
  }

  /* full-page loader */
  .page-loader {
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at top, rgba(37, 99, 235, 0.18), transparent 55%),
      rgba(15, 23, 42, 0.92);
    display: none;
    align-items: top;
    justify-content: center;
    z-index: 9999;
  }

  .spinner {
    width: 78px;
    height: 78px;
    border-radius: 50%;
    border: 6px solid rgba(15, 23, 42, 0.4);
    border-top-color: #2563eb;
    animation: spin 0.9s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  /* Local overlay blur */
  .lock-wrapper {
    position: relative;
  }

  .lock-blur {
    filter: blur(4px);
    pointer-events: none;
  }

  .local-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .local-overlay .panel {
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    background: rgba(255, 255, 255, 0.96);
    border: 1px solid rgba(148, 163, 184, 0.4);
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.28);
    border-radius: 14px;
    padding: 18px;
    max-width: 620px;
    width: 100%;
  }

  .col-12+.col-12 {
    margin-top: 0.75rem;
  }

  /* Top form styling */
  .top-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: stretch;
  }

  .url-wrap {
    flex: 1 1 280px;
  }

  .url-input {
    border-radius: 999px;
    border: 0;
    padding: 0.9rem 1.4rem;
    font-size: 1.05rem;
    background: #eef2ff;
    box-shadow: inset 0 0 0 1px #c7d2fe;
  }

  .url-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.6);
    background: #f9fafb;
  }

  .type-toggle {
    display: inline-flex;
    align-items: center;
    padding: 0.14rem;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.03);
    box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.55);
    gap: 2px;
  }

  .toggle-pill {
    border: none;
    background: transparent;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.18s ease;
    white-space: nowrap;
  }

  .toggle-pill.active {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.45);
  }

  .btn-main {
    border-radius: 999px;
    padding: 0.8rem 1.8rem;
    font-weight: 600;
    font-size: 1rem;
    background: linear-gradient(135deg, #2563eb, #38bdf8);
    border: none;
    color: #ffffff;
    box-shadow: 0 18px 40px rgba(37, 99, 235, 0.6);
    white-space: nowrap;
  }

  .btn-main:hover {
    background: linear-gradient(135deg, #1d4ed8, #0ea5e9);
    color: #ffffff;
  }

  /* Category bar chart */
  .cat-card {
    padding: 18px;
  }

  .cat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .cat-header-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
  }

  .cat-sub {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .cat-rows {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    margin-top: 0.35rem;
  }

  .cat-row {
    display: grid;
    grid-template-columns: 230px 80px 1fr;
    align-items: center;
    column-gap: 12px;
    font-size: 0.85rem;
  }

  .cat-label {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-weight: 500;
    color: #111827;
  }

  .cat-icon {
    width: 20px;
    height: 20px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #f9fafb;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.3);
  }

  .cat-icon svg {
    width: 11px;
    height: 11px;
  }

  .cat-icon-content {
    background: linear-gradient(135deg, #4f46e5, #22d3ee);
  }

  .cat-icon-structure {
    background: linear-gradient(135deg, #22c55e, #16a34a);
  }

  .cat-icon-meta {
    background: linear-gradient(135deg, #f97316, #fb923c);
  }

  .cat-icon-locale {
    background: linear-gradient(135deg, #0ea5e9, #6366f1);
  }

  .cat-icon-localSeo {
    background: linear-gradient(135deg, #a855f7, #ec4899);
  }

  .cat-value {
    font-variant-numeric: tabular-nums;
    color: #6b7280;
    font-size: 0.8rem;
    min-width: 72px;
  }

  .cat-bar {
    position: relative;
    flex: 1;
    height: 8px;
    border-radius: 999px;
    background: #e2e8f0;
    overflow: hidden;
  }

  .cat-fill {
    position: absolute;
    inset: 0;
    transform-origin: left center;
    transform: scaleX(0);
    background: linear-gradient(90deg, #4f46e5, #22d3ee);
    border-radius: inherit;
    transition: transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
  }

  .cat-row[data-level="high"] .cat-label {
    color: #0f172a;
  }

  .cat-row[data-level="medium"] .cat-label {
    color: #4b5563;
  }

  .cat-row[data-level="low"] .cat-label {
    color: #6b7280;
  }

  /* Impact grouping */
  .impact-title {
    margin-top: 0.55rem;
    margin-bottom: 0.15rem;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #9ca3af;
  }

  .impact-title:first-of-type {
    margin-top: 0;
  }

  .impact-high {
    color: #16a34a;
  }

  .impact-medium {
    color: #f59e0b;
  }

  .impact-low {
    color: #6b7280;
  }

  /* Novaweb badge + info strip */
  .info-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.3rem;
  }

  .info-left {
    font-size: 0.8rem;
    color: #6b7280;
  }

  .score-link {
    font-weight: 500;
    text-decoration: none;
    color: #2563eb;
  }

  .score-link:hover {
    text-decoration: underline;
    color: #1d4ed8;
  }

  .novaweb-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    background: #020617;
    color: #e5edff;
    font-size: 0.75rem;
    text-decoration: none;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.75);
  }

  .novaweb-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: linear-gradient(135deg, #22c55e, #22d3ee);
  }

  /* Modal scor */
  .score-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
  }

  .score-modal.active {
    display: flex;
  }

  .score-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.76);
    backdrop-filter: blur(6px);
  }

  .score-modal-dialog {
    position: relative;
    background: rgba(255, 255, 255, 0.96);
    border-radius: 18px;
    padding: 22px 22px 18px;
    max-width: 640px;
    width: 100%;
    box-shadow: 0 24px 55px rgba(15, 23, 42, 0.45);
    z-index: 1;
  }

  .score-modal-title {
    margin-bottom: 0.35rem;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
  }

  .score-modal-body {
    font-size: 0.9rem;
    color: #4b5563;
  }

  .score-modal-body h6 {
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-top: 1rem;
    margin-bottom: 0.15rem;
    color: #6b7280;
  }

  .score-modal-body ul {
    padding-left: 1.1rem;
    margin-bottom: 0.4rem;
  }

  .score-modal-body li {
    margin-bottom: 0.15rem;
  }

  .score-modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    border: none;
    background: transparent;
    padding: 4px;
    border-radius: 999px;
    color: #6b7280;
    cursor: pointer;
  }

  .score-modal-close:hover {
    background: #f3f4f6;
    color: #111827;
  }

  @media (max-width: 767.98px) {
    .hero {
      padding: 32px 0 18px;
    }

    /* FORM pe mobil: coloane + full width + elemente puțin mai mici */
    .top-row {
      flex-direction: column;
      align-items: stretch;
    }

    .url-wrap {
      flex: 1 1 auto;
      width: 100%;
    }

    .url-input {
      font-size: 0.95rem;
      padding: 0.75rem 1.1rem;
    }

    /* container-ele cu toggle + buton să fie full width */
    .top-row > .d-flex {
      width: 100%;
      justify-content: stretch;
    }

    .type-toggle {
      width: 100%;
      justify-content: space-between;
    }

    .toggle-pill {
      flex: 1 1 0;
      text-align: center;
      padding: 0.5rem 0.6rem;
      font-size: 0.85rem;
    }

    .btn-main {
      display: block;
      width: 100%;
      text-align: center;
      white-space: normal;
      padding: 0.75rem 1.2rem;
      font-size: 0.95rem;
      box-shadow: 0 12px 26px rgba(37, 99, 235, 0.5);
    }

    .donut-wrap {
      flex-direction: column;
      align-items: flex-start;
    }

    .radial-progress {
      margin: auto;
    }

    .cat-row {
      grid-template-columns: minmax(0, 1fr);
      row-gap: 4px;
    }

    .cat-value {
      order: 3;
    }
  }
  .container {
    max-width: 1360px !important;
  }
</style>

</head>

<body>

  <!-- Loader pe toată pagina -->
  <div id="pageLoader" class="page-loader">
    <div class="text-center">
      <div class="spinner mx-auto mb-3"></div>
      <div class="text-muted">Analizăm pagina…</div>
    </div>
  </div>

  <div class="container hero">
    <div class="card p-4">
      <!-- FORM -->
      <form id="scoreForm" class="top-row">
        <div class="url-wrap">
          <input id="urlInput" name="url" type="text" class="form-control form-control-lg url-input"
            placeholder="https://exemplu.ro/articol" required>
        </div>

        <div class="d-flex align-items-center gap-2">
          <div class="type-toggle" role="radiogroup" aria-label="Tip pagină">
            <button type="button" class="toggle-pill active" data-type="article">Articol obișnuit</button>
            <button type="button" class="toggle-pill" data-type="local">Pagină locală</button>
          </div>
        </div>

        <div class="d-flex align-items-center">
          <button class="btn-main" type="submit">Calculează scor</button>
        </div>
      </form>

      <!-- REZULTAT -->
      <div class="mt-4">
        <div id="result" class="row g-3"></div>
      </div>

      <pre id="debugJson" class="bg-white p-3 rounded small text-body mt-3" style="display:none"></pre>
    </div>
  </div>

  <!-- Modal: Cum calculăm scorul -->
  <div id="scoreModal" class="score-modal" aria-hidden="true">
    <div class="score-modal-backdrop"></div>
    <div class="score-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="scoreModalTitle">
      <button type="button" class="score-modal-close" id="closeScoreModal" aria-label="Închide">
        &times;
      </button>
      <div class="score-modal-title" id="scoreModalTitle">Cum calculăm scorul</div>
      <div class="score-modal-body">
        <p>
          Scorul SEO este gândit ca un <strong>audit rapid, orientat pe conținut</strong>, nu ca un scor de „PageSpeed”.
          Fiecare secțiune are o pondere maximă, iar verificările importante cântăresc mai mult.
        </p>

        <h6>Structura scorului global (100 puncte)</h6>
        <ul>
          <li><strong>Conținut &amp; Media (40p)</strong> – lungimea articolului, structura H1/H2, imagini, ALT, video,
            date, autor.</li>
          <li><strong>Structură &amp; Indexare (25p)</strong> – indexabilitate, canonical, curățenia URL-ului, linkuri
            interne/extern.</li>
          <li><strong>Metadate &amp; Rich Snippets (20p)</strong> – titlu, meta description, Open Graph, Twitter Card,
            schema Article.</li>
          <li><strong>Localizare RO (15p)</strong> – limbă „ro”, formate de dată românești, hreflang, locale corecte.
          </li>
        </ul>

        <h6>SEO local extins</h6>
        <p>
          Pentru paginile marcate ca <strong>„Pagină locală”</strong>, calculăm și un sub-scor de SEO local (max. 30
          puncte):
        </p>
        <ul>
          <li>Telefon click-to-call, prefix românesc, adresă vizibilă.</li>
          <li>Schema <code>LocalBusiness</code> cu <code>PostalAddress</code>, coordonate, sameAs/hasMap, areaServed
            etc.</li>
          <li>Prezența orașului în title, H1, URL, introducere și în ALT-urile imaginilor.</li>
          <li>Elemente de UX local: link „Direcții”, hartă embed, program, WhatsApp, locator cu mai multe locații.</li>
        </ul>

        <p>
          La paginile locale, scorul final combină <strong>80% scor global</strong> cu <strong>20% scor SEO
            local</strong>,
          pentru a evidenția avantajul unei implementări locale complete.
        </p>
      </div>
    </div>
  </div>

<script>
  (function () {
    const form = document.getElementById('scoreForm');
    const result = document.getElementById('result');
    const debug = document.getElementById('debugJson');
    const pageLoader = document.getElementById('pageLoader');

    let currentContext = 'article';
    let lastUrl = '';

    // Normalizează URL-ul: dacă nu are http/https, adaugă https://
    function normalizeUrl(input) {
      const url = (input || '').trim();
      if (!url) return '';
      // dacă deja are schemă, îl lăsăm așa
      if (/^https?:\/\//i.test(url)) return url;
      return 'https://' + url;
    }

    // Toggle tip pagină (buton stil switch)
    document.querySelectorAll('.toggle-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.toggle-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentContext = btn.dataset.type === 'local' ? 'local' : 'article';
      });
    });

    function icon(state) {
      if (state === true) return `<span class="pill ok">✓</span>`;
      if (state === false) return `<span class="pill bad">✕</span>`;
      return `<span class="pill neutral">–</span>`;
    }

    function row(label, state, note) {
      const base = state === true ? 'kv kv-ok' : (state === false ? 'kv kv-bad' : 'kv kv-warn');
      return `<div class="${base}">
      <div>${label}${note ? ` <span class="note">— ${escapeHtml(note)}</span>` : ''}</div>
      <div>${icon(state)}</div>
    </div>`;
    }

    function get(checks, id) { return checks.find(x => x.id === id) || null; }
    function stateOf(checks, id) { const it = get(checks, id); return it ? (!!it.ok) : null; }
    function noteOf(checks, id) { const it = get(checks, id); return it && it.note ? it.note : ''; }
    function escapeHtml(s) { return String(s ?? '').replace(/[&<>\"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])); }

    /* ------- Donut modern (gradient + animație) ------- */
    function donut(score, pass, warn, fail) {
      const pct = Math.max(0, Math.min(100, score | 0));
      const id = 'rp-' + Math.random().toString(36).slice(2);
      const R = 56;
      const C = 2 * Math.PI * R;

      return `
      <div class="card donut-card">
        <div class="donut-wrap">
          <div class="radial-progress" id="${id}" data-score="${pct}" data-circ="${C}">
            <svg viewBox="0 0 160 160" aria-hidden="true">
              <defs>
                <linearGradient id="${id}-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#6366f1"/>
                  <stop offset="100%" stop-color="#22d3ee"/>
                </linearGradient>
                <filter id="rp-glow" x="-50%" y="-50%" width="200%" height="200%">
                  <feGaussianBlur stdDeviation="1.6" result="coloredBlur"/>
                  <feMerge>
                    <feMergeNode in="coloredBlur"/>
                    <feMergeNode in="SourceGraphic"/>
                  </feMerge>
                </filter>
              </defs>

              <circle class="rp-track" cx="80" cy="80" r="${R}"></circle>

              <circle class="rp-bar" cx="80" cy="80" r="${R}"
                      stroke="url(#${id}-grad)"
                      stroke-dasharray="${C}"
                      stroke-dashoffset="${C}"
                      transform="rotate(-90 80 80)"></circle>

              <text x="80" y="80" text-anchor="middle" dominant-baseline="central" class="rp-num">
                <tspan id="${id}-num">0</tspan>
              </text>
              <text x="80" y="104" text-anchor="middle" class="rp-sub">/ 100</text>
            </svg>
          </div>

          <div class="legend">
            <span><span class="dot pass"></span><strong>${pass}</strong> Passed</span>
            <span><span class="dot warn"></span><strong>${warn}</strong> Warnings</span>
            <span><span class="dot fail"></span><strong>${fail}</strong> Failed</span>
          </div>
        </div>
      </div>`;
    }

    function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
    function animateDonuts() {
      document.querySelectorAll('.radial-progress').forEach(el => {
        const score = +el.dataset.score || 0;
        const C = +el.dataset.circ || 1;
        const bar = el.querySelector('.rp-bar');
        const num = el.querySelector('.rp-num tspan');
        if (!bar || !num) return;

        const duration = 1400;
        const targetOffset = C * (1 - score / 100);
        const t0 = performance.now();

        function frame(now) {
          const t = Math.min(1, (now - t0) / duration);
          const e = easeOutCubic(t);
          const currentOffset = C - (C - targetOffset) * e;
          const currentNum = Math.round(score * e);
          bar.style.strokeDashoffset = String(currentOffset);
          num.textContent = currentNum;
          if (t < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
      });
    }

    /* ------- Category bar chart (inclusiv SEO local) ------- */
    function categoryBars(b, localInfo, context) {
      const max = { content: 40, structure: 25, signals: 20, locale: 15 };
      const val = {
        content: b.content ?? 0,
        structure: b.structure ?? 0,
        signals: b.signals ?? 0,
        locale: b.locale ?? 0
      };
      const pct = {
        content: Math.round((val.content / max.content) * 100) || 0,
        structure: Math.round((val.structure / max.structure) * 100) || 0,
        signals: Math.round((val.signals / max.signals) * 100) || 0,
        locale: Math.round((val.locale / max.locale) * 100) || 0
      };

      const localMax = localInfo && typeof localInfo.max === 'number' ? localInfo.max : 30;
      const localVal = localInfo && typeof localInfo.points === 'number' ? localInfo.points : 0;
      const localPct = localMax ? Math.round((localVal / localMax) * 100) : 0;

      function level(p) {
        if (p >= 80) return 'high';
        if (p >= 50) return 'medium';
        return 'low';
      }

      const localLabelSuffix = context === 'local' ? '' : ' (doar raport)';
      const localHelper = context === 'local'
        ? `${localVal}/${localMax} — inclus în scor`
        : `${localVal}/${localMax} — util pentru paginile locale`;

      return `
      <div class="col-12">
        <div class="card cat-card">
          <div class="cat-header">
            <div>
              <div class="cat-header-title">Distribuția scorului pe categorii</div>
              <div class="cat-sub">Vezi rapid unde pierzi cele mai multe puncte, inclusiv pe SEO local.</div>
            </div>
          </div>
          <div class="cat-rows">
            <div class="cat-row" data-pct="${pct.content}" data-level="${level(pct.content)}">
              <div class="cat-label">
                <span class="cat-icon cat-icon-content">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7h14M4 12h10M4 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </span>
                <span>Conținut &amp; media</span>
              </div>
              <div class="cat-value">${val.content}/${max.content}</div>
              <div class="cat-bar"><div class="cat-fill"></div></div>
            </div>

            <div class="cat-row" data-pct="${pct.structure}" data-level="${level(pct.structure)}">
              <div class="cat-label">
                <span class="cat-icon cat-icon-structure">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 7h4v4H6zM14 7h4v4h-4zM10 13h4v4h-4zM8 9h8M12 11v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span>Structură &amp; indexare</span>
              </div>
              <div class="cat-value">${val.structure}/${max.structure}</div>
              <div class="cat-bar"><div class="cat-fill"></div></div>
            </div>

            <div class="cat-row" data-pct="${pct.signals}" data-level="${level(pct.signals)}">
              <div class="cat-label">
                <span class="cat-icon cat-icon-meta">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 4l2.09 4.24L19 9.27l-3.3 3.22.78 4.54L12 15.77l-4.48 2.36.78-4.54L5 9.27l4.91-.73L12 4z" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span>Metadate &amp; rich snippets</span>
              </div>
              <div class="cat-value">${val.signals}/${max.signals}</div>
              <div class="cat-bar"><div class="cat-fill"></div></div>
            </div>

            <div class="cat-row" data-pct="${pct.locale}" data-level="${level(pct.locale)}">
              <div class="cat-label">
                <span class="cat-icon cat-icon-locale">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 4a6 6 0 0 1 6 6c0 4-6 10-6 10S6 14 6 10a6 6 0 0 1 6-6z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="12" cy="10" r="2" fill="currentColor"/>
                  </svg>
                </span>
                <span>Localizare RO</span>
              </div>
              <div class="cat-value">${val.locale}/${max.locale}</div>
              <div class="cat-bar"><div class="cat-fill"></div></div>
            </div>

            <div class="cat-row" data-pct="${localPct}" data-level="${level(localPct)}">
              <div class="cat-label">
                <span class="cat-icon cat-icon-localSeo">
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 19V9l5-3 5 3v10M3 19h18M10 19v-5h4v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span>SEO local${localLabelSuffix}</span>
              </div>
              <div class="cat-value">${localVal}/${localMax}</div>
              <div class="cat-bar"><div class="cat-fill"></div></div>
            </div>
          </div>
          <div class="cat-sub mt-2">
            ${localHelper}
          </div>
        </div>
      </div>`;
    }

    function animateCategoryBars() {
      document.querySelectorAll('.cat-row').forEach(row => {
        const pct = Math.max(0, Math.min(100, parseInt(row.dataset.pct || '0', 10)));
        const fill = row.querySelector('.cat-fill');
        if (!fill) return;
        requestAnimationFrame(() => {
          fill.style.transform = 'scaleX(' + (pct / 100) + ')';
        });
      });
    }

    /* ------- Render UI ------- */
    function render(json) {
      if (!json || !json.score) {
        result.innerHTML = '<div class="alert alert-danger">Răspuns invalid</div>';
        return;
      }
      const b = json.score.breakdown || {};
      const checks = json.score.checks || [];
      const total = json.score.total || 0;
      const context = json.score.context || 'article';
      const localInfo = json.score.local || null;

      let pass = 0, warn = 0, fail = 0;
      for (const c of checks) {
        if (typeof c.ok === 'boolean') {
          if (c.ok) pass++; else fail++;
        }
      }

      const localBadgeInline = (localInfo && context === 'local')
        ? ` · SEO local: <strong>${localInfo.points}/${localInfo.max}</strong> (${localInfo.percent}%)`
        : '';

      result.innerHTML = `
      <div class="col-12">
        ${donut(total, pass, 0, fail)}
        <div class="info-strip">
          <div class="info-left">
            Tip pagină: <strong>${context === 'local'
          ? 'Pagină locală (SEO local activ în scor)'
          : 'Articol obișnuit (SEO local doar informativ)'}</strong>${localBadgeInline}
            · <a href="#" id="openScoreModal" class="score-link">Află cum calculăm scorul</a>
          </div>
          <a href="https://novaweb.ro" target="_blank" rel="noopener" class="novaweb-badge">
            <span class="novaweb-dot"></span>
            Novaweb SEO Checker
          </a>
        </div>
      </div>

      ${categoryBars(b, localInfo, context)}
      ${cardContent(b, checks)}
      ${cardStruct(b, checks)}
      ${cardMeta(b, checks)}
      ${cardLocal(b, checks, localInfo, context)}
    `;

      animateDonuts();
      animateCategoryBars();
      bindLocalEmail(lastUrl);
      bindScoreModal();
    }

    function cardContent(b, checks) {
      return `
      <div class="col-12">
        <div class="card p-3 h-100">
          <div class="h6 mb-1">Conținut &amp; Media</div>
          <div class="fs-5 fw-semibold mb-2">${b.content ?? 0}/40</div>

          <div class="impact-title impact-high">Impact mare</div>
          ${row('Cuvinte ≥ 800', stateOf(checks, 'word_count_800'), noteOf(checks, 'word_count_800'))}
          ${row('H1 unic', stateOf(checks, 'h1_single'), noteOf(checks, 'h1_single'))}
          ${row('Ierarhie H2/H3 bună', stateOf(checks, 'headings_hierarchy'), noteOf(checks, 'headings_hierarchy'))}
          ${row('Imagini în corp', stateOf(checks, 'images_in_body'), noteOf(checks, 'images_in_body'))}
          ${row('ALT ≥ 80%', stateOf(checks, 'img_alt_ratio_80'), noteOf(checks, 'img_alt_ratio_80'))}

          <div class="impact-title impact-medium">Impact mediu</div>
          ${row('Intro menționează tema', stateOf(checks, 'intro_mentions_topic'), noteOf(checks, 'intro_mentions_topic'))}
          ${row('Liste/Tabele în conținut', stateOf(checks, 'lists_tables'), noteOf(checks, 'lists_tables'))}
          ${row('Lazy-load la imagini', stateOf(checks, 'lazyload_images'), noteOf(checks, 'lazyload_images'))}
          ${row('Video/Iframe prezent', stateOf(checks, 'video_present'), '')}

          <div class="impact-title impact-low">Impact redus</div>
          ${row('Dată publicare', stateOf(checks, 'date_published'), noteOf(checks, 'date_published'))}
          ${row('Dată actualizare', stateOf(checks, 'date_modified'), noteOf(checks, 'date_modified'))}
          ${row('Autor vizibil/Schema', stateOf(checks, 'author_visible_or_schema'), noteOf(checks, 'author_visible_or_schema'))}
        </div>
      </div>`;
    }

    function cardStruct(b, checks) {
      return `
      <div class="col-12">
        <div class="card p-3 h-100">
          <div class="h6 mb-1">Structură &amp; Indexare</div>
          <div class="fs-5 fw-semibold mb-2">${b.structure ?? 0}/25</div>

          <div class="impact-title impact-high">Impact mare</div>
          ${row('Indexabil (fără noindex)', stateOf(checks, 'indexable'), '')}
          ${row('Canonical valid/self', stateOf(checks, 'canonical_valid'), noteOf(checks, 'canonical_valid'))}
          ${row('URL curat', stateOf(checks, 'url_clean'), noteOf(checks, 'url_clean'))}
          ${row('≥ 1 link intern', stateOf(checks, 'internal_links_present'), noteOf(checks, 'internal_links_present'))}

          <div class="impact-title impact-medium">Impact mediu</div>
          ${row('Canonical prezent', stateOf(checks, 'canonical_present'), noteOf(checks, 'canonical_present'))}
          ${row('≥ 1 link extern', stateOf(checks, 'external_links_present'), noteOf(checks, 'external_links_present'))}
        </div>
      </div>`;
    }

    function cardMeta(b, checks) {
      return `
      <div class="col-12">
        <div class="card p-3 h-100">
          <div class="h6 mb-1">Metadate &amp; Rich Snippets</div>
          <div class="fs-5 fw-semibold mb-2">${b.signals ?? 0}/20</div>

          <div class="impact-title impact-high">Impact mare</div>
          ${row('Title lungime OK', stateOf(checks, 'title_length_ok'), noteOf(checks, 'title_length_ok'))}
          ${row('Meta description OK', stateOf(checks, 'meta_description_ok'), noteOf(checks, 'meta_description_ok'))}
          ${row('JSON-LD Article recomandat', stateOf(checks, 'schema_article_recommended'), noteOf(checks, 'schema_article_recommended'))}

          <div class="impact-title impact-medium">Impact mediu</div>
          ${row('Open Graph minim', stateOf(checks, 'og_minimal'), noteOf(checks, 'og_minimal'))}
          ${row('Twitter Card (large)', stateOf(checks, 'twitter_card_large'), noteOf(checks, 'twitter_card_large'))}
        </div>
      </div>`;
    }

    // CARD Localizare RO + audit local (cu overlay)
    function cardLocal(b, checks, localInfo, context) {
      const visible = `
      <div class="h6 mb-1">Localizare RO &amp; SEO local</div>
      <div class="fs-5 fw-semibold mb-2">${b.locale ?? 0}/15</div>

      <div class="impact-title impact-high">Impact mare</div>
      ${row('lang="ro"/"ro-RO"', stateOf(checks, 'lang_ro'), noteOf(checks, 'lang_ro'))}

      <div class="impact-title impact-medium">Impact mediu</div>
      ${row('OG locale / inLanguage „ro”', stateOf(checks, 'og_locale_or_inLanguage_ro'), noteOf(checks, 'og_locale_or_inLanguage_ro'))}
      ${row('Dată în format RO', stateOf(checks, 'date_format_ro'), noteOf(checks, 'date_format_ro'))}
      ${row('Hreflang (dacă există)', stateOf(checks, 'hreflang_pairs'), noteOf(checks, 'hreflang_pairs'))}
    `;
      const locked = `
      <hr class="my-2">

      <div class="impact-title impact-high">Impact mare</div>
      ${row('Telefon click-to-call', stateOf(checks, 'local_tel_click'), noteOf(checks, 'local_tel_click'))}
      ${row('Prefix local (+40 2x/3x)', stateOf(checks, 'local_tel_prefix_local'), noteOf(checks, 'local_tel_prefix_local'))}
      ${row('Schema LocalBusiness', stateOf(checks, 'local_schema_localbusiness'), noteOf(checks, 'local_schema_localbusiness'))}
      ${row('Schema PostalAddress', stateOf(checks, 'local_schema_postal'), noteOf(checks, 'local_schema_postal'))}
      ${row('Schema telephone', stateOf(checks, 'local_schema_tel'), noteOf(checks, 'local_schema_tel'))}
      ${row('Oraș detectat', stateOf(checks, 'local_city_detected'), noteOf(checks, 'local_city_detected'))}
      ${row('Oraș în Title', stateOf(checks, 'local_city_in_title'), noteOf(checks, 'local_city_in_title'))}
      ${row('Oraș în H1', stateOf(checks, 'local_city_in_h1'), noteOf(checks, 'local_city_in_h1'))}
      ${row('Oraș în URL slug', stateOf(checks, 'local_city_in_slug'), noteOf(checks, 'local_city_in_slug'))}
      ${row('Oraș în intro', stateOf(checks, 'local_city_in_intro'), noteOf(checks, 'local_city_in_intro'))}

      <div class="impact-title impact-medium">Impact mediu</div>
      ${row('Adresă vizibilă', stateOf(checks, 'local_address_visible'), '')}
      ${row('Link „Direcții” (hărți)', stateOf(checks, 'local_directions_link'), noteOf(checks, 'local_directions_link'))}
      ${row('Program/Orar', stateOf(checks, 'local_opening_hours'), noteOf(checks, 'local_opening_hours'))}
      ${row('Schema geo (lat/long)', stateOf(checks, 'local_schema_geo'), noteOf(checks, 'local_schema_geo'))}
      ${row('sameAs/hasMap', stateOf(checks, 'local_schema_sameas'), noteOf(checks, 'local_schema_sameas'))}
      ${row('areaServed/serviceArea', stateOf(checks, 'local_schema_area'), noteOf(checks, 'local_schema_area'))}
      ${row('Embed hartă', stateOf(checks, 'local_map_embed'), noteOf(checks, 'local_map_embed'))}
      ${row('ALT imagini cu oraș', stateOf(checks, 'local_alt_has_city'), noteOf(checks, 'local_alt_has_city'))}

      <div class="impact-title impact-low">Impact redus</div>
      ${row('Recenzii / note (aggregateRating)', stateOf(checks, 'local_schema_rating'), noteOf(checks, 'local_schema_rating'))}
      ${row('Store locator / pagini locații', stateOf(checks, 'local_locator'), noteOf(checks, 'local_locator'))}
      ${row('WhatsApp click-to-chat', stateOf(checks, 'local_whatsapp'), noteOf(checks, 'local_whatsapp'))}
    `;

      const localBadge = (localInfo && context === 'local')
        ? 'Pentru această pagină, SEO local intră în calculul scorului final.'
        : 'Pentru această pagină, SEO local este doar informativ (nu influențează scorul total).';

      return `
      <div class="col-12">
        <div class="card p-3 h-100 lock-wrapper">
          <div class="small text-muted mb-2">${localBadge}</div>
          <div>${visible}</div>
          <div id="localBlur" class="lock-blur">${locked}</div>

          <!-- Overlay cu consimțământ -->
          <div id="localOverlay" class="local-overlay">
            <div class="panel">
              <div class="mb-2 fw-semibold">Primește pe email audit-ul local complet</div>
              <div class="text-muted small mb-3">
                Îți trimitem raportul local (+ recomandări). Bifează acordurile pentru abonare.
              </div>

              <div class="row g-2">
                <div class="col-12">
                  <input id="localEmail" type="email" class="form-control" placeholder="email@exemplu.ro" required>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="consentNewsletter" checked>
                    <label class="form-check-label" for="consentNewsletter">
                      Sunt de acord să primesc newsletter cu articole, oferte și noutăți Novaweb.
                    </label>
                  </div>
                  <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="consentTerms" required>
                    <label class="form-check-label" for="consentTerms">
                      Am citit și accept
                      <a href="https://novaweb.ro/termeni-si-conditii/" target="_blank" rel="noopener">Termenii</a> și
                      <a href="https://novaweb.ro/politica-de-confidențialitate/" target="_blank" rel="noopener">Politica de confidențialitate</a>.
                    </label>
                  </div>
                </div>

                <div class="col-12 d-grid">
                  <button id="localSend" class="btn btn-main" type="button">Trimite raportul local</button>
                </div>
              </div>

              <div id="localMsg" class="small mt-2"></div>
            </div>
          </div>
        </div>
      </div>`;
    }

    // Fetch + loader cu durată minimă 1.5s
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const raw = (document.getElementById('urlInput').value || '').trim();
      if (!raw) { return; }

      const url = normalizeUrl(raw);
      if (!url) { return; }

      lastUrl = url;
      debug.style.display = 'none';

      const context = currentContext || 'article';

      pageLoader.style.display = 'flex';
      const t0 = (performance?.now?.() ?? Date.now());

      try {
        const resp = await fetch('/api/score', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ url, context })
        });
        const txt = await resp.text(); let json = null; try { json = JSON.parse(txt); } catch { }
        const elapsed = (performance?.now?.() ?? Date.now()) - t0;
        const pad = Math.max(0, 1500 - elapsed);
        setTimeout(() => { pageLoader.style.display = 'none'; render(json); }, pad);
      } catch (err) {
        pageLoader.style.display = 'none';
        result.innerHTML = '<div class="alert alert-danger">Eroare rețea.</div>';
      }
    });

    /* ------- Overlay email + consimțământ (blur NU se deblochează) ------- */
    function bindLocalEmail(currentUrl) {
      const localSend = document.getElementById('localSend');
      const localEmail = document.getElementById('localEmail');
      const consentNews = document.getElementById('consentNewsletter');
      const consentTerms = document.getElementById('consentTerms');
      const localMsg = document.getElementById('localMsg');
      const overlayPanel = localSend ? localSend.closest('.panel') : null;

      if (!localSend) return;

      function validEmail(s) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); }

      localSend.addEventListener('click', async () => {
        const email = (localEmail.value || '').trim();
        const cNews = !!(consentNews && consentNews.checked);
        const cTerms = !!(consentTerms && consentTerms.checked);

        if (!validEmail(email)) {
          localMsg.textContent = 'Te rog introdu un email valid.';
          localMsg.className = 'small mt-2 text-danger';
          return;
        }
        if (!cTerms) {
          localMsg.textContent = 'Trebuie să accepți Termenii și Politica de confidențialitate.';
          localMsg.className = 'small mt-2 text-danger';
          return;
        }

        localSend.disabled = true;
        localEmail.disabled = true;
        if (consentNews) consentNews.disabled = true;
        if (consentTerms) consentTerms.disabled = true;
        localMsg.textContent = 'Se trimite raportul…';
        localMsg.className = 'small mt-2 text-muted';

        try {
          const resp = await fetch('/api/email-report', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              url: currentUrl,
              email: email,
              first_name: '',
              consent_newsletter: cNews,
              consent_terms: cTerms
            })
          });
          const txt = await resp.text(); let j = null; try { j = JSON.parse(txt); } catch { }

          if (j && j.ok) {
            if (overlayPanel) {
              overlayPanel.innerHTML = `
              <div class="text-center">
                <div class="fw-semibold mb-1">Raportul de SEO local a fost trimis ✉️</div>
                <div class="text-muted small">
                  Verifică inbox-ul adresei <strong>${escapeHtml(email)}</strong>.<br>
                  Zona de detaliu rămâne blurată pentru a fi consultată direct în raport.
                </div>
              </div>`;
            } else {
              localMsg.textContent = 'Trimis! Verifică emailul pentru auditul local complet.';
              localMsg.className = 'small mt-2 text-success';
            }
          } else {
            localMsg.textContent = 'Nu am putut trimite emailul: ' + ((j && j.error) || 'eroare');
            localMsg.className = 'small mt-2 text-danger';
            localSend.disabled = false;
            localEmail.disabled = false;
            if (consentNews) consentNews.disabled = false;
            if (consentTerms) consentTerms.disabled = false;
          }
        } catch (err) {
          localMsg.textContent = 'Eroare rețea.';
          localMsg.className = 'small mt-2 text-danger';
          localSend.disabled = false;
          localEmail.disabled = false;
          if (consentNews) consentNews.disabled = false;
          if (consentTerms) consentTerms.disabled = false;
        }
      });
    }

    /* ------- Modal scor ------- */
    function bindScoreModal() {
      const modal = document.getElementById('scoreModal');
      const open = document.getElementById('openScoreModal');
      const close = document.getElementById('closeScoreModal');
      if (!modal || !open || !close) return;

      open.addEventListener('click', function (ev) {
        ev.preventDefault();
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
      });

      function hide() {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
      }

      close.addEventListener('click', hide);
      modal.querySelector('.score-modal-backdrop')?.addEventListener('click', hide);
    }

  })();
</script>

  <script>
    (function () {
      function sendHeight() {
        var h = document.documentElement.scrollHeight || document.body.scrollHeight;
        // trimitem înălțimea către pagina părinte
        window.parent.postMessage(
          {
            type: 'NW_SEO_HEIGHT',
            height: h
          },
          '*'
        );
      }

      window.addEventListener('load', function () {
        // la load
        sendHeight();
        // și apoi din 500 în 500 ms (ca să prindă și rezultatele după calcul)
        setInterval(sendHeight, 500);
      });
    })();
  </script>
</body>

</html>

</body>

</html>