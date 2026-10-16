# EdHut Solutions

Laravel 12 learning site for EdHut Solutions. Public pages, student accounts, cart checkout, admin order approval, protected course files, quizzes, and the SEO tools are in place.

## Assumptions

- This machine runs PHP 8.2.12 (XAMPP), so the app uses Laravel 12, which supports PHP 8.2. PHP 8.3 is not installed here.
- MySQL database `edhutsolutions`, user `root`, empty password, host `127.0.0.1`.
- Roles are a `users.role` column (`super_admin`, `seo_manager`, `student`) with middleware. Spatie Permission is not installed.
- `GOOGLE_LOGIN_MODE=demo` signs in `demo@edhut.test`. Set the mode to `live` and fill `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI` to use Google OAuth through Laravel Socialite.
- Mail uses the `log` driver. Verification and password-reset messages are written to `storage/logs/laravel.log`.
- Course tuition is not published as a single price, so courses are stored as “Request quotation”. Checkout still creates an order. No card is charged. An administrator approves the order before the student can open files.
- Service fees use the INR amounts published on the EdHut homepage cards.
- The PMP orientation quiz is built from facts on the published PMP page. It is not a PMI exam paper.

## Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://127.0.0.1:8000`.

npm is not required. Styles and scripts live in `public/assets`.

## Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | admin@edhut.test | EdhutAdmin#2026 |
| SEO Manager | seo@edhut.test | EdhutSeo#2026 |
| Demo Google student | demo@edhut.test | EdhutDemo#2026 |
| Student | student@edhut.test | EdhutUser#2026 |

The SEO manager can open `/admin/seo` only. Students land on `/account`.

## How learning access works

1. A visitor adds a course to the cart. Guests use a session cart, which is merged into the account at sign-in.
2. Checkout creates a pending order. Payment status stays `not_required` because no gateway is connected.
3. An administrator opens the order and chooses Approve or Reject.
4. Approval creates an active enrollment. The student then sees the course under My Courses.
5. PDFs and uploaded videos are stored on the private disk (`storage/app/private`) and are streamed only after `CoursePolicy::viewContent` passes. Each open is written to `media_access_logs`.
6. A file that has already been delivered can still be saved by the browser. An external video URL, once shown to an enrolled student, is visible in the page. Uploaded files are not placed in `public/`.

Quizzes linked to a course use the same enrollment check. A question is single-select or multi-select. Multi-select is correct only when the selected set matches the correct set. A wrong selection can subtract the question’s negative marks, or the quiz default. The score does not go below zero. Submitting after the time limit marks the attempt as not passed.

## SEO

`/sitemap.xml` lists the home page, courses, services, blogs, and CMS pages. `/robots.txt` points at that sitemap and disallows `/admin`, `/account`, `/checkout`, and `/learn`. Redirects are edited on the SEO screen and applied to public GET requests.

## Folder structure

```text
app/Http/Controllers    Public, auth, account, learning, and admin controllers
app/Http/Middleware     Role, blocked-account, and redirect checks
app/Services            Cart, order approval, quiz scoring, settings, manual payment
app/Policies            Course content access
public/assets           CSS, JavaScript, and images
resources/views         public, auth, account, and admin Blade layouts
routes/web.php          Public, auth, account, learning, and admin routes
storage/app/private     PDFs and uploaded videos
```

## Tests

```bash
php artisan test
```
