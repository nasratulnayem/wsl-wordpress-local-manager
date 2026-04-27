// Page assemblies for FB Ads + Google Ads landing pages
const googleData = {
  hero: {
    eyebrow: 'Google Ads Management',
    title: 'Google Ads campaigns that bring real <span class="hl">enquiries</span> — not just clicks',
    blurb: 'Most businesses running Google Ads are paying for traffic that never converts. Wrong keywords, weak landing pages, no negative keyword lists — the budget drains and results stay flat. We build campaigns around one thing: getting your phone ringing and your inbox filling.',
    kind: 'google',
  },
  ticker: [
    { num: '231', label: 'qualified leads in 30 days' },
    { num: '£24.10', label: 'average cost per lead' },
    { num: '5.1×', label: 'return on ad spend' },
  ],
  problem: {
    eyebrow: 'Why most campaigns fail',
    title: 'Why most Google Ads campaigns <span class="hl-orange">waste money</span>',
    paras: [
      "A lot of businesses have tried Google Ads. Some still run them. But between broad match keywords eating budget, ads pointing to a homepage instead of a focused landing page, and no proper conversion tracking — most campaigns spend money without a clear way to measure what's working.",
      "We've seen it across HVAC companies, solar installers, and EV repair businesses. The setup looks fine on the surface. Underneath, there's no structure connecting the ad to an actual customer.",
    ],
  },
  features: [
    { icon: 'search', title: 'Keyword research with intent', body: 'We find what people type when ready to book — not just browse. That difference cuts wasted spend significantly.' },
    { icon: 'target', title: 'Conversion-focused landing pages', body: 'A strong ad with a weak landing page still loses. Every element is aligned to turn clicks into calls.' },
    { icon: 'shieldCheck', title: 'Negative keyword management', body: 'Irrelevant searches eat budget fast. We actively manage negatives so ads show only to genuine intent.' },
    { icon: 'barChart', title: 'Transparent reporting', body: "Clear weekly reports: cost, clicks, conversions, cost per lead. Not vanity metrics. We flag problems and fix them." },
  ],
  industries: [
    { icon: 'flame', title: 'HVAC companies', body: 'Heating, cooling, installation, emergency callouts.' },
    { icon: 'rocket', title: 'Solar panel installation', body: 'Residential and commercial lead generation.' },
    { icon: 'zap', title: 'EV repair & charging', body: 'High-intent local searches and infrastructure leads.' },
    { icon: 'users', title: 'Home services', body: 'Plumbing, electrical, roofing, removals.' },
    { icon: 'shieldCheck', title: 'Professional services', body: 'Coaching, dental, health, consultancy.' },
  ],
  process: [
    { title: 'Audit & keyword research', body: "Before launch we audit your account, competitors, and search landscape. Bad campaigns lose money fast — we cut them first." },
    { title: 'Campaign build & landing page review', body: "We build campaigns from scratch (or rebuild yours), with conversion tracking set up properly from day one. Landing pages are part of the build." },
    { title: 'Launch & weekly optimisation', body: "Search terms, bid adjustments, ad copy tests, negative keywords. We touch the account every week." },
    { title: 'Optimise & scale', body: "When a campaign produces consistent leads at a stable cost per acquisition, we scale the budget gradually — only when the data supports it." },
  ],
  caseStudy: {
    title: 'Real campaign. Real results.',
    client: 'HVAC · Boston, MA',
    location: 'US · Search + PMax',
    campaign: '90-day rebuild',
    challenge: 'Account was running broad match across 80+ keywords with no negatives. £6K/month spend, fewer than 12 leads. Tracking misfired on every form submit.',
    actions: [
      'Rebuilt account around 8 high-intent keyword themes',
      'Implemented conversion tracking via GTM + offline imports',
      'Wrote new ad copy aligned to each landing page',
      'Built negative keyword list from 90 days of search terms',
    ],
    quote: "Within six weeks the cost per lead was cut in half and the leads were actually homeowners ready to book a quote. Murad runs it like an operator, not an agency.",
    quoteBy: 'Marc, Holistic Dental & HVAC Group',
    metrics: [
      { num: '231', label: 'conversions' },
      { num: '£412K', label: 'revenue generated' },
      { num: '5.1×', label: 'ROAS' },
    ],
  },
  comparison: {
    headers: ['DynaByte Digital', 'Freelancer', 'Big Agency'],
    rows: [
      { label: 'Direct team access', cells: [
        { type:'yes', text:'You speak to who does the work' },
        { type:'yes', text:'Usually direct' },
        { type:'no',  text:'Junior account manager' }] },
      { label: 'Local business focus', cells: [
        { type:'yes', text:'HVAC · Solar · EV · Home Services' },
        { type:'no',  text:'Generalist' },
        { type:'no',  text:'All industries, no niche depth' }] },
      { label: 'Transparent reporting', cells: [
        { type:'yes', text:'Weekly, plain-English breakdown' },
        { type:'meh', text:'Varies by freelancer' },
        { type:'meh', text:'Dashboard access, little context' }] },
      { label: 'Conversion tracking setup', cells: [
        { type:'yes', text:'Included from day one' },
        { type:'meh', text:'Sometimes extra cost' },
        { type:'yes', text:'Usually included' }] },
      { label: 'Landing page review', cells: [
        { type:'yes', text:'Part of every campaign build' },
        { type:'no',  text:'Ads only' },
        { type:'meh', text:'Sometimes at extra cost' }] },
      { label: 'No long lock-in contracts', cells: [
        { type:'yes', text:'Month-to-month available' },
        { type:'yes', text:'Flexible' },
        { type:'no',  text:'6–12 month minimums typical' }] },
    ],
  },
  faqs: [
    { q: 'How much should I budget for Google Ads?', a: 'For local service businesses, a starting budget of $1,000–$2,000/month gives enough data to optimise. We recommend based on your market, service area, and competition level.' },
    { q: 'How long before I see results?', a: "Most well-structured campaigns start generating leads within 1–2 weeks of launch. The first 30 days are about gathering data — we cut what isn't working and scale what is." },
    { q: 'Do you manage campaigns in the US, UK, Canada, and Australia?', a: 'Yes. Bid strategies, keyword intent, and competition levels vary by region — we build campaigns with those differences in mind.' },
    { q: "What if I've already tried Google Ads and it didn't work?", a: "That's the most common situation we walk into. Usually: poor keyword structure, no conversion tracking, or a landing page that doesn't match the ad. We audit first before rebuilding." },
    { q: 'Will you write the ads for us?', a: 'Yes. We write all ad copy, headlines, and descriptions. You review and approve before anything goes live.' },
    { q: "What's the difference between PMax and Search campaigns?", a: 'Performance Max runs across all Google channels automatically. Search gives more control. For most local businesses, we recommend Search first to build clean conversion data, then layer in PMax once we know what is working.' },
  ],
  contrast: {
    title: 'When Google Ads is the <span class="hl">right choice</span>',
    blurb: 'Google works best when your customer already knows they have a problem and is searching for a solution. Someone typing "emergency HVAC repair near me" is ready to act. Facebook is better for awareness. For most local businesses, both run together — coordinated, not competing.',
    link: 'See how we run Facebook Ads',
  },
  cta: {
    title: 'Ready to run Google Ads that <span class="hl-orange">actually work?</span>',
    blurb: "If you're serious about generating enquiries from Google — not just impressions — let's talk. We'll review your current setup or build a campaign from scratch.",
  },
};

const facebookData = {
  hero: {
    eyebrow: 'Facebook & Instagram Ads',
    title: 'Facebook ads that fill your pipeline — <span class="hl">before</span> customers even start searching',
    blurb: "Google Ads catches people who are ready to buy. Facebook Ads reaches the people who will be ready soon. For local service businesses — solar, HVAC, EV charging, home services — Facebook is where you build familiarity, retarget warm visitors, and generate leads at a lower cost per acquisition. When it's set up right.",
    kind: 'facebook',
  },
  ticker: [
    { num: '184', label: 'purchases generated' },
    { num: '£228K', label: 'revenue attributed' },
    { num: '4.2×', label: 'purchase ROAS' },
  ],
  problem: {
    eyebrow: 'Why most campaigns fail',
    title: "Why most Facebook campaigns <span class=\"hl-orange\">don't convert</span>",
    paras: [
      "Facebook Ads look simple to run. That's why so many businesses waste money on them.",
      "Targeting too broad. Creatives that don't stop the scroll. No retargeting in place. Sending traffic to a homepage. Running the same ad for months until it burns out. The platform rewards structure and consistent testing — without both, spend disappears with little to show for it.",
    ],
  },
  features: [
    { icon: 'target', title: 'Audience strategy', body: 'Built on location, interests, life events, and lookalikes from your customer data. No guessing who sees your ads.' },
    { icon: 'sparkles', title: 'Creative that works', body: 'We write copy and direct the creative brief so your ads stop people mid-scroll — not blend into the feed.' },
    { icon: 'refresh', title: 'Retargeting sequences', body: 'Sequences that follow warm visitors — site viewers, content engagers, cart abandoners — through to conversion.' },
    { icon: 'zap', title: 'Creative rotation', body: 'We rotate creatives every 10–14 days and track fatigue, so you never burn budget on an audience that has already tuned out.' },
  ],
  industries: [
    { icon: 'rocket', title: 'Solar panel installation', body: 'Before/after imagery, cost savings messaging, lead gen forms.' },
    { icon: 'users', title: 'Coaching & consulting', body: 'Trust-building content, webinar promotion, lead magnets.' },
    { icon: 'flame', title: 'HVAC companies', body: 'Seasonal campaigns, emergency service retargeting, maintenance reminders.' },
    { icon: 'shieldCheck', title: 'Dental & health services', body: 'Local awareness, offer-driven campaigns, review-based social proof.' },
    { icon: 'zap', title: 'EV repair & charging', body: 'Early adopter audiences, local awareness, charging infrastructure content.' },
  ],
  process: [
    { title: 'Audience & funnel mapping', body: 'Before any ad is written, we map your audience — cold, warm, and hot — and plan what each segment sees and when.' },
    { title: 'Creative brief & campaign build', body: 'We write ad copy, provide creative direction for images or video, and build the full campaign structure in Ads Manager.' },
    { title: 'Launch, track, refine', body: "We monitor daily after launch — frequency, CTR, CPM, conversion rate. What isn't working gets pulled early. What's working gets more budget." },
    { title: 'Scale & expand', body: 'Once we have a winning creative and audience combination, we scale the budget and test new angles — new audiences, new formats, new offers.' },
  ],
  caseStudy: {
    title: 'Real campaign. Real results.',
    client: 'Solar · Manchester, UK',
    location: 'UK · Meta Lead Forms',
    campaign: '60-day retargeting build',
    challenge: 'Cold-only campaigns burning £4K/month with 12% lead-to-quote rate. No retargeting, no creative rotation, frequency over 6 on top audiences.',
    actions: [
      'Built 3-stage retargeting funnel (site visitors → engagers → cart)',
      'Refreshed creatives every 12 days with new hooks',
      'Switched lead gen ads to instant forms + qualifying questions',
      'Rebuilt audiences using lookalikes from real customer data',
    ],
    quote: 'We were burning budget on cold audiences before. The retargeting flow alone cut our cost per lead in half — and the leads are actually picking up the phone.',
    quoteBy: 'Helen, Adams Solar Group',
    metrics: [
      { num: '184', label: 'purchases' },
      { num: '£228K', label: 'revenue' },
      { num: '4.2×', label: 'ROAS' },
    ],
  },
  comparison: {
    headers: ['DynaByte Digital', 'Freelancer', 'Big Agency'],
    rows: [
      { label: 'Direct team access', cells: [{type:'yes',text:'You speak to who does the work'},{type:'yes',text:'Usually direct'},{type:'no',text:'Junior account manager'}]},
      { label: 'Local business focus', cells: [{type:'yes',text:'HVAC · Solar · EV · Home Services'},{type:'no',text:'Generalist'},{type:'no',text:'All industries, no niche depth'}]},
      { label: 'Transparent reporting', cells: [{type:'yes',text:'Weekly, plain-English breakdown'},{type:'meh',text:'Varies'},{type:'meh',text:'Dashboard access, little context'}]},
      { label: 'Creative direction included', cells: [{type:'yes',text:'Briefs + copy from day one'},{type:'no',text:'Ads only'},{type:'meh',text:'Extra cost typically'}]},
      { label: 'US · UK · CA · AU markets', cells: [{type:'yes',text:'All four, built per region'},{type:'meh',text:'Limited by experience'},{type:'yes',text:'Large teams, less personalised'}]},
      { label: 'No long lock-in contracts', cells: [{type:'yes',text:'Month-to-month available'},{type:'yes',text:'Flexible'},{type:'no',text:'6–12 month minimums typical'}]},
    ],
  },
  faqs: [
    { q: 'What budget do I need for Facebook Ads?', a: 'We recommend a minimum of $1,000/month in ad spend to gather enough data to optimise. For retargeting specifically, budgets can be smaller since the audience is already warm.' },
    { q: 'Do you manage Instagram as well?', a: 'Yes. Facebook and Instagram run through the same Ads Manager. We manage placements across both and advise on which formats perform better for your creative type.' },
    { q: 'Do I need to provide images or video?', a: "We write all ad copy. For visuals, we either provide a creative brief for your designer or work with existing assets. Strong creative is critical on Meta — we won't run ads we don't believe in." },
    { q: 'How quickly do Facebook Ads produce results?', a: 'Cold traffic campaigns typically need 2–4 weeks to optimise. Retargeting campaigns produce results faster because the audience is already familiar with your business.' },
    { q: 'Can you run campaigns in the US, UK, Canada, and Australia?', a: 'Yes. Audience behaviour, CPMs, and creative preferences vary by region — we build campaigns with that in mind.' },
    { q: "What's the difference between Facebook lead gen ads and website traffic ads?", a: 'Lead gen ads collect contact details directly inside Facebook — lower friction, faster leads. Website traffic ads send users to your site where you have more control over the conversion. Which works better depends on your offer and sales process.' },
  ],
  contrast: {
    title: 'When Facebook Ads is the <span class="hl">right choice</span>',
    blurb: "Facebook Ads are built for reaching people who aren't actively searching yet — but match the profile of your ideal customer. They work well for building brand awareness in a new market, promoting a seasonal offer, retargeting people who visited your site but didn't convert, and generating leads through Meta's native lead forms.",
    link: 'See how we run Google Ads',
  },
  cta: {
    title: 'Ready to build a Facebook strategy that <span class="hl-orange">actually works?</span>',
    blurb: "If your current campaigns aren't generating leads — or you haven't started yet — we'll build you a strategy that fits your business, your budget, and your market.",
  },
};

const LandingPage = ({ data }) => (
  <div className="page">
    <HeroBlock {...data.hero} />
    <Section soft>
      <ResultsTicker items={data.ticker} />
    </Section>
    <ProblemBlock {...data.problem} />
    <Section>
      <SectionHead eyebrow="What we do" title='How we run <span class="hl">campaigns that convert</span>' align="center" />
      <FeatureGrid items={data.features} />
    </Section>
    <Section soft>
      <SectionHead eyebrow="Who we work with" title="Built for local service businesses" align="center" />
      <IndustriesGrid items={data.industries} />
    </Section>
    <Section>
      <SectionHead eyebrow="Process" title="From setup to scale" align="center" />
      <ProcessSteps items={data.process} />
    </Section>
    <Section soft>
      <CaseStudyBlock data={data.caseStudy} />
    </Section>
    <Section>
      <SectionHead eyebrow="Why DynaByte" title='Why work with us over a <span class="hl">freelancer or bigger agency?</span>' align="center" blurb="Most local businesses have either been burned by a freelancer who disappeared or quoted by an agency that wanted a 12-month contract before showing a single result. Here's how we're different." />
      <ComparisonTable {...data.comparison} />
    </Section>
    <PlatformContrast {...data.contrast} />
    <Section soft>
      <SectionHead eyebrow="FAQ" title="Common questions" align="center" />
      <FaqList items={data.faqs} />
    </Section>
    <CtaBanner {...data.cta} secondary="View Case Studies" />
  </div>
);

Object.assign(window, { LandingPage, googleData, facebookData });
