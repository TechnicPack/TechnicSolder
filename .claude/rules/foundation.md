# Laravel Boost Guidelines

This application's main packages & versions:

- php 8.4, laravel/framework v13, laravel/fortify v1, laravel/sanctum v4
- larastan/larastan v3, laravel/boost v2, laravel/mcp v0, laravel/pint v1
- phpunit/phpunit v12, laravel/prompts v0, alpinejs v3, tailwindcss v4

## Skills Activation

Activate the relevant skill whenever you work in that domain — don't wait until stuck.

- `laravel-best-practices` — Apply whenever writing, reviewing, or refactoring Laravel PHP code. This includes controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 queries, caching, authorization, validation, error handling, queue/job config, route definitions, and architectural decisions.
- `tailwindcss-development` — Always invoke when the message includes 'tailwind' in any form. Also invoke for: responsive grid layouts, flex/grid page structures, styling UI components (cards, tables, navbars, forms, badges), dark mode variants, spacing/typography fixes, and Tailwind v3/v4 work. Skip for backend PHP logic, database queries, API routes, or vanilla CSS.
- `fortify-development` — Activate for authentication work: login, registration, password reset, email verification, 2FA/TOTP/QR codes/recovery codes, profile updates, password confirmation, auth routes/controllers. Activate on mentions of Fortify, auth, login, register, forgot password, verify email, 2FA, or references to app/Actions/Fortify/, FortifyServiceProvider, config/fortify.php. Do NOT activate for Passport (OAuth2) or Socialite (social login).
- `debug-using-debugbar` — Use to debug Laravel application issues: slow pages, N+1 queries, exceptions, failed requests, or unexpected behavior via Debugbar CLI commands. Activate even if the user doesn't explicitly mention "debugbar."
