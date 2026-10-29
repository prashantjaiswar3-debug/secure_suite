import Link from "next/link";
import { redirect } from "next/navigation";
import { createClient } from "@/lib/supabase/server";
import { signOut } from "./actions";

export default async function DashboardPage() {
  const supabase = await createClient();
  const { data, error } = await supabase.auth.getClaims();
  const claims = data?.claims;

  if (error || !claims?.sub) redirect("/login");

  const email = typeof claims.email === "string" ? claims.email : "Signed-in user";

  return (
    <main className="shell">
      <nav className="nav">
        <Link className="brand" href="/"><span className="brand-mark">S</span> secure<span>suite</span></Link>
        <form action={signOut}><button className="signout-button" type="submit">Sign out <span aria-hidden="true">↗</span></button></form>
      </nav>
      <section className="dashboard-hero">
        <p className="eyebrow">PRIVATE WORKSPACE</p>
        <h1>You’re in.<br /><span>Welcome back.</span></h1>
        <p className="intro">Your Secure Suite account is authenticated. This private dashboard is the starting point for your application.</p>
        <div className="account-panel">
          <div className="account-avatar">{email.slice(0, 1).toUpperCase()}</div>
          <div><p className="account-label">SIGNED IN AS</p><p className="account-email">{email}</p></div>
          <span className="verified-chip"><span className="pulse" /> Authenticated</span>
        </div>
      </section>
      <section className="dashboard-grid">
        <article className="feature-card"><span className="feature-number">01</span><h3>Account access</h3><p>Sign-in and sign-out are connected to Supabase Auth.</p><span className="status">Connected in code</span></article>
        <article className="feature-card"><span className="feature-number">02</span><h3>Database</h3><p>PostgreSQL is ready for your app's data model once the Supabase project is configured.</p><span className="status">Setup required</span></article>
        <article className="feature-card"><span className="feature-number">03</span><h3>Security</h3><p>Server-side identity verification and environment-only credentials are built into the starter.</p><span className="status">Foundation</span></article>
      </section>
      <footer><span>SECURE SUITE <span className="footer-muted">/ PRIVATE WORKSPACE</span></span><span>Built to evolve. Designed to protect.</span></footer>
    </main>
  );
}