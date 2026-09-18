# Closetly

Streetwear & graphic tee online store — a custom PHP MVC app (no framework) built as a
learning/portfolio project, covering catalog browsing, multi-vendor selling, cart/checkout,
order management, and role-based admin.

## Stack

- **PHP 8.2**, custom router (no framework) — `/controller/action&param=value`
- **MySQL 8.0**, plain PDO with prepared statements
- **nginx** as reverse proxy / static file server
- Vanilla **CSS/JS** frontend, no build step
- **Docker Compose** for local dev (db, php, nginx, mailpit)
- **Playwright** for end-to-end tests

## Getting started

```bash
cp .env.example .env        # edit MYSQL_ROOT_PASSWORD / MYSQL_PASSWORD as you like
docker compose up -d
```

The store is then available at **http://localhost:8080**. Outgoing email (order
notifications) is caught by Mailpit at **http://localhost:8025** instead of a real SMTP
server.

The database schema loads automatically from `shop.sql` on first boot — no seed data, so
the first thing you'll want is an account (see **Roles** below on how to get an admin
one), then create some categories and products from there.

## Running the tests

```bash
npm install
npm test              # full Playwright suite
npm run test:mobile   # mobile viewport only
npm run test:desktop  # desktop viewport only
```

Tests expect the app running at `http://localhost:8080` (see `playwright.config.js`).

## Architecture

- **`controllers/`** — one class per resource (`ProductController`, `OrderController`, ...),
  dispatched by `index.php` based on the URL's `controller`/`action`.
- **`models/`** — plain data records (`Product`, `Order`, ...) with public properties only.
- **`models/*Repository.php`** — all persistence (SQL) for a given record, injected with a
  PDO connection so tests can swap it out.
- **`views/`** — plain PHP templates, rendered by the controller that owns them.
- **`helpers/utils.php`** — auth/role checks, CSRF tokens, small view helpers.
- **`helpers/Mailer.php` / `EmailTemplate.php`** — raw-SMTP order notification emails.

## Roles

| Role | Can do |
|---|---|
| `user` (customer) | Browse, buy, view their own orders |
| `vendedor` (seller) | Everything a customer can, plus manage their own products |
| `admin` | Manage all products, categories, orders, and users |

The first admin account has to be created directly in the database (there's no public
"become admin" flow, by design). Everyone else can register at `/user/create`; admins can
promote/create sellers and admins from **Manage Users**.

## Security notes

- CSRF tokens on every state-changing form (`Utils::generateCsrfToken()` /
  `validateCsrfToken()`).
- Passwords hashed with `password_hash()` / verified with `password_verify()`.
- Order viewing is ownership-checked (a customer can't view another customer's order by
  guessing its id).
- Uploaded product images are validated by their actual file bytes, not the
  client-supplied filename/MIME type.
