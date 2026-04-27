# DynaByte Digital — Design System

A brand and UI system for **DynaByte Digital** (dynabytebd.com), a performance-marketing agency that helps local service businesses — EV repair, EV charging, HVAC, solar, and home services — generate leads through SEO and paid ads (Google + Meta).

This system was built specifically to support two paid-traffic landing pages:

1. **Facebook & Instagram Ads** — `/facebook-ads/`
2. **Google Ads** — `/google-ads/`

Both live under the **ClickWise Marketing** sub-brand inside DynaByte Digital, and share the same parent visual identity.

---

## Sources used

| Source | What it is |
|---|---|
| `uploads/DynaByte logo ...png` | Master logo (B-mark + wordmark) |
| `uploads/screencapture-dynabytebd-2026-04-25-23_59_51.png` | Full homepage screenshot of dynabytebd.com (very tall — 23,486px) |
| `uploads/Facebook_Ads_Cluster_Page_v2.docx` → `uploads/facebook_ads_structure.txt` | Section-by-section structure + copy for the Facebook Ads landing page |
| `uploads/Google_Ads_Cluster_Page.pdf` → `uploads/google_ads_structure.txt` | Section-by-section structure + copy for the Google Ads landing page |
| Live site (read once during research) | https://dynabytebd.com — used for tone, services list, testimonials, process |

---

## Brand at a glance

- **Who they help:** EV repair, EV charging, HVAC, solar, home services, dental, coaching — local service businesses across **US · UK · CA · AU**.
- **Promise:** structured systems that turn paid traffic and SEO into **leads, calls, and bookings** — not just impressions.
- **Voice:** plain, confident, anti-fluff. Calls out wasted spend and broken setups directly. "We" + "you" — no third person.
- **Visual DNA:** deep navy + electric blue + an orange "byte" pixel motif from the logo. Clean, premium, conversion-focused.

---

## Index — what's in this folder

```
README.md                  this file
SKILL.md                   skill manifest (Agent SKills compatible)
colors_and_type.css        all CSS vars: colors, type, spacing, shadows, motion
assets/                    logos, brand decoratives
preview/                   design-system preview cards (one HTML per concept)
ui_kits/
  landing_pages/           UI kit for the FB Ads + Google Ads landing pages
    index.html             interactive prototype (both pages, switchable)
    *.jsx                  modular section components
    README.md              kit notes
uploads/                   original source materials (don't ship)
```

---

## Content fundamentals

DynaByte's voice is **direct, plain, and confidently anti-fluff**. It calls out the bad behaviour of competitors and the symptoms of failing campaigns by name, then frames their own work as structured / connected / transparent in contrast.

**Copy principles**

- **First-person plural ("we") talking to second-person ("you").** Never third person, never "our clients" in body copy.
- **Sentence case headlines.** Title Case is reserved for nav and small labels (eyebrows, button text). Headlines read like spoken sentences: *"Google Ads campaigns that bring real enquiries — not just clicks."*
- **No emoji in body copy.** The footer uses 🇨🇦 / 🇧🇩 flag emoji for office addresses — that is the only emoji usage.
- **Em-dash + en-dash usage is heavy** — used for dramatic pauses and to attach a contrast clause. (See almost every headline.)
- **Short paragraphs, often two-line.** Most paragraphs are 2–4 sentences. Lots of one-sentence paragraphs for emphasis.
- **Concrete, not abstract.** "$1,000/month" beats "a reasonable budget." "2–4 weeks" beats "quickly."
- **British/Aus spelling slips through** ("optimise", "personalised", "enquiries") because they target UK + AU markets. Keep this — don't Americanise.
- **Bold sparingly.** Only on the 2–4 most important phrases per section, never on whole sentences.
- **Eyebrow + headline + paragraph** is the canonical section header. Eyebrow is a 2–4 word category in uppercase tracked-out caps; headline is a sentence-case statement; paragraph follows.

**Tone reference quotes** (from the FB/Google Ads docs and homepage):

> "Google Ads catches people who are ready to buy. Facebook Ads reaches the people who will be ready soon."

> "Most businesses are doing something… running ads, posting content, trying SEO. But nothing connects."

> "A lot of businesses have tried Google Ads. Some still run them. But between broad match keywords eating budget, ads pointing to a homepage instead of a focused landing page, and no proper conversion tracking — most campaigns spend money without a clear way to measure what's working."

> "No guesswork. No scattered efforts. Just strategy, execution, and real growth you can track."

The vibe: **operator-to-operator.** Business owner reading it should feel like they're being talked to by another operator, not pitched by a salesperson.

---

## Visual foundations

### Color
- **Navy `#0B1B3A`** is the trust anchor — used for top-of-funnel hero backgrounds, the wordmark, and primary text on light surfaces.
- **Electric blue `#1F7AEC`** is the action color — every button, every link, every iconified accent.
- **Orange `#F58A1F`** is used **sparingly** as the secondary accent — pulled directly from the "scattering byte" pixels in the logo. Reserved for: highlight underlines on key headline words, ticker/stat numbers, and the occasional contrast badge. Never use as a flat large background.
- **Layered surfaces:** navy / navy-2 / blue-deep on dark sections; paper / `--bg-soft` (#F6F8FC) on light sections. Never gray-on-gray.

### Type
- **Display + body:** Plus Jakarta Sans (display, headlines) + Inter (body). These are Google Fonts substitutions for whatever the live site uses (the homepage runs an Elementor theme — I could not extract the exact font stack from the codebase, so the closest neutral premium pairing was selected). **⚠ Substitution flagged for the user — please confirm or send the font files.**
- **H1 is loud:** `clamp(2.25rem, 4.4vw, 4rem)`, 800-weight, tight tracking, balanced wrap.
- **Highlighting words inside a headline** is a brand signature: a colored word (`--brand-blue` or `--brand-orange`) inside an otherwise-dark headline. Always 1–2 words max.
- **Eyebrow** is the brand's most identifiable type pattern: `0.8125rem`, 600 weight, 0.14em tracking, uppercase, **blue**.

### Spacing & layout
- Section vertical padding `--space-9` (96px) on desktop, `--space-7` (48px) on mobile.
- Container max width `1200px`, narrow `880px` for prose.
- Cards sit on a **light page** with a subtle shadow OR on a **dark page** with a 1px white-10% inner border + dark-2 fill. The two patterns are never mixed in one section.

### Backgrounds
- Hero uses a **radial dark gradient** (`--grad-hero`) — a mid-blue glow in the upper right falling off to near-black bottom-left. Echoes the logo's gradient.
- Light sections are flat **paper** or `--bg-soft`. No textures, no patterns, no full-bleed photography.
- Decorative elements: **soft blob shapes** (low-opacity blurred circles in `--brand-blue` / `--brand-orange`) anchor hero corners and stat sections — same pattern as the live site's `hero-shape-*.webp` decoratives.

### Motion
- Default duration **220ms**, easing `cubic-bezier(0.22, 1, 0.36, 1)` (ease-out) for entrances, and `cubic-bezier(0.65, 0, 0.35, 1)` (ease-in-out) for state changes.
- No bounces. No spring overshoots. Nothing "playful." Fades and short translates only.
- On scroll: short fade + 8px translate-y. Never parallax.

### Hover & press
- **Buttons:** primary darkens (`--action` → `--action-hover`) + 2px translate-y up + shadow grows from `--sh-2` to `--sh-glow-blue`.
- **Cards:** 1px translate-y up, shadow `--sh-2` → `--sh-3`, no color change.
- **Links:** color shifts darker; no underline animation.
- **Press:** translate-y returns to 0, shadow shrinks to `--sh-1`. No scale changes.

### Borders & shadows
- Borders are 1px, `--line-1` (#E1E5EE) on light, `--line-on-dark` (white 10%) on dark.
- Shadows are **navy-tinted**, never neutral gray. Defined in `--sh-1` → `--sh-4`.
- Two glow shadows for hero CTAs and floating stat cards: `--sh-glow-blue`, `--sh-glow-orange`.

### Corner radii
- Inputs / pills / buttons: `--r-md` (12px) for buttons, `--r-pill` (999px) for status badges and stat pills.
- Cards: `--r-xl` (24px). This is the dominant radius across the site.
- Hero blocks: `--r-2xl` (32px) for big illustrative containers.

### Transparency & blur
- Almost never. The only transparency uses are: (1) the dark-mode-on-dark white-alpha border, (2) protection gradients fading hero text down toward CTA, (3) decorative blob shapes at 30–50% opacity.
- No frosted glass. No `backdrop-filter`. Keep surfaces opaque — these are conversion pages.

### Imagery (when used)
- Real photos of clients (testimonial avatars) and screenshot mockups of campaign dashboards.
- Treatment: **warm-leaning, slightly desaturated**, no heavy grain. No cool/blue color grading. No b&w.
- Always inside a card with rounded corners — never full-bleed edge-to-edge.

### Cards (canonical)
- Background: `--bg-card` (paper) on light pages, `--bg-dark-2` on dark pages.
- Border: 1px `--line-1` on light, 1px `--line-on-dark` on dark.
- Radius: `--r-xl` (24px).
- Padding: `--space-6` (32px) desktop, `--space-5` (24px) mobile.
- Shadow: `--sh-2` resting, `--sh-3` on hover.
- Inside, a small icon or numbered badge (in a 48×48 rounded square with blue gradient bg) sits above the title.

---

## Iconography

DynaByte's live site uses a **mix of Elementor-bundled icons + lightweight social glyphs**. There is no proprietary icon font.

**Decision for this design system:** use **Lucide** via CDN (`https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js`). Lucide matches the brand's clean / line-based / 1.5px-stroke aesthetic and covers every concept the landing pages need (calendar, target, trending-up, message-square, shield-check, zap, search, chart, etc.).

Icon usage rules:
- **Stroke 1.75px**, color `--brand-blue` on light or `--fg-on-dark` on dark backgrounds.
- **Sized 20px or 24px inline**, 28–32px inside a tinted container.
- **Always inside a 48×48 rounded square (`--r-md`) with a soft blue tint background** when used as section/feature accents (`background: rgba(31,122,236,0.08); color: var(--brand-blue);`).
- **No emoji** anywhere on landing pages. (Footer office addresses on the main site use 🇨🇦/🇧🇩 — replicate only if matching that exact section.)
- **No unicode symbol icons** (✓, ★, etc.) except for ✓ in the comparison table — there it is intentional and stays.
- **Logo is master**, available in `assets/dynabyte-logo.png` (full lockup, color, on white).

If a future page needs an icon Lucide doesn't have, prefer Tabler Icons (also line-based, similar weight) before drawing custom SVG.

⚠ **Substitution flagged:** the live site's icon set was not extractable. Lucide is the closest match in stroke and style. If the user has a preferred icon library, swap the CDN reference at the top of `ui_kits/landing_pages/index.html`.

---

## What's missing / open questions for the user

1. **Fonts** — actual font files from the live site weren't accessible. Plus Jakarta Sans + Inter is a strong substitute. **Want exact match? Send the font files or name the typefaces.**
2. **Real campaign numbers** — both landing pages show `[X]`, `[£X]`, `[X.Xx]` placeholders. The kit renders representative example numbers; **send real Meta + Google campaign metrics** to drop in.
3. **Real client testimonial(s)** for the Case Study section on each page.
4. **Hero imagery** — currently a stylized dashboard mockup placeholder. Send a screenshot of an Ads Manager / Google Ads dashboard if you'd like it grounded in real data visuals.
5. **Photography of the team or client work?** — happy to integrate, just send.
