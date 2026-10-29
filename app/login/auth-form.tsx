"use client";

import { useActionState, useState } from "react";
import { login, signup, type AuthActionState } from "./actions";

const initialState: AuthActionState = {};

export default function AuthForm() {
  const [mode, setMode] = useState<"login" | "signup">("login");
  const action = mode === "login" ? login : signup;
  const [state, formAction, pending] = useActionState(action, initialState);

  return (
    <div className="auth-card">
      <div className="auth-tabs" role="tablist" aria-label="Account action">
        <button type="button" role="tab" aria-selected={mode === "login"} className={mode === "login" ? "auth-tab active" : "auth-tab"} onClick={() => setMode("login")}>Sign in</button>
        <button type="button" role="tab" aria-selected={mode === "signup"} className={mode === "signup" ? "auth-tab active" : "auth-tab"} onClick={() => setMode("signup")}>Create account</button>
      </div>
      <form action={formAction} className="auth-form">
        <label htmlFor="email">Email address</label>
        <input id="email" name="email" type="email" autoComplete="email" placeholder="you@example.com" required maxLength={254} />
        <label htmlFor="password">Password</label>
        <input id="password" name="password" type="password" autoComplete={mode === "login" ? "current-password" : "new-password"} placeholder="At least 8 characters" required minLength={8} maxLength={128} />
        {state.error && <p className="form-message error" role="alert">{state.error}</p>}
        {state.success && <p className="form-message success" role="status">{state.success}</p>}
        <button className="auth-submit" type="submit" disabled={pending}>{pending ? "Please wait…" : mode === "login" ? "Sign in securely" : "Create your account"} <span aria-hidden="true">↗</span></button>
      </form>
      <p className="auth-footnote">Your password is handled by Supabase Auth. Secure Suite never stores it in its own tables.</p>
    </div>
  );
}