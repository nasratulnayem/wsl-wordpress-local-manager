// Mid-page sections: features, industries, process, comparison, faq, cta

const FeatureGrid = ({ items }) => (
  <div className="feat-grid">
    {items.map((f, i) => (
      <div className="feat-card" key={i}>
        <div className="feat-card__icon"><Icon name={f.icon} size={22} /></div>
        <h3>{f.title}</h3>
        <p>{f.body}</p>
      </div>
    ))}
  </div>
);

const IndustriesGrid = ({ items }) => (
  <div className="ind-grid">
    {items.map((it, i) => (
      <div className="ind-card" key={i}>
        <div className="ind-card__icon"><Icon name={it.icon} size={20} /></div>
        <h4>{it.title}</h4>
        <p>{it.body}</p>
      </div>
    ))}
  </div>
);

const ProcessSteps = ({ items }) => (
  <div className="process">
    {items.map((s, i) => (
      <div className="process__step" key={i}>
        <div className="process__num">{String(i + 1).padStart(2, '0')}</div>
        <div className="process__body">
          <h3>{s.title}</h3>
          <p>{s.body}</p>
        </div>
        {i < items.length - 1 && <div className="process__connector" />}
      </div>
    ))}
  </div>
);

const CaseStudyBlock = ({ data }) => (
  <div className="case">
    <div className="case__meta">
      <Eyebrow>Case study</Eyebrow>
      <h2 dangerouslySetInnerHTML={{ __html: data.title }} />
      <div className="case__pills">
        <span className="pill pill-outline">{data.client}</span>
        <span className="pill pill-outline">{data.location}</span>
        <span className="pill pill-outline">{data.campaign}</span>
      </div>
      <div className="case__stack">
        <div>
          <h5>The challenge</h5>
          <p>{data.challenge}</p>
        </div>
        <div>
          <h5>What we did</h5>
          <ul>{data.actions.map((a, i) => <li key={i}><Icon name="check" size={14} stroke={3} color="#1F7AEC"/>{a}</li>)}</ul>
        </div>
      </div>
      <blockquote className="case__quote">
        “{data.quote}”
        <cite>— {data.quoteBy}</cite>
      </blockquote>
    </div>
    <div className="case__results">
      <Eyebrow>Results</Eyebrow>
      <div className="case__metrics">
        {data.metrics.map((m, i) => (
          <div key={i} className="case__metric">
            <span className="case__metric-num">{m.num.split(/(\D+)/).map((p,j)=> /^\d+(\.\d+)?$/.test(p) ? <span key={j}>{p}</span> : <em key={j}>{p}</em>)}</span>
            <span className="case__metric-lbl">{m.label}</span>
          </div>
        ))}
      </div>
      <a className="case__link" href="#">Read the full case study →</a>
    </div>
  </div>
);

const ComparisonTable = ({ rows, headers }) => (
  <div className="cmp">
    <div className="cmp__head">
      <div />
      <div className="cmp__head-cell cmp__head-cell--us">
        <img src="../../assets/dynabyte-logo.png" alt="" />
      </div>
      <div className="cmp__head-cell">{headers[1]}</div>
      <div className="cmp__head-cell">{headers[2]}</div>
    </div>
    {rows.map((r, i) => (
      <div className="cmp__row" key={i}>
        <div className="cmp__rowlbl">{r.label}</div>
        {r.cells.map((c, j) => (
          <div className={`cmp__cell ${j === 0 ? 'cmp__cell--us' : ''} cmp__cell--${c.type}`} key={j}>
            <span className="cmp__mark">
              {c.type === 'yes' && <Icon name="check" size={16} stroke={3}/>}
              {c.type === 'no' && <Icon name="x" size={16} stroke={3}/>}
              {c.type === 'meh' && <Icon name="minus" size={16} stroke={3}/>}
            </span>
            <span>{c.text}</span>
          </div>
        ))}
      </div>
    ))}
  </div>
);

const FaqList = ({ items }) => {
  const [open, setOpen] = useState(0);
  return (
    <div className="faq">
      {items.map((q, i) => (
        <div className={`faq__item ${open === i ? 'faq__item--open' : ''}`} key={i}>
          <button className="faq__q" onClick={() => setOpen(open === i ? -1 : i)}>
            <span>{q.q}</span>
            <span className="faq__plus">{open === i ? '–' : '+'}</span>
          </button>
          {open === i && <div className="faq__a"><p>{q.a}</p></div>}
        </div>
      ))}
    </div>
  );
};

const CtaBanner = ({ title, blurb, primary = 'Book a Free Strategy Call', secondary }) => (
  <Section dark>
    <div className="cta">
      <div className="cta__shape" />
      <h2 className="on-dark" dangerouslySetInnerHTML={{ __html: title }} />
      <p className="on-dark-2">{blurb}</p>
      <div className="cta__actions">
        <Button variant="primary" icon="calendar">{primary}</Button>
        {secondary && <Button variant="ghost-light" icon="chevron">{secondary}</Button>}
      </div>
    </div>
  </Section>
);

const PlatformContrast = ({ title, blurb, link, otherLabel }) => (
  <Section soft>
    <div className="contrast">
      <div className="contrast__copy">
        <Eyebrow>Facebook vs Google</Eyebrow>
        <h2 dangerouslySetInnerHTML={{ __html: title }} />
        <p>{blurb}</p>
        <a className="contrast__link" href="#">{link} →</a>
      </div>
      <div className="contrast__visual">
        <div className="contrast__pair">
          <div className="contrast__plat contrast__plat--google">
            <Icon name="google" size={28} color="#1F7AEC"/>
            <h5>Google Ads</h5>
            <p>Captures intent. Right when they're ready to buy.</p>
          </div>
          <div className="contrast__plus">+</div>
          <div className="contrast__plat contrast__plat--meta">
            <Icon name="facebook" size={28} color="#1F7AEC"/>
            <h5>Facebook &amp; Instagram</h5>
            <p>Builds awareness. Reaches them before they search.</p>
          </div>
        </div>
        <div className="contrast__joint">Together &gt; either alone</div>
      </div>
    </div>
  </Section>
);

Object.assign(window, { FeatureGrid, IndustriesGrid, ProcessSteps, CaseStudyBlock, ComparisonTable, FaqList, CtaBanner, PlatformContrast });
