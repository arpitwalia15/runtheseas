# Run The Seas — Admin Platform, WordPress Edition — MASTER FINAL v3 (2026-08-19)

> **Developers: read `HANDOFF_REPORT.md` first.** It has the A–E breakdown (complete / needs a setting /
> must do on the live site / hardening / future) with step-by-step instructions. This README is the
> build history and rationale.

## MASTER FINAL v3 — what changed after "Batch 7 FINAL v2 REST secured"

- **Admin-role inconsistency fixed.** All 35 admin pages now register through `RTS_Auth::page()` and
  all 38 `admin_post` actions through `RTS_Auth::action_cap()` — gated by the **same `rts_*` caps as
  the REST routes**. `manage_options` is gone from the plugin. An `rts_administrator` can use every
  operational screen and is blocked (direct URL too) from Administrators/Backup/Security/Settings and
  from core WP admin; a Content Editor reaches only Website Content. Proven in the browser.
- **Emergency Take-Offline built** (it had only been *referenced* before): real 503 holding page, admin-bar
  toggle, type-OFFLINE confirm, admin notice, alert email, REST routes.
- **Email subsystem**: every message via `wp_mail()`; `log`/`send` modes; `wp_rts_email_outbox`; real
  verification email + real `?rts_verify=` link; merge fields; CASL footer on marketing only.
- **WP-Cron**: campaign triggers (hourly), scheduled reports (daily), action items (daily), FR sync (15 min).
- **AI integration point** (`/ai/draft`, Q&R button) — real provider call when a key is set, honest
  `AI_NOT_CONFIGURED` when not. **External Founding Runner** CSV/REST import + email-match sync.
  **Forms adapter** `RTS_Production::register_from_form()`. **Rate limiting** on public routes.
  **Input validation** on public routes. **Settings** page. `uninstall.php`. Verification token 8→16 chars.
- Suite: **334 passing** (was 286). Zero plugin PHP warnings during the full run.


This is a **real, working WordPress custom plugin** — not a mockup, and not the Node.js
prototype in disguise. It was built after the development team correctly pointed out that the
live runtheseas.com site is WordPress, while the earlier prototype was Node.js/Express. This
plugin answers the question *"can the same business logic be delivered natively in WordPress?"*
The answer, proven here with real tests: **yes**.

**This zip is cumulative and FINAL** — all seven batches. It is the complete WordPress-native
implementation of the 35-screen specification, mirroring the Node.js prototype batch for batch.

## Batch 7 additions (new in this zip) — the last eight screens

- **Report Builder** — dynamic queries against a **whitelisted** set of fields and operators per
  data source. Non-whitelisted fields/filters are silently dropped, never interpolated — tested by
  passing `email; DROP TABLE wp_rts_participants;--` as a field and confirming the table survived.
  `contains` uses `$wpdb->esc_like`, so a literal `%` matches nothing rather than everything.
  Preview results travel via a user-keyed transient, not the URL (the Batch 2 lesson).
- **Saved & Scheduled Reports** — save a definition, "Run now" executes it and logs the run. No
  real scheduler — the frequency is stored; a WP-Cron event would call `run_report()`.
- **Build Custom Segment** — saved segments are **recounted live on every view** (1 → 2 after
  adding a matching participant, proven in the browser), never frozen at save time.
- **Quick Reports — Number Reference** — the spec's relationship-badge screen (Independent /
  Subset of / Overlaps with / = Sum of / Event count) with **real live numbers**, cross-checked in
  tests against the Cabin Credit and Executive Dashboard screens — no discrepancies.
- **Action Items** — **real rule-based recommendations** over live data thresholds (high-CAC
  campaign, pending duplicate reviews, flagged referrals, deferred credits, aging questions).
  **Explicitly NOT AI-generated.** Stable `rule_key` ⇒ at most one open item per condition;
  re-generate never duplicates; a condition that resolves itself auto-closes its item with a note.
- **Estimate Cabin Sales** — four **mutually-exclusive** pools queried live (each participant in
  exactly one WHERE clause — the 8 cells sum to the total participant count, tested), with a
  live-recalculating projection as you type rates.
- **Founding Runner Outreach** — totals against the 10,000 goal. "Without Credit" is honestly
  **0 with a note** — no main-site integration exists in this prototype (spec Appendix F).
- **Survey Logic Map** — real conditional-dependency data from the question table, rendered as a
  flow (Q3 shown only if Q2 = "Yes").

## Batch 6 additions

Five new wp-admin screens mirroring the Node prototype's Batch 6:

- **Customer Feedback** — per-question response breakdowns, a comment feed from real submitted
  survey answers, and a keyword-frequency "recurring themes" view — **explicitly a keyword
  heuristic, not LLM/NLP theme clustering** (no AI integration exists in this prototype).
- **Question & Response Queue** — the full draft → revise → approve → send loop is real: log a
  question, save drafts (each new version keeps the feedback that prompted it), approve & send
  logs the final response with the version count and removes the question from the open queue.
  The honest gap: "AI Create Draft" (spec Appendix Q) is not implemented — drafts are typed.
- **Who Is The Customer** — demographic / geographic / acquisition / runner breakdowns computed
  live from exactly the population the spec calls a "customer": verified + received a Cabin
  Credit. Tested against the ledger to confirm the count matches that definition.
- **Website Content Management** — **genuinely wired to the public site**: edit `survey_intro` in
  wp-admin, and the public `/survey` page fetches it from the DB on load and shows it. Proven by a
  browser test that saves the block in wp-admin, then loads the public page and finds the text.
- **Export Center** — real CSV (`fputcsv`, proper escaping) for participants, cabin credits,
  referrals, comments; streamed as a real download via both REST and an `admin_post` form; every
  export logged with its row count.

## Batch 5 additions

Five new wp-admin screens mirroring the Node prototype's Batch 5:

- **Email Campaign Builder** — real automated, triggered sends (distinct from Broadcast's
  immediate send). "Run trigger check" finds everyone newly eligible (N days after registration /
  verification), sends through the *same* audience function as Broadcast (so unsubscribes are
  excluded), and records each send in `campaign_sends` — **re-running never double-sends**
  (verified: first run 8, second run 0). In production a WP-Cron `wp_schedule_event` would call it.
- **Email Reporting** — real send counts by category and campaign. Open / click / bounce rates
  are honestly `n/a` — no provider webhook exists (Appendix F).
- **Ad Campaign Analysis** — **real UTM attribution**: "Interested" and "Verified & Credited" are
  computed by matching `participants.utm_campaign` to the campaign's code, never typed in. Real
  Cost-per-Interested and CAC, with `—` (null) for zero-conversion campaigns instead of a
  divide-by-zero. Duplicate UTM codes refused.
- **Interest & Notification Lists** — notify list and declined-contact list are **mutually
  exclusive by construction** (declining removes you from the notify list in the same action).
- **Duplicate Detection & Fraud** — same-name + same-country heuristic → a persistent review queue
  (pair ids stored in canonical order so the UNIQUE key works regardless of which way they're
  passed). Approve-as-unique / Confirm-duplicate via real forms; a **reviewed pair never
  reappears** on re-scan. Flagged referrals view with a reject action.

## Batch 4 additions

Five new wp-admin screens — and the one place WordPress is **genuinely better** than the Node
prototype, not just equivalent:

- **Administrator & Role Management** — the four spec roles (Super Administrator, Administrator,
  Content Editor, Contributor) are created as **real WordPress roles** with real capabilities
  (`rts_view`, `rts_manage`, `rts_send_bulk`, `rts_manage_admins`, `rts_system`). Inviting an
  admin creates a real `wp_users` account. Changing a role changes what that person can actually
  do when they log in. The Node prototype had a standalone `admins` table and *no login at all*.
  Proven by a browser test that logs in as an RTS Contributor and gets WordPress's own
  "Sorry, you are not allowed to access this page" on the Administrators screen. Lockout
  prevention: the last Super Administrator cannot be demoted or deactivated.
- **Executive Dashboard v2** — expanded Top-20 KPIs: week-over-week growth, avg referrals per
  Founding Runner, avg travel party, geographic + marketing-source breakdowns, and a
  Registered → Verified → Credited conversion funnel. Replaces the Batch 1 dashboard.
- **Super Administrator Dashboard** — real global search across participants, surveys, trophies,
  admins and the audit log (with `esc_like`, so a literal `%` doesn't match everything), plus
  System Health and quick links.
- **Security Dashboard** — real role distribution and audit log. Failed-login and active-session
  counts are honestly `n/a` with a note: WordPress has real login but core doesn't track those
  without a security plugin — not faked.
- **Backup & System** — manual backup event log (the actual dump is a hosting concern, Appendix F).

## Batch 3 additions

Five new wp-admin screens mirroring the Node prototype's Batch 3 exactly:

- **Cabin Credit Management** (replaces the Batch 1 ledger) — KPIs incl. Outstanding Liability,
  **Defer** (shared-cabin rule: second credit goes to the 2nd sailing, still counted in liability,
  not forfeited) and **Void** (reason required). Can't defer a non-issued credit.
- **Trophy Management** — unlock distribution (0/1/2/3/4+ per participant), create trophy, and
  **retroactive unlock** that lists who already qualifies but doesn't have it and unlocks only on
  explicit click (answers spec Appendix L: retroactive unlock is never silent).
- **Referral Leaderboard & Draws** — live leaderboard, **Draw A / Draw B** with a confirm dialog.
  Draw A weights by verified referral count; B is 42+ only. Every draw stores a random seed and
  the test **independently recomputes the winner from that seed** — the pick is reproducible,
  which a legally-governed contest needs.
- **Subscription Management** — active vs unsubscribed per category, recent unsubscribes with
  the reason captured.
- **Broadcast** — audience-scoped (all / runners / non-runners) with the full **send-gate**:
  draft → test-to-self → test-to-admin-group → bulk, red/green lights, step 3 locked until both
  tests are green. Clicking Send while locked fires the exact warning copy, then a prompt for a
  reason; an override is allowed but logged as `⚠ BULK SEND GATE OVERRIDDEN`. **Enforced
  server-side** — the REST `send-bulk` returns `GATE_NOT_CLEARED` regardless of the UI.

## Batch 2 additions

All four mirror the Node prototype's Batch 2 exactly, so the two can be compared line for line:

- **Survey Administration** (wp-admin → Run The Seas → Survey Administration) — list, clone,
  publish/archive. Clone does a two-pass id remap so **conditional branching survives cloning**
  (the clone's Q3 points at the *clone's* Q2, not the original's — verified by a test that checks
  the remapped id is inside the clone).
- **Participant Profile** (linked from the Participants list) — edit, suspend/reinstate, and
  **Merge Duplicate with a mandatory preview step**: the preview shows exactly what will change
  (referrals reassigned, trophies merged, whether both records have a credit) and commits nothing
  until you click Confirm. Only one Cabin Credit survives a merge.
- **Email Verification Queue** — oldest-first pending list; **manual verify requires a typed
  reason** and then calls the *same* `verify_email()` as a real link click, so it triggers
  identical side effects (credit, trophy, referral completion).
- **Email Template Library** — create / update / roll back. **Version history is append-only**:
  an update writes v2 and keeps v1; a rollback to v1 writes v3 *with v1's content* and keeps
  everything. Nothing is ever deleted.

All admin forms use the WordPress-native `admin_post_{action}` handler pattern with
`wp_nonce_field` / `wp_verify_nonce` and a `manage_options` capability check. The REST routes are
still unauthenticated (see limitations).

## What this is

A standard WordPress plugin (`wp-content/plugins/rts-admin-platform/`), using only
WordPress-native mechanisms:

| Concern | How it's done in WordPress |
|---|---|
| Database | 32 custom tables (`wp_rts_*`) created with `dbDelta()` — the standard WP way |
| Users & roles | Real `wp_users` + `add_role()` / capabilities — real login, real enforcement |
| Data access | `$wpdb` with `->prepare()` everywhere user input touches a query |
| API | WordPress REST API, namespace `rts/v1`, via `register_rest_route()` |
| Admin screens | Real `add_menu_page` / `add_submenu_page` pages inside wp-admin |
| Admin forms | `admin_post_*` actions + nonces + capability checks |
| Cross-request state | `set_transient` / `get_transient` (merge preview) |
| Public pages | Shortcodes `[rts_survey]` and `[rts_unsubscribe]` on ordinary Pages |
| Assets | `wp_enqueue_script` / `wp_localize_script` — no hardcoded URLs |

## What is PROVEN to work (real tests, real WordPress 6.4.3, real MySQL/MariaDB)

`wp_test_flow.py` — **286 assertions, all passing** on a fresh seed (240 functional, now authenticated
as a real admin via Application Password, + 46 security) — hits the live REST API and confirms every
Batch 1–6 behaviour plus all of Batch 7 above (injection dropped + table intact,
literal-% esc_like'd, NO_VALID_FIELDS, run logged, segment live recount, Quick Reports ==
source screens, one open item per rule_key, no duplicates on regenerate, auto-close with note,
8 forecast cells == participant count, honest 0 for external FRs, logic-map dependency). For Batch 6 (comment filter by question,
breakdown NOT_FOUND, stopwords filtered, send-with-no-draft refused, version history keeps the
prompting feedback, static `/questions/response-log` not swallowed by `/questions/(?P<id>)`,
REPLACE updates a block in place, invalid dataset refused, CSV header/rows/content). For Batch 5 (trigger on draft/paused refused,
zero re-sends on second run, unsubscribed excluded from campaign sends, duplicate UTM refused,
null CAC on zero conversions, attribution +1/+1, mutual-exclusivity of the two lists,
case-insensitive duplicate match, reversed-id review accepted, reviewed pair gone on re-scan).
For Batch 4 (invalid role/email refused,
duplicate invite refused, demote-when-another-super-exists allowed, demote/deactivate the LAST
super refused, literal-% search escaped, funnel monotone, honest nulls). Plus a **real login
test** as an RTS Contributor proving WordPress enforces the role. For Batch 3: including the negative cases
(defer a non-issued credit, void without reason, Draw B with nobody eligible, send-bulk before
tests, force without reason, double-send) and the reproducible-seed check. For Batch 2:
invalid status refused, non-whitelisted profile fields silently ignored (not written), merging a
record with itself refused, manual-verify without a reason refused, re-verifying refused,
rollback to a nonexistent version refused, template without a subject refused.

Plus real **browser tests** (Playwright/Chromium) that drive the actual wp-admin forms — clone,
suspend/reinstate, merge preview → confirm, manual verify, template create → update → rollback —
and confirm the DB changed (or, for preview, did *not* change) after each click.

## Bugs found and fixed while building this (the honest log)

1. **Dashboard called its own REST API over HTTP** (`wp_remote_get` loopback). Fragile; doesn't
   work under PHP's built-in dev server. Fixed by calling the REST callback directly — the correct
   WordPress approach anyway.
2. **`$wpdb` returns numeric columns as strings.** `"0"` is truthy in JavaScript. Broke the
   unsubscribe toggle re-render and made an optional question mandatory. Fixed with explicit
   `String(x) === '1'`. **Most important thing for your developers to know** — it recurs anywhere
   `$wpdb` integers reach JS.
3. **22 broken symlinks** in the Debian `wordpress` apt package broke wp-admin's JS in the
   sandbox. Irrelevant to a real WordPress.org install.
11. **(Production pass) WP-Cron custom schedule registered too late.** The 15-minute recurrence was added
   via the `cron_schedules` filter inside `init()`, but `schedule_cron()` runs from the activation hook
   *before* `init()`, so `wp_schedule_event` rejected the unknown schedule and `rts_cron_fr_sync` silently
   never scheduled. Caught by listing cron events after activation (3 of 4 present). Fixed by adding the
   filter at the top of `schedule_cron()`. Lesson: activation hooks run before `plugins_loaded`.

10. **(REST security pass) Role over-permission from Batch 4.** Content Editor and Contributor had
   `rts_view`. Harmless while REST was open (nothing was gated by it) and the UI was gated by
   `manage_options`, but it would have become a PII leak the moment `rts_view` guarded `GET
   /participants`. Caught while mapping routes to capabilities; corrected and covered by tests.
   Lesson: a capability that guards nothing looks harmless right up until it guards something.

9. **(Batch 7) Test-harness only.** Two browser checks failed because my *test script* filled a
   filter value without selecting the field dropdown, so the plugin correctly saved
   `runner_status = "Italy"` (the first option) and correctly found nothing. Re-ran with the field
   selected: both pass. The plugin did exactly what it was told. Also one sloppy assertion of my
   own replaced with a ledger cross-check.

8. **(Batch 6) Test-harness only.** `jget()` raised on a *correct* 404 from
   `/feedback/breakdown/999999`; `jpost()` already returned the JSON body on 4xx. Made `jget()`
   do the same. No plugin change. Also replaced one sloppy assertion of my own with a clean
   cross-check of `total_customers` against the ledger.

7. **(Batch 5) None.** The insert-then-audit pattern from bug #5 was applied from the start
   (`$id = (int) $wpdb->insert_id` *before* `audit()`) and the tests now assert returned ids
   match real rows, so the class of bug that hit Batch 3 three times could not recur silently.

6. **(Batch 4) Test-harness type mismatch, not a plugin bug.** `backup_id` is returned as an int
   (explicitly cast) but `last_backup.id` comes back from `$wpdb` as the string `"1"`; a strict
   `==` in the *test* failed. The plugin was logically correct. Normalised the comparison in the
   test. Logged because it's the same "`$wpdb` returns strings" theme as bug #2 — it bites tests
   as well as JavaScript.

5. **(Batch 3) `$wpdb->insert_id` clobbered by the audit log.** Three functions inserted a row,
   then called `audit()` (which does its *own* `$wpdb->insert`), then read `$wpdb->insert_id` —
   getting the audit row's id, not the real one. The first symptom was `create_draft` returning
   `NOT_FOUND` immediately after a successful insert. The other two (`create_trophy`, `run_draw`)
   had the identical bug but the tests passed because they never asserted on the returned id.
   Fixed all three by capturing `$wpdb->insert_id` into a local *before* calling `audit()`, and
   tightened the tests to assert the returned id matches the real row. **Pattern for your
   developers:** any helper that writes to the DB (logging, caching, etc.) invalidates
   `$wpdb->insert_id` — read it immediately after the insert you care about.

4. **(Batch 2) Merge-preview fatal error.** I first passed the preview as JSON inside a URL query
   arg; WordPress's `add_query_arg` mangled the empty-array `[]` brackets, `json_decode` returned
   null for that key, and `count(null)` fatal-errored. **Caught only by the browser test** — the
   API tests passed because the API never touched that code path. Fixed by stashing the preview in
   a short-lived user-keyed transient, which is the idiomatic WordPress way to pass state from a
   POST handler to its redirect target. Lesson: don't pass structured data through the URL.

## What is NOT done / honest limitations

- **All 35 spec screens are now present.** The gaps that remain are *integrations and
  hardening*, not screens — see the list below.
## Where things stand at the end of all seven batches — read this before anything else

**Done:** every one of the 35 screens from the specification exists as real, working,
WordPress-native code, backed by **286 passing API assertions** (240 functional + 46 security)
and browser tests of every admin form and the public survey.

### REST API authorization — DONE (this was the last flagged gap)

Every REST route is now registered through one central registrar, `RTS_Auth::route()`, which
**refuses to register a route that does not name a capability** (it throws). There is no way to
add an unguarded route by omission. Audit of the live registry: **106 routes — 95 guarded, 11
public.** The 11 public routes are exactly the anonymous participant flows (survey questions /
start / answers / complete, register, email-link verify, token-based subscription management,
and the single content block the public survey page reads). Everything else requires a logged-in
user holding the named `rts_*` capability; WordPress returns 401 (not logged in) or 403 (logged in,
lacking the capability).

Capability per route (summary): reads of platform data → `rts_view`; mutations → `rts_manage`;
broadcast/campaign sends → `rts_send_bulk`; admin user management → `rts_manage_admins`;
Tier-3 actions (Draw A/B, Bulk Void All, backups) → `rts_system`; website content →
`rts_content`. Full per-route list: `wp eval 'rest_get_server(); print_r(RTS_Auth::registry());'`.

**Roles were corrected to match spec Appendix B** (the one deliberate change outside the REST
layer): Content Editor now holds only `rts_content` and Contributor holds nothing — Batch 4 had
given both `rts_view`, which would have exposed participant PII once REST was gated. This has
**zero effect on the admin UI** (those pages are gated by `manage_options`); it only affects what
those roles can reach via REST. `register_roles()` now *syncs* capabilities exactly (adds and
removes), so existing installs are corrected on the next activation / version bump.

**How to authenticate to the REST API:** standard WordPress Application Passwords (HTTP Basic):
`wp user application-password create curtis my-app --porcelain`, then
`curl -u curtis:<password> ".../?rest_route=/rts/v1/participants"`. (Application Passwords need
HTTPS, or `WP_ENVIRONMENT_TYPE=local` for a dev box — the sandbox sets the latter in wp-config.)

**Security tests (section 43 of `wp_test_flow.py`)** are *generated from the live registry*, so
they cannot drift from the code: every guarded route is hit anonymously and must return 401; every
public route is hit anonymously and must not; then a role matrix with real WordPress users —
Contributor (403 everywhere), Content Editor (can write content, 403 on participants/finance/
security/sends), Administrator (can manage/send, 403 on Draw/Void-All/backups/admin-management),
a deactivated administrator (403 — roles stripped, app password alone grants nothing), wrong
password (401), and an end-to-end anonymous survey → register → verify → unsubscribe that still
works while the same anonymous caller is refused their own record via the admin route.

**Not done — the remaining work is integration and hardening, not authorization or screens:**

- **Email is simulated** — "click to simulate," not `wp_mail()`.
- **No integration with your existing WordPress forms plugin.** Creates its own tables; does
  not read/migrate the current form plugin. That's the development team's to scope.
- **Sandbox, not your server.** PHP built-in dev server on `localhost:8080`. Standard plugin,
  should drop into any WP 6.x, but untested on your host.

## Comparing this to the Node prototype

Business rules are **identical** (same schema, same rules, same test assertions — 286 here
mirror the Node suite). One place WordPress is *ahead*: REST authorization is real here (roles,
capabilities, 401/403); the Node prototype's API was open. The one place they *diverge*, deliberately: Batch 4's admin management
uses WordPress's real user/role system instead of a standalone table, because reinventing auth
inside WordPress would be wrong. Only the delivery mechanism differs. So the architecture question is
purely: which stack fits the live site and team. The spec is stack-agnostic and governs either.

## How to run it

```
wp plugin activate rts-admin-platform --allow-root      # creates the 31 tables + 4 RTS roles
wp eval-file wp-content/plugins/rts-admin-platform/seed.php --allow-root
wp post create --post_type=page --post_title=Survey --post_name=survey --post_status=publish --post_content='[rts_survey]' --allow-root
wp post create --post_type=page --post_title="Manage Email Preferences" --post_name=unsubscribe --post_status=publish --post_content='[rts_unsubscribe]' --allow-root
python3 wp-content/plugins/rts-admin-platform/wp_test_flow.py   # expects http://localhost:8080; creates/removes an app password for 'curtis' via WP-CLI
```

Admin: **Run The Seas** menu in wp-admin — Executive Dashboard (v2), Participants, Audit Log,
Survey Administration, Verification Queue, Email Templates, Cabin Credits, Trophies, Referrals &
Draws, Subscriptions, Broadcast, Super Admin Dashboard, Security Dashboard, Administrators &
Roles, Backup & System, Email Campaigns, Email Reporting, Ad Campaign Analysis, Interest
Lists, Fraud Detection, Customer Feedback, Question Queue, Who Is The Customer, Website
Content, Export Center, Report Builder, Saved Reports, Segments, Quick Reports, Action Items,
Cabin Sales Forecast, FR Outreach, Survey Logic Map (Participant Profile via the Participants
list). That is every screen in the specification. Public: `/survey` and `/unsubscribe`.
