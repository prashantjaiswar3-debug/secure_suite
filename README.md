# Secure Suite

A modern Next.js starter with Supabase authentication and a private dashboard.

## Stack

- Next.js App Router, React, and TypeScript
- Tailwind CSS v4 plus custom responsive styling
- Supabase Auth with cookie-aware server-side sessions
- Supabase PostgreSQL profile table protected by Row Level Security (RLS)
- Zod input validation and GitHub Actions CI

## Supabase setup

The connected Supabase project is `https://zjrugiqcjicranvkmoxr.supabase.co`. The `public.profiles` table and its row-level security policies have already been applied there.

1. Copy `.env.example` to an untracked `.env.local` file in the repository root.
2. Set `NEXT_PUBLIC_SUPABASE_URL` to the project URL and `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY` to the project's publishable key. Do not use a secret or service-role key in the application.
3. In Supabase **Authentication → URL Configuration**, set the Site URL to `http://localhost:3000` during local development and add `http://localhost:3000/**` to the redirect allow list. After deployment, change the Site URL to the production origin and allow only the exact production redirect patterns you need.
4. Install dependencies and start the application:

   ```bash
   npm install
   npm run dev
   ```

5. Open `http://localhost:3000/login`, create an account, confirm the email if confirmation is enabled, then sign in.

For a fresh Supabase project, review and apply the SQL in `supabase/migrations/20261029041559_create_profiles_table.sql` to that project. Do not rerun it against the connected project unless you intentionally need to reconcile its schema.

## Routes

- `/` — landing page
- `/login` — sign in and create account
- `/dashboard` — private page; verifies authenticated claims on the server

## Database security

The profiles table uses the Supabase Auth user ID as its primary key, enables RLS, denies anonymous access, and allows authenticated users to read or modify only their own profile. Password credentials remain managed by Supabase Auth.

Never commit `.env.local`, a Supabase secret key, or a service-role key. The publishable key is intended for client applications but should still be supplied through environment configuration rather than hardcoded in application source.

## Quality checks

```bash
npm run typecheck
npm run lint
npm run build
```

GitHub Actions runs these checks on pushes and pull requests. A successful CI run is required before considering this starter validated for deployment.

## Deployment

Configure `NEXT_PUBLIC_SUPABASE_URL` and `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY` in your hosting provider's environment settings for every required environment. Configure Supabase Auth Site URL and redirect URLs for the deployed domain, then run the quality checks before release.
