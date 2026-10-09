

```css
:root{
--col-danger           : #f01313;
--col-danger-secondary : #fdd8d0;
--col-info             : #0000ff;
--col-info-secondary   : #d0d8fd;
--col-note             : #0b9d14;
--col-note-secondary   : #d0f0da;        
--col-warning          : #f79503;
--col-warning-secondary: #fdf3d0;
}

/* Définition des thèmes */
:root[data-theme="marine"] {
  --bg-main: #f0f4f8;
  --bg-header: #1e293b;
  --bg-sidebar: #0f172a;
  --bg-card: #ffffff;
  --bg-nav: #ffffff;  
  --text-main: #334155;
  --text-header: #f8fafc;
  --text-sidebar: #f8fafc;
  --accent: #2563eb;
  --accent-hover: #1d4ed8;
  --border-color: #cbd5e1;


  /* ── Espacements ─────────────────────────────────────────────────────────── */
  --wb-gap:        1rem;
  --wb-padding:    1rem;
  --wb-radius:     8px;
  --wb-shadow:     0 2px 12px rgba(0,0,0,.08);

  /* ── Panel ───────────────────────────────────────────────────────────────── */
  --wb-panel-bg:          var(--wb-white);
  --wb-panel-header-bg:   var(--wb-navy);
  --wb-panel-header-fg:   var(--wb-yellow);
  --wb-panel-header-pad:  .875rem 1rem;
  --wb-panel-body-pad:    1rem;
  --wb-left-width:        360px;
}

:root[data-theme="nature"] {
  --bg-main: #f4f7f4;
  --bg-header: #1b382b;
  --bg-sidebar: #0f2319;
  --bg-card: #ffffff;
  --bg-nav: #ffffff;
  --text-main: #27352a;
  --text-header: #f0fdf4;
  --text-sidebar: #f0fdf4;
  --accent: #16a34a;
  --accent-hover: #15803d;
  --border-color: #bbf7d0;

  /* ── Espacements ─────────────────────────────────────────────────────────── */
  --wb-gap:        1rem;
  --wb-padding:    1rem;
  --wb-radius:     8px;
  --wb-shadow:     0 2px 12px rgba(0,0,0,.08);

  /* ── Panel ───────────────────────────────────────────────────────────────── */
  --wb-panel-bg:          var(--wb-white);
  --wb-panel-header-bg:   var(--wb-navy);
  --wb-panel-header-fg:   var(--wb-yellow);
  --wb-panel-header-pad:  .875rem 1rem;
  --wb-panel-body-pad:    1rem;
  --wb-left-width:        360px;  
}
```
