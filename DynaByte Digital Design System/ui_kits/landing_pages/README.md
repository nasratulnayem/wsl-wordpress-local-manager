# Landing Pages UI Kit

Two paid-traffic landing pages for **ClickWise Marketing** under DynaByte Digital:

- `?page=google` (default) — Google Ads management
- `?page=facebook` — Facebook & Instagram Ads management

Toggle between them using the nav links "Google Ads" / "Facebook Ads".

## Components

| File | What's in it |
|---|---|
| `Shared.jsx` | Header, Footer, Button, Eyebrow, Section, SectionHead, Icon (custom inline-SVG icon set) |
| `HeroSections.jsx` | HeroBlock, DashboardCard, ResultsTicker, ProblemBlock |
| `Sections.jsx` | FeatureGrid, IndustriesGrid, ProcessSteps, CaseStudyBlock, ComparisonTable, FaqList, CtaBanner, PlatformContrast |
| `Pages.jsx` | googleData, facebookData, LandingPage (renders the full page from a data object) |

## Notes
- Both pages share 100% of components — only data differs. To create a third (e.g. Local SEO), copy `googleData` and edit.
- Page structure mirrors the FB/Google Ads cluster docs section-for-section.
- Real campaign numbers, real client testimonials, and real Ads Manager screenshots are placeholders.
