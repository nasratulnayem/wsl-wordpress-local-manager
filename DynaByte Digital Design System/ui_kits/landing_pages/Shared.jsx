// Shared building blocks for DynaByte landing pages
// Loaded as a Babel script; exposes components on window.

const { useState } = React;

const Icon = ({ name, size = 22, stroke = 1.75, color = 'currentColor', className = '' }) => {
  const paths = {
    target: <><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5" fill={color}/></>,
    trendingUp: <><path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/></>,
    messageSquare: <><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></>,
    shieldCheck: <><path d="M12 2l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/></>,
    zap: <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>,
    search: <><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></>,
    refresh: <><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/></>,
    users: <><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></>,
    barChart: <><path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="8" width="3" height="10"/><rect x="17" y="4" width="3" height="14"/></>,
    chevron: <polyline points="9 18 15 12 9 6"/>,
    check: <polyline points="20 6 9 17 4 12"/>,
    x: <><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></>,
    minus: <line x1="5" y1="12" x2="19" y2="12"/>,
    phone: <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.37 1.9.72 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.35 1.85.59 2.81.72A2 2 0 0 1 22 16.92z"/>,
    calendar: <><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></>,
    sparkles: <><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><circle cx="12" cy="12" r="3"/></>,
    flame: <path d="M12 2c1 4 5 5 5 10a5 5 0 0 1-10 0c0-2 1-3 2-4-1 4 2 5 3 5s3-1 3-4c0-3-3-4-3-7z"/>,
    funnel: <path d="M3 4h18l-7 9v5l-4 2v-7z"/>,
    rocket: <><path d="M5 13l-2 6 6-2"/><path d="M19 5c0 6-5 11-9 13l-4-4C8 10 13 5 19 5z"/><circle cx="14.5" cy="9.5" r="1.5"/></>,
    instagram: <><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill={color}/></>,
    facebook: <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>,
    google: <><path d="M21.5 12.2c0-.7-.1-1.4-.2-2H12v3.8h5.4c-.2 1.3-.9 2.4-2 3.1v2.6h3.3c1.9-1.7 3-4.3 3-7.5z"/><path d="M12 22c2.7 0 5-.9 6.7-2.4l-3.3-2.6c-.9.6-2 1-3.4 1-2.6 0-4.8-1.7-5.6-4.1H3v2.6C4.7 19.7 8.1 22 12 22z"/><path d="M6.4 13.9A6 6 0 0 1 6.4 10V7.4H3a10 10 0 0 0 0 9.2z"/><path d="M12 6.5c1.5 0 2.8.5 3.8 1.5l2.9-2.9C16.9 3.4 14.7 2.5 12 2.5c-3.9 0-7.3 2.3-9 5.6l3.4 2.6C7.2 8.2 9.4 6.5 12 6.5z"/></>,
  };
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round" className={className}>
      {paths[name]}
    </svg>
  );
};

const Eyebrow = ({ children, color }) => (
  <div className="eyebrow" style={color ? { color } : null}>{children}</div>
);

const Button = ({ variant = 'primary', children, onClick, icon, ariaLabel }) => {
  const cls = `btn btn-${variant}`;
  return (
    <button className={cls} onClick={onClick} aria-label={ariaLabel}>
      <span>{children}</span>
      {icon && <Icon name={icon} size={18} stroke={2} />}
    </button>
  );
};

const Section = ({ id, dark, soft, children, narrow }) => (
  <section id={id} className={`section ${dark ? 'section--dark' : ''} ${soft ? 'section--soft' : ''}`}>
    <div className={`container ${narrow ? 'container--narrow' : ''}`}>{children}</div>
  </section>
);

const SectionHead = ({ eyebrow, title, blurb, dark, align = 'left', highlight }) => (
  <div className={`sec-head sec-head--${align}`}>
    {eyebrow && <Eyebrow color={dark ? '#6FC3FF' : null}>{eyebrow}</Eyebrow>}
    <h2 className={dark ? 'on-dark' : ''} dangerouslySetInnerHTML={{ __html: title }} />
    {blurb && <p className={`sec-blurb ${dark ? 'on-dark-2' : ''}`}>{blurb}</p>}
  </div>
);

const Header = ({ activePage, onNav }) => (
  <header className="site-header">
    <div className="container site-header__row">
      <a href="#" className="brand">
        <img src="../../assets/dynabyte-logo.png" alt="DynaByte Digital" />
      </a>
      <nav className="site-nav">
        <a href="#">Home</a>
        <a href="#">Services</a>
        <a className={activePage==='google' ? 'active' : ''} onClick={() => onNav('google')}>Google Ads</a>
        <a className={activePage==='facebook' ? 'active' : ''} onClick={() => onNav('facebook')}>Facebook Ads</a>
        <a href="#">Case Studies</a>
        <a href="#">About</a>
      </nav>
      <Button variant="primary" icon="calendar">Book Now</Button>
    </div>
  </header>
);

const Footer = () => (
  <footer className="site-footer">
    <div className="container site-footer__grid">
      <div>
        <img src="../../assets/dynabyte-logo.png" alt="DynaByte Digital" className="site-footer__logo" />
        <p className="site-footer__tag">We make SEO &amp; branding simple. More visibility, better rankings, and real growth — without the guesswork.</p>
      </div>
      <div>
        <h6>Services</h6>
        <ul><li>SEO</li><li>Local SEO</li><li>Google Ads</li><li>Facebook Ads</li><li>Design &amp; Development</li></ul>
      </div>
      <div>
        <h6>Offices</h6>
        <p className="site-footer__addr">🇨🇦 3798 Lawrence Ave E, Scarborough, ON M1G, Canada</p>
        <p className="site-footer__addr">🇧🇩 Tukerbazar, Sylhet 3100, Bangladesh</p>
      </div>
      <div>
        <h6>Get in touch</h6>
        <p className="site-footer__addr">hello@dynabytebd.com</p>
        <Button variant="ghost-light" icon="calendar">Book a Strategy Call</Button>
      </div>
    </div>
    <div className="container site-footer__bottom">
      <span>© 2026 DynaByte Digital. All rights reserved.</span>
      <span>Privacy · Terms</span>
    </div>
  </footer>
);

Object.assign(window, { Icon, Eyebrow, Button, Section, SectionHead, Header, Footer });
