---
name: dynabyte-design
description: Use this skill to generate well-branded interfaces and assets for DynaByte Digital (and its ClickWise Marketing sub-brand), either for production or throwaway prototypes/mocks/landing pages. Contains essential design guidelines, colors, type, fonts, assets, and a UI kit for the FB Ads and Google Ads landing pages.
user-invocable: true
---

Read the README.md file within this skill, and explore the other available files. The system covers DynaByte Digital — a performance marketing agency for local service businesses (EV repair, EV charging, HVAC, solar, home services) across US · UK · CA · AU.

Key files:
- `README.md` — brand context, content fundamentals, visual foundations, iconography
- `colors_and_type.css` — all CSS vars (colors, type, spacing, shadows, motion)
- `assets/` — logo
- `ui_kits/landing_pages/` — interactive prototype of both FB Ads + Google Ads landing pages
- `preview/` — design-system swatches and component cards

If creating visual artifacts (slides, mocks, throwaway prototypes, landing page variants), copy assets out of this folder and create static HTML files for the user to view. The voice is **direct, plain, anti-fluff, operator-to-operator** — sentence-case headlines, em-dashes, "we"+"you", British/Aus spelling, no emoji. Use the navy hero + light section pattern. Highlight 1–2 key words per headline in `--brand-blue-bright` or `--brand-orange`. Cards radius `--r-xl` (24px), shadow `--sh-2`. Buttons are pill-radius with `--grad-cta` for primary.

If working on production code, copy assets and read the rules here to become an expert in designing with this brand.

If the user invokes this skill without any other guidance, ask them what they want to build or design, ask some questions about audience and section structure, and act as an expert designer who outputs HTML artifacts or production code, depending on the need.
