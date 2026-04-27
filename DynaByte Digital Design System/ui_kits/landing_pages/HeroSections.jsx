// Hero, results ticker, problem section
const HeroBlock = ({ eyebrow, title, blurb, primaryCta = 'Book a Free Strategy Call', secondaryCta = 'View Our Case Studies', kind }) => {
  return (
    <section className="hero">
      <div className="hero__bg">
        <span className="hero__blob hero__blob--blue" />
        <span className="hero__blob hero__blob--orange" />
        <span className="hero__grid" />
      </div>
      <div className="container hero__inner">
        <div className="hero__copy">
          <Eyebrow color="#6FC3FF">{eyebrow}</Eyebrow>
          <h1 className="on-dark hero__title" dangerouslySetInnerHTML={{ __html: title }} />
          <p className="hero__blurb on-dark-2">{blurb}</p>
          <div className="hero__ctas">
            <Button variant="primary" icon="calendar">{primaryCta}</Button>
            <Button variant="ghost-light" icon="chevron">{secondaryCta}</Button>
          </div>
          <div className="hero__trust">
            <span className="hero__trust-item"><Icon name="shieldCheck" size={16}/> US · UK · CA · AU</span>
            <span className="hero__trust-dot" />
            <span className="hero__trust-item"><Icon name="users" size={16}/> Trusted by 40+ local brands</span>
            <span className="hero__trust-dot" />
            <span className="hero__trust-item"><Icon name="sparkles" size={16}/> No long contracts</span>
          </div>
        </div>
        <div className="hero__visual">
          <DashboardCard kind={kind} />
        </div>
      </div>
    </section>
  );
};

const DashboardCard = ({ kind = 'google' }) => {
  const isMeta = kind === 'facebook';
  return (
    <div className="dashboard">
      <div className="dashboard__head">
        <div className="dashboard__app">
          {isMeta ? <Icon name="facebook" size={20} color="#1F7AEC"/> : <Icon name="google" size={20} color="#1F7AEC"/>}
          <span>{isMeta ? 'Meta Ads Manager' : 'Google Ads'}</span>
        </div>
        <span className="dashboard__live"><span/> Live · Last 30 days</span>
      </div>
      <div className="dashboard__metrics">
        <div className="dashboard__metric">
          <span className="dashboard__metric-lbl">Conversions</span>
          <span className="dashboard__metric-val">{isMeta ? '184' : '231'}</span>
          <span className="dashboard__metric-delta up">▲ 38%</span>
        </div>
        <div className="dashboard__metric">
          <span className="dashboard__metric-lbl">Cost / Lead</span>
          <span className="dashboard__metric-val">{isMeta ? '£18.40' : '£24.10'}</span>
          <span className="dashboard__metric-delta down">▼ 22%</span>
        </div>
        <div className="dashboard__metric">
          <span className="dashboard__metric-lbl">ROAS</span>
          <span className="dashboard__metric-val">{isMeta ? '4.2×' : '5.1×'}</span>
          <span className="dashboard__metric-delta up">▲ 1.4×</span>
        </div>
      </div>
      <div className="dashboard__chart">
        <svg viewBox="0 0 320 100" preserveAspectRatio="none">
          <defs>
            <linearGradient id="gFill" x1="0" x2="0" y1="0" y2="1">
              <stop offset="0%" stopColor="#1F7AEC" stopOpacity="0.45"/>
              <stop offset="100%" stopColor="#1F7AEC" stopOpacity="0"/>
            </linearGradient>
          </defs>
          <path d="M0 80 L40 70 L80 75 L120 55 L160 60 L200 40 L240 30 L280 22 L320 14 L320 100 L0 100 Z" fill="url(#gFill)"/>
          <path d="M0 80 L40 70 L80 75 L120 55 L160 60 L200 40 L240 30 L280 22 L320 14" stroke="#2E9CFF" strokeWidth="2.5" fill="none"/>
          {[0,40,80,120,160,200,240,280,320].map((x,i)=>{
            const ys = [80,70,75,55,60,40,30,22,14];
            return <circle key={i} cx={x} cy={ys[i]} r="3" fill="#fff" stroke="#2E9CFF" strokeWidth="2"/>
          })}
        </svg>
      </div>
      <div className="dashboard__row">
        <span className="dashboard__pill">{isMeta ? 'Retargeting · Solar UK' : 'HVAC · Boston · Search'}</span>
        <span className="dashboard__pill dashboard__pill--ok"><Icon name="check" size={12} stroke={3}/> Optimised today</span>
      </div>
    </div>
  );
};

const ResultsTicker = ({ items }) => (
  <div className="ticker">
    <div className="ticker__head">
      <Eyebrow>Results at a Glance</Eyebrow>
      <span className="ticker__note">Real numbers, real campaigns. Update with yours.</span>
    </div>
    <div className="ticker__row">
      {items.map((it, i) => (
        <div className="ticker__cell" key={i}>
          <div className="ticker__num">{it.num.split(/(\D+)/).map((p,j)=> /^\d+(\.\d+)?$/.test(p) ? <span key={j}>{p}</span> : <em key={j}>{p}</em>)}</div>
          <div className="ticker__lbl">{it.label}</div>
        </div>
      ))}
    </div>
  </div>
);

const ProblemBlock = ({ eyebrow, title, paras }) => (
  <Section soft>
    <div className="problem">
      <div className="problem__head">
        <Eyebrow>{eyebrow}</Eyebrow>
        <h2 dangerouslySetInnerHTML={{ __html: title }} />
      </div>
      <div className="problem__body">
        {paras.map((p, i) => <p key={i}>{p}</p>)}
      </div>
    </div>
  </Section>
);

Object.assign(window, { HeroBlock, ResultsTicker, ProblemBlock, DashboardCard });
