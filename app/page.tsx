const features = [
  { number: "01", title: "Identity & access", text: "Build authentication and server-side authorization around your requirements.", status: "Planned" },
  { number: "02", title: "Data protection", text: "Use validated inputs, least-privilege access, and secure database policies.", status: "Foundation" },
  { number: "03", title: "Operational visibility", text: "Add structured logging, health checks, and automated quality gates.", status: "Planned" },
];

export default function Home() {
  return (
    <main className="shell">
      <nav className="nav">
        <a className="brand" href="/" aria-label="Secure Suite home"><span className="brand-mark">S</span> secure<span>suite</span></a>
        <span className="nav-note"><span className="pulse" /> PROJECT FOUNDATION</span>
      </nav>

      <section className="hero">
        <p className="eyebrow">A BETTER PLACE TO BUILD</p>
        <h1>Security is not a feature.<br /><span>It’s the foundation.</span></h1>
        <p className="intro">A clean, modern starting point for Secure Suite. We’ll shape the application around your real workflow, with maintainability and security built in from day one.</p>
        <div className="hero-actions">
          <a className="primary-button" href="/login">Sign in or create account <span aria-hidden="true">↗</span></a>
          <span className="stack-note">Next.js · TypeScript · PostgreSQL-ready</span>
        </div>
        <div className="hero-art" aria-hidden="true">
          <div className="orbit orbit-one" /><div className="orbit orbit-two" />
          <div className="shield"><svg viewBox="0 0 100 116" role="img"><path d="M50 5 89 20v31c0 27-16 47-39 60C27 98 11 78 11 51V20Z" /><path d="m33 56 12 12 24-27" /></svg></div>
          <div className="art-label label-top"><span className="tiny-dot" /> DESIGNED WITH CARE</div>
          <div className="art-label label-bottom">01 / BUILD WITH CONFIDENCE</div>
        </div>
      </section>

      <section id="roadmap" className="roadmap">
        <div className="section-heading"><div><p className="eyebrow">THE FOUNDATION</p><h2>Ready for what comes next.</h2></div><p className="section-aside">A thoughtful start. A flexible future.</p></div>
        <div className="feature-grid">{features.map((feature) => <article className="feature-card" key={feature.number}><div className="card-top"><span className="feature-number">{feature.number}</span><span className="status">{feature.status}</span></div><h3>{feature.title}</h3><p>{feature.text}</p><span className="card-arrow" aria-hidden="true">↗</span></article>)}</div>
      </section>
      <footer><span>SECURE SUITE <span className="footer-muted">/ PROJECT STARTER</span></span><span>Built to evolve. Designed to protect.</span></footer>
    </main>
  );
}