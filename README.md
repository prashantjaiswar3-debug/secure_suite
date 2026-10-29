# Secure Suite

A modern Next.js starter with Supabase authentication and a private dashboard.

## Stack

- Next.js App Router, React, and TypeScript
- Tailwind CSS v4 plus custom responsive styling
- Supabase Auth with cookie-aware server-side sessions
- Supabase PostgreSQL profile table protected by Row Level Security (RLS)
- Zod input validation and GitHub Actions CI

## Supabase setup

1. Create or select a Supabase project at https://supabase.com/dashboard.
2. In the project dashboard, open **Connect** (or **Settings → API Keys**) and copy the Project URL and **publishable** key.
3. Create an untracked `.env.local` file in the repository root:

   ```dotenv
   NEXT_PUBLIC_SUPABASE_URL=https://YOUR_PROJECT_REF.supabase.co
   NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_replace_me
   ```

4. In Supabase **SQL Editor**, run the SQL from `supabase/migrations/20261029000000_profiles.sql`. Review the target project before running it.
5. In **Authentication → URL Configuration**, set your local Site URL to `http://localhost:3000` during local development and add the production domain to the redirect allow list after deployment. Enable email confirmation if desired.
6. Install dependencies and start the application:

   ```bash
   npm install
   npm run dev
   ```

7. Open `http://localhost:3000/login`, create an account, confirm the email if confirmation is enabled, then sign in.

## Routes

- `/` — landing page
- `/login` — sign in and create account
- `/dashboard` — private page; verifies authenticated claims on the server

## Database security

The profiles migration creates a minimal `public.profiles` table and enables RLS. Each authenticated user can only read or change the row whose ID matches their own Supabase Auth user ID. Profile data is separate from password credentials, which remain managed by Supabase Auth.

No service-role or secret key is required by this starter. Never put a Supabase secret key in a `NEXT_PUBLIC_*` variable or commit real credentials to Git.

## Quality checks

```bash
npm run typecheck
npm run lint
npm run build
```

GitHub Actions runs these checks on pushes and pull requests. A successful CI run is required before considering this starter validated for deployment.

## Deployment

Configure the two public Supabase environment variables in your hosting provider, configure the production auth Site URL and redirect URLs, apply the database migration to the intended Supabase project, and run the quality checks before release.
