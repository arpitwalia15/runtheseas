# Run The Seas — Admin Platform (WordPress) — FINAL DEVELOPER HANDOFF REPORT

**Master artifact:** `RTS_WordPress_Plugin_MASTER_FINAL_v3_2026-08-19.zip` (supersedes every earlier Batch/FINAL zip)
**Plugin version:** 1.8.0 · **DB schema version:** 1.8.0 · **Tests:** 334 / 334 passing · **Plugin PHP warnings during the full run:** 0
**Companion documents:** `RTS_Admin_Platform_Full_Specification_v5.docx` (134 pp, the 35-screen spec), `RTS_Admin_Platform_Wireframes_v17.html`, `RTS_Handoff_Cover_Note.docx`

This report is the single source of truth for what is done, what needs a live setting, and what the developer must do on the production site. Read **Section C** first if you are the developer.

---

## A. COMPLETE AND TESTED (works now, in this plugin, proven by automated tests)

| Area | What's real | Proof |
|---|---|---|
| All 35 spec screens | Every screen exists in wp-admin under **Run The Seas** (35 pages + Settings). | 334 REST/API assertions + Playwright browser runs of every admin form and the public survey |
| Core flow | Survey w/ conditional logic → registration → email-link verification → $100 Cabin Credit (1/person, DB-enforced) → Founding Runner # → referral chain → trophies (1 & 42 referrals) → K-factor | §1–11 of `wp_test_flow.py` |
| Authorization — REST | **113 routes via one fail-closed registrar (`RTS_Auth::route`)**: 102 guarded by `rts_*` caps, 11 public (exactly the anonymous participant flows). Registrar throws if a route names no capability. | §43: generated anonymous sweep (all 102 → 401), public sweep, role matrix |
| Authorization — admin UI | **35 admin pages via `RTS_Auth::page()`**, gated by the *same* `rts_*` caps as the REST routes; **38 `admin_post` handlers** gated by `RTS_Auth::action_cap()` (unknown action → `do_not_allow`) + nonce. **`manage_options` is no longer used anywhere.** | §51 + browser: `rts_administrator` opens 9 operational screens, performs a suspend, is blocked from Administrators/Backup/Security/Settings and from core WP admin (plugins/users/themes/settings) |
| Roles | 4 real WP roles (`rts_super_admin`, `rts_administrator`, `rts_content_editor`, `rts_contributor`) with 6 caps (`rts_view/manage/send_bulk/manage_admins/system/content`), synced to spec Appendix B on every activation. WP `administrator` holds all. Last-super-admin lockout prevention. | §24, §43e |
| Emergency Take-Offline | Real 503 branded holding page for the public; wp-admin/login/REST/cron stay up; logged-in RTS staff still see the site; admin-bar toggle on every screen; type-`OFFLINE`-to-confirm; red notice on every admin page while down; admin alert email; REST `POST /system/take-offline {"confirm":"OFFLINE"}`, `/system/restore`, `GET /system/status`. | §44 + browser (anon sees 503, admin restores) |
| Send-gate | Draft → test-to-self → test-to-admin-group → bulk; step 3 locked until both tests; override requires a reason and is logged distinctly; **enforced server-side**. Now actually generates emails (see B). | §21, §46 |
| Unsubscribe | 4 categories, token link, instant; **one** audience function excludes unsubscribed people from every bulk path; transactional mail never suppressed; marketing mail auto-gets an unsubscribe footer. | §9, §20, §46 |
| Email Campaign Builder | Real triggers (N days after registration/verification), dedup via `campaign_sends` (2nd run sends 0), hourly WP-Cron. | §26, §50 |
| Cabin Credit mgmt | Defer (shared cabin, still in liability), void (reason), bulk void (Tier-3). | §10, §17 |
| Trophies | Distribution, create, explicit retroactive unlock. | §18 |
| Draws A/B | Seeded, **independently reproducible** winner selection; Tier-3. | §19 |
| Participants | Directory, profile, suspend/reinstate, **merge-preview-before-commit** (one credit survives). | §13–14 |
| Verification queue | Manual verify requires a reason; same side effects as the real link. | §15 |
| Email templates | Append-only version history; rollback creates a new version. | §16 |
| Broadcast / FR Outreach | Audience-scoped (all/runners/non-runners) through the send-gate. | §21, §25 |
| Exec/Security/Super-admin dashboards, Admin & Roles, Backup | Live KPIs, global search, role distribution, lockout prevention, backup log. | §22–25 |
| Email Reporting, Ad Campaign Analysis (real UTM attribution, null-safe CAC), Interest lists (mutually exclusive), Fraud (heuristic → human review, never reappears) | | §27–30 |
| Customer Feedback, Q&R loop (append-only drafts), Who Is The Customer, Website CMS (**live on the public survey page**), Export Center (real CSV download) | | §31–35 |
| Report Builder (whitelisted, injection-tested), Saved/Scheduled Reports (daily WP-Cron), Segments (live recount), Quick Reports (real numbers + relationship badges), Action Items (rule-based, dedup, auto-close, daily cron), Cabin Sales Forecast (mutually-exclusive pools), FR Outreach, Survey Logic Map | | §36–42 |
| Rate limiting | Per-IP/hour on public register & verify (defaults 100/60, 0 = off); 429 on breach. | §47 |
| Input validation | Public register: email format required; all free text sanitized + length-capped; party size clamped; survey answers sanitized/capped. Admin forms: `sanitize_*` everywhere; every renderer escapes via `esc_html`/`esc_attr`. No SQL built from user input (all `$wpdb->prepare`). | §45 + static review |
| Email subsystem | **Every** email goes through `RTS_Production::send()` → `wp_mail()`; `log` mode (default) writes to `wp_rts_email_outbox`; `send` mode also calls `wp_mail()` and records delivered/error per message. Merge fields `{first_name} {name} {email} {founding_runner_number} {referral_url} {unsubscribe_url} {verify_url}`. Real verification email with a real link (`/survey/?rts_verify=TOKEN`) that the survey page honours. | §45–46 |
| WP-Cron | 4 hooks scheduled on activation: `rts_cron_campaign_triggers` (hourly), `rts_cron_scheduled_reports` (daily), `rts_cron_action_items` (daily), `rts_cron_fr_sync` (15 min). Each proven to execute. | §50 |
| AI integration point | `POST /ai/draft` + "✨ AI: create draft" in Q&R queue. Real `wp_remote_post` to Anthropic Messages API when a key is set; **`AI_NOT_CONFIGURED` (never a fake answer) when not**. Request/response proven with a mocked provider. | §48 |
| External Founding Runners | CSV import (Settings page) + REST import + **email-match sync** (immediate + 15-min cron); FR Outreach shows real with/without-credit counts. | §49 |
| Forms adapter | `RTS_Production::register_from_form($fields, $source)` + action `rts_participant_registered` — one call from any forms plugin. | §50 |
| Uninstall | `uninstall.php` removes roles/caps/cron/options; tables preserved unless `RTS_UNINSTALL_DROP_TABLES` is defined true. | manual |

---

## B. COMPLETE BUT REQUIRES LIVE CONFIGURATION (code done; one setting/credential on the live site)

All of these are on **wp-admin → Run The Seas → Settings** (role: Super Administrator / `rts_system`).

1. **Email delivery — switch `log` → `send`.**
   - Install and configure an SMTP/API mail plugin (any of: *WP Mail SMTP*, *Brevo (Sendinblue) for WordPress*, *SendGrid*, *Post SMTP*). These plug into `wp_mail()`, which is exactly what the plugin calls — no RTS code change.
   - In that plugin, verify the sender domain/address. Set the same address under RTS Settings → *From address* (default `info@runtheseas.com`) and *From name*.
   - RTS Settings → Email delivery → **send**. Send yourself a test via Broadcast → *Send test to me*; check Settings → *Email outbox* shows `send ✅`.
   - Until you switch, everything is written to `wp_rts_email_outbox` (visible on Settings) and nothing leaves the server. Note: in `log` mode the public survey shows a "Follow verification link" button so the flow can be exercised without email; in `send` mode it tells the user to check their inbox.

2. **AI drafting — add the API key.** Settings → *AI drafting* → paste an Anthropic API key; model defaults to `claude-sonnet-4-6`. Buttons enable themselves. Key is stored in `wp_options` (`rts_settings`) — treat like a password; restrict DB access accordingly. Endpoint used: `https://api.anthropic.com/v1/messages` (outbound HTTPS from the web server must be allowed).

3. **Rate limits** — defaults 100 registrations / 60 verification attempts per IP per hour. If the site is behind Cloudflare/a load balancer, the code already prefers `X-Forwarded-For`; make sure the proxy sets it (otherwise all users share one IP and will hit the limit). Adjust in Settings; `0` disables.

4. **Admin alert email** — Settings → *Admin alert email* (take-offline/restore alerts). Blank = WordPress `admin_email`.

5. **Holding-page message** — Settings → *Offline message* (also editable at the moment you take the site offline).

6. **Reliable cron timing** — WP-Cron runs on page loads (works, but timing drifts on low-traffic sites). For production: in `wp-config.php` add `define( 'DISABLE_WP_CRON', true );` and add a system cron line on the server: `*/5 * * * * curl -s https://runtheseas.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1`. Settings → *Scheduled jobs* shows next/last run per hook.

7. **Application Passwords for any API client** — any external tool calling `/rts/v1/*` must authenticate: `wp user application-password create <user> <label> --porcelain` (or Users → Profile → Application Passwords) and send HTTP Basic. Requires HTTPS (or `WP_ENVIRONMENT_TYPE=local` on a dev box only).

---

## C. MUST BE COMPLETED BY THE DEVELOPER ON THE LIVE SITE (needs access/credentials I do not have)

1. **Install the plugin on runtheseas.com**
   1. Back up the site (DB + files).
   2. Upload `rts-admin-platform/` to `wp-content/plugins/` (or Plugins → Add New → Upload the zip).
   3. Activate. This creates **32 tables** `wp_rts_*` (prefix follows your `$table_prefix`), the 4 roles + 6 caps, and schedules 4 cron hooks. Requires MySQL/MariaDB with `utf8mb4`, PHP 8.1+ (tested 8.3), WordPress 6.4+.
   4. Create two Pages: **Survey** (slug `survey`, content `[rts_survey]`) and **Manage Email Preferences** (slug `unsubscribe`, content `[rts_unsubscribe]`). The email links and the CMS rely on these slugs.
   5. Run the seed **only on a staging copy** (`wp eval-file .../seed.php`) — it wipes RTS tables. On production, build the real survey in *Survey Administration* (clone the seeded one on staging and export, or enter questions directly).
   6. Log in as the WP administrator; confirm the **Run The Seas** menu, open **Settings** and complete Section B.

2. **Existing WordPress forms plugin → participant registration.** Your current forms live in a plugin I cannot see. Wire its submission hook to the adapter; nothing else changes:
   ```php
   // Gravity Forms example (functions.php or a tiny mu-plugin):
   add_action( 'gform_after_submission_5', function ( $entry, $form ) {   // 5 = your form id
       RTS_Production::register_from_form( array(
           'first_name' => rgar( $entry, '1.3' ), 'last_name' => rgar( $entry, '1.6' ),
           'email' => rgar( $entry, '2' ), 'country' => rgar( $entry, '3' ),
           'runner_status' => rgar( $entry, '4' ) === 'Yes' ? 'runner' : 'non_runner',
           'ref' => rgar( $entry, '7' ), 'utm_campaign' => rgar( $entry, '8' ),
       ), 'gravity_forms' );
   }, 10, 2 );
   // WPForms: wpforms_process_complete   · CF7: wpcf7_mail_sent   · Elementor: elementor_pro/forms/new_record
   ```
   The adapter validates, registers, sends the verification email (per Section B.1), fires `rts_participant_registered`, and returns the same array as `POST /rts/v1/participants/register` (`DUPLICATE_EMAIL` on repeats). Map your form's field IDs; nothing else is needed. If you'd rather post from JavaScript, call `POST /rts/v1/participants/register` (public, rate-limited) with `{name,email,country,runner_status,referred_by_code,utm_campaign}`.

3. **Migrate existing referral/leaderboard data (if any) from the current plugin.** The schema is in `includes/class-rts-db.php`. Minimum: insert into `wp_rts_participants` (email unique; set `email_verified=1`, `verified_at`, a `founding_runner_number`, `referral_code`, `unsubscribe_token`), then `wp_rts_cabin_credits` one row per verified person, `wp_rts_subscriptions` 4 rows per person (`survey/referral/trophy/general`, `subscribed=1`), and `wp_rts_referrals` for historical referrals (`verified=1`, `fraud_review_status='clear'`). Then run `POST /rts/v1/founding-runners/sync`. Do this on staging first and run `wp_test_flow.py` against it.

4. **Founding Runners from the main site ("Without Cruise Credit").** Export `name,email` from the main site and either upload the CSV on Settings → *External Founding Runners*, or POST `/rts/v1/founding-runners/import {"rows":[{"name":..,"email":..}],"source":"main_site"}` (Super Admin). Matching by email runs immediately and every 15 minutes. For a permanent feed, schedule the export → import (e.g., a cron job that POSTs the CSV rows) — the endpoint is idempotent (`INSERT IGNORE` on email).

5. **HTTPS + Application Passwords.** The live site must be HTTPS (it is); remove nothing — app passwords then work natively. Do **not** add `WP_ENVIRONMENT_TYPE=local` on production.

6. **Database backups.** The *Backup & System* page logs backup events; the actual dump is hosting-level. Configure your host's (or UpdraftPlus/BlogVault) scheduled DB+files backup and test a restore once. Optionally call `POST /rts/v1/backups/run` from that job so the RTS log reflects reality.

7. **Create real admin accounts.** Run The Seas → Administrators & Roles → Invite (creates a real WP user with the chosen RTS role; they set a password via the standard WP reset email — which requires Section B.1 email to be live, or set the password manually in Users).

---

## D. SECURITY / PRODUCTION HARDENING STILL REQUIRED (recommended before public launch)

1. **Login protection** — WordPress core does not limit login attempts or track sessions (the Security Dashboard shows `n/a` for those honestly). Install *Limit Login Attempts Reloaded* or *Wordfence*; enable 2FA for `rts_super_admin` users (e.g., *Two-Factor* plugin).
2. **WAF / edge** — put the site behind Cloudflare (or equivalent); enable its WAF and rate limiting on `/wp-login.php`, `/xmlrpc.php` (disable XML-RPC if unused) and `/?rest_route=/rts/v1/participants/register`.
3. **Secrets** — the AI key lives in `wp_options`. If your policy forbids that, define it in `wp-config.php` and add `add_filter('pre_option_rts_settings', ...)` to inject it, or keep it in the host's secret store and hydrate at request time. Restrict DB and `wp-config.php` permissions (`640`, owned by the web user/root).
4. **REST scope review** — by design 11 routes are public (survey/register/verify/token-subscriptions/one content block). If you want `POST /participants/register` callable **only** from your own forms, keep it public but add a nonce header check in a small mu-plugin, or lower `rate_limit_register`. Everything else already requires `rts_*` caps.
5. **Content Security** — the public survey JS escapes all server text; admin pages escape all output. Still recommended: enable `DISALLOW_FILE_EDIT` in `wp-config.php`, keep core/plugins updated, and run a `WPScan`/`Wordfence` scan after install.
6. **Load/volume testing** — the suite proves correctness, not scale. Before a campaign push, test the survey endpoint at expected concurrency (e.g., `k6`/`ab`) on staging; add object caching (Redis) if the host offers it. All heavy reads use indexed columns, but the bulk-send path loops `wp_mail()` synchronously — for very large lists (>5k) switch the send loop to Action Scheduler or your mail provider's batch API (single function to change: `RTS_Production::send_to_participants`).
7. **PII & legal** — exports and the participant directory contain PII; keep `rts_view` to staff who need it. Contest rules for Draw A/B are governed by the Official Contest Rules document (legal review still pending per Curtis's notes) — the draw implementation stores the seed for reproducibility; keep the `wp_rts_draws` table as the audit record.
8. **Monitoring** — set the *Admin alert email* (B.4), and have uptime monitoring watch `GET /rts/v1/system/status` (expects `{"online":true}`; auth required) or the public home page for a 503.

---

## E. OPTIONAL / FUTURE ITEMS

- AI drafting for **broadcast** copy (the endpoint supports `task: email_draft`; only the Q&R queue has a button so far).
- True NLP theme clustering for Customer Feedback (today: honest keyword frequency).
- Open/click/bounce tracking in Email Reporting — needs your mail provider's webhook; today those show `n/a`.
- Ad platform APIs (Google/Meta) to auto-populate impressions/clicks/cost (today: manual entry; attribution via UTM is automatic).
- Public leaderboard/trophy pages on the marketing site (the data and REST routes exist; gated `rts_view` — add a public, anonymised route if you want a public board).
- Per-segment runner/non-runner measurement in the Cabin Sales Forecast (today: 78% platform-wide assumption, labelled as such).
- Copy-only permissions for Content Editor on Survey Builder / Email Templates (spec nuance; today Content Editor is content-only).

---

## Quick reference

**Roles → caps** · `rts_super_admin`: view, manage, send_bulk, manage_admins, system, content · `rts_administrator`: view, manage, send_bulk, content · `rts_content_editor`: content · `rts_contributor`: (none) · WP `administrator`: all.
**Tier-3 (`rts_system`)**: Take-offline/restore, Draw A/B, Bulk Void All, backups, settings, FR import, Security Dashboard.
**Audit commands**: `wp eval 'rest_get_server(); print_r(RTS_Auth::registry());'` (routes→caps) · `wp eval 'do_action("admin_menu"); print_r(RTS_Auth::pages());'` (pages→caps) · `wp cron event list | grep rts_` · `python3 wp_test_flow.py` (334 tests; needs WP-CLI + `localhost:8080` or edit `BASE`).
**Tables (32)**: `wp_rts_` surveys, survey_questions, survey_responses, survey_answers, participants, referrals, trophies, trophy_unlocks, cabin_credits, subscriptions, sent_emails, audit_log, email_templates, email_template_versions, draws, email_drafts, backups, campaigns, email_campaigns, campaign_sends, duplicate_reviews, customer_questions, question_response_drafts, question_response_log, content_blocks, export_history, report_definitions, report_runs, segments, action_items, external_founding_runners, email_outbox.
