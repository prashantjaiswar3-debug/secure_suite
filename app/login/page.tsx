import Link from "next/link";
import AuthForm from "./auth-form";

export default function LoginPage() {
  return (
    <main className="auth-shell">
      <header className="auth-nav">
        <Link className="brand" href="/"><span className="brand-mark">S</span> secure<span>suite</span></Link>
        <Link className="back-link" href="/">Back to home <span aria-hidden="true">↗</span></Link>
      </header>
      <section className="auth-layout">
        <div className="auth-copy">
          <p className="eyebrow">YOUR SECURE WORKSPACE</p>
          <h1>Good to have<br /><span>you here.</span></h1>
          <p className="intro">Sign in to your Secure Suite workspace or create an account to get started.</p>
          <div className="auth-assurance"><span className="assurance-icon">✓</span><span>Secure authentication powered by Supabase</span></div>
        </div>
        <AuthForm />
      </section>
      <footer><span>SECURE SUITE <span className="footer-muted">/ ACCOUNT ACCESS</span></span><span>Built to evolve. Designed to protect.</span></footer>
    </main>
  );
}