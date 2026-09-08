#!/usr/bin/env python3
# wp_test_flow.py — mirrors the Node prototype's test_flow.js against the WordPress REST API.
# Every assertion here is a real HTTP call against real WordPress + real MySQL.
#
# AUTH: admin routes require a logged-in user with an rts_* capability. This harness authenticates
# as the WP administrator 'curtis' using a WordPress Application Password (HTTP Basic auth — the
# standard WP REST auth mechanism). It creates that password via WP-CLI at startup and removes it
# at the end. Public participant routes are exercised anonymously in the SECURITY section.
import json, urllib.request, urllib.error, base64, os, subprocess, sys, time

BASE    = "http://localhost:8080/?rest_route=/rts/v1"  # ?rest_route= works without pretty permalinks
WP_PATH = os.environ.get("RTS_WP_PATH", "/var/www/rts-wordpress")

def wp(*args):
    r = subprocess.run(["wp", *args, "--path=" + WP_PATH, "--allow-root"], capture_output=True, text=True)
    if r.returncode != 0:
        raise RuntimeError("wp " + " ".join(args) + " failed: " + r.stderr.strip())
    return r.stdout.strip()

def app_password(login, label="rts-tests"):
    try: wp("user", "application-password", "delete", login, "--all")
    except RuntimeError: pass  # none existed
    return wp("user", "application-password", "create", login, label, "--porcelain")

ADMIN_USER = os.environ.get("RTS_TEST_USER", "curtis")
ADMIN_PW   = os.environ.get("RTS_TEST_APP_PASSWORD") or app_password(ADMIN_USER)
AUTH_ADMIN = (ADMIN_USER, ADMIN_PW)

def _req(method, path, body=None, auth=AUTH_ADMIN, raw=False):
    data = json.dumps(body or {}).encode() if method == "POST" else None
    req = urllib.request.Request(f"{BASE}{path}", data=data, method=method, headers={"Content-Type": "application/json"})
    if auth:
        req.add_header("Authorization", "Basic " + base64.b64encode(f"{auth[0]}:{auth[1]}".encode()).decode())
    try:
        resp = urllib.request.urlopen(req); code = resp.status; payload = resp.read()
    except urllib.error.HTTPError as e:
        code = e.code; payload = e.read()
    if raw: return code, payload
    try: return json.loads(payload)
    except Exception: return {"_http": code, "_raw": payload[:200].decode(errors="replace")}

def jget(path, auth=AUTH_ADMIN):
    # BASE already contains "?rest_route=", so any query string on the route must use "&", never "?".
    # Returns the JSON body on 4xx so tests can assert on error codes (e.g. NOT_FOUND).
    return _req("GET", path, auth=auth)
def jpost(path, body=None, auth=AUTH_ADMIN): return _req("POST", path, body, auth=auth)
def status(method, path, body=None, auth=None): return _req(method, path, body, auth=auth, raw=True)[0]

passed = 0; failed = 0
def check(cond, msg):
    global passed, failed
    if cond: passed += 1; print("  ✅ CONFIRMED:", msg)
    else:    failed += 1; print("  ❌ FAILED:", msg)

print("=== 1. Survey questions load ===")
questions = jget("/surveys/1/questions")
check(len(questions) >= 5, f"loaded {len(questions)} questions")
cond_q = next((q for q in questions if q["conditional_on_question_id"]), None)
check(cond_q is not None and cond_q["conditional_equals"] == "Yes", "conditional question (Q3 if Q2=Yes) present in data")

print("\n=== 2. Survey session + answers + complete ===")
start = jpost("/surveys/1/start")
check("responseId" in start, f"survey session started, responseId={start.get('responseId')}")
jpost(f"/responses/{start['responseId']}/answers", {"question_id": questions[0]["id"], "answer_value": "25-34"})
done = jpost(f"/responses/{start['responseId']}/complete")
check(done.get("ok") is True, "survey marked complete")

print("\n=== 3. Register Jordan ===")
import time; suf = int(time.time())
reg1 = jpost("/participants/register", {"name": "Jordan Alvarez", "email": f"jordan-{suf}@example.com",
              "runner_status": "runner", "country": "Canada"})
check(reg1.get("error") is None and "verificationToken" in reg1, "registration returns verification token + referral code")

print("\n=== 4. Verify Jordan -> credit + FRN + Founding Runner trophy ===")
v1 = jget(f"/participants/verify/{reg1['verificationToken']}")
check(v1.get("error") is None, "verification succeeded")
check(str(v1["participant"]["founding_runner_number"]).startswith("FR-"), f"FRN assigned: {v1['participant']['founding_runner_number']}")
check(v1.get("creditResult", {}).get("error") is None, "Cabin Credit issued on verification")
p1 = jget(f"/participants/{reg1['participantId']}")
check(any(t["name"] == "Founding Runner" for t in p1["trophies"]), "Founding Runner trophy unlocked")
check(p1["cabin_credit"] is not None and p1["cabin_credit"]["status"] == "issued", "Cabin Credit row exists with status=issued")

print("\n=== 5. Duplicate email refused ===")
dupe = jpost("/participants/register", {"name": "Fake", "email": f"jordan-{suf}@example.com"})
check(dupe.get("error") == "DUPLICATE_EMAIL", "duplicate email correctly refused")

print("\n=== 6. Referral chain: Maria registers via Jordan's code ===")
reg2 = jpost("/participants/register", {"name": "Maria Chen", "email": f"maria-{suf}@example.com",
              "runner_status": "runner", "country": "USA", "referred_by_code": reg1["referralCode"]})
check(reg2.get("error") is None, "Maria registered with referral code")
v2 = jget(f"/participants/verify/{reg2['verificationToken']}")
check(v2.get("error") is None, "Maria verified")
p1 = jget(f"/participants/{reg1['participantId']}")
check(len(p1["referrals"]) == 1 and p1["referrals"][0]["verified"] == "1", "Jordan's referral marked verified after Maria verified")
check(any(t["name"] == "First Referral" for t in p1["trophies"]), "Jordan unlocked First Referral trophy")

print("\n=== 7. Executive summary reflects all of this ===")
s = jget("/reports/executive-summary")
check(s["verifiedReferralsTotal"] >= 1, f"verifiedReferralsTotal={s['verifiedReferralsTotal']}")
check(s["cabinCreditsIssued"] >= 2, f"cabinCreditsIssued={s['cabinCreditsIssued']}")
check(isinstance(s["referralCoefficient"], dict) and "k" in s["referralCoefficient"], f"K computed: {s['referralCoefficient']}")

print("\n=== 8. Leaderboard ===")
lb = jget("/referrals/leaderboard")
check(any(r["id"] == str(reg1["participantId"]) or r["id"] == reg1["participantId"] for r in lb), "Jordan appears on leaderboard")

print("\n=== 9. Unsubscribe — the exclusion must be real ===")
tok = v1["participant"]["unsubscribe_token"]
status_before = jget(f"/subscriptions/{tok}")
check(all(str(sub["subscribed"]) == "1" for sub in status_before["subscriptions"]), "starts subscribed to all 4 categories")
u = jpost(f"/subscriptions/{tok}/unsubscribe", {"category": "all"})
check(u.get("error") is None and len(u["unsubscribed_from"]) == 4, "unsubscribed from all 4")
status_after = jget(f"/subscriptions/{tok}")
check(all(str(sub["subscribed"]) == "0" for sub in status_after["subscriptions"]), "status confirms all 4 now unsubscribed")
r = jpost(f"/subscriptions/{tok}/resubscribe", {"category": "general"})
check(r.get("error") is None, "resubscribe to one category works")

print("\n=== 10. Bulk Void All — the one that was silently broken in Node Batch 1 ===")
before = jget("/cabin-credits/summary")
void = jpost("/cabin-credits/void-all", {"adminUser": "curtis", "reason": "test"})
after = jget("/cabin-credits/summary")
check(void["voided_count"] >= 2 and after["issued"] == 0, f"voided {void['voided_count']}, issued now {after['issued']} (was {before['issued']})")

print("\n=== 11. Audit log has every action ===")
log = jget("/audit-log")
actions = [a["action"] for a in log]
for needle in ["Participant registered", "Email verified", "Cabin Credit issued", "Referral verified",
               "Trophy unlocked: Founding Runner", "Trophy unlocked: First Referral", "Unsubscribed (all)",
               "Bulk void all outstanding Cabin Credits"]:
    check(any(needle in a for a in actions), f"audit log contains '{needle}'")

print("\n=== 12. BATCH 2 — Survey Administration: clone preserves conditional logic ===")
cl = jpost("/surveys/1/clone", {"new_name": "Clone test", "created_by": "curtis"})
check(cl.get("error") is None, f"survey cloned, new id {cl.get('new_survey_id')}")
orig_q = jget("/surveys/1/questions"); clone_q = jget(f"/surveys/{cl['new_survey_id']}/questions")
check(len(orig_q) == len(clone_q), f"clone has same question count ({len(clone_q)})")
cq = next((q for q in clone_q if q["conditional_on_question_id"]), None)
check(cq is not None, "clone still has a conditional question")
# The conditional must point at the CLONE's Q2, not the original's — the remap is the whole point.
clone_ids = {str(q["id"]) for q in clone_q}
check(cq is not None and str(cq["conditional_on_question_id"]) in clone_ids, "clone's conditional points at a question INSIDE the clone (ids remapped, not dangling)")
st = jpost(f"/surveys/{cl['new_survey_id']}/status", {"status": "live", "updated_by": "curtis"})
check(st.get("error") is None, "clone published (draft -> live)")
check(jpost(f"/surveys/{cl['new_survey_id']}/status", {"status": "bogus"}).get("error") == "INVALID_STATUS", "invalid status refused")
surveys = jget("/surveys/list")
check(any(s["id"] == str(cl["new_survey_id"]) or s["id"] == cl["new_survey_id"] for s in surveys), "clone appears in survey list with stats")

print("\n=== 13. BATCH 2 — Participant actions ===")
up = jpost(f"/participants/{reg1['participantId']}/update", {"household_income_bracket": "$100K-$150K", "country": "Canada"})
check(up.get("error") is None, "profile update ok")
check(jpost(f"/participants/{reg1['participantId']}/update", {"email": "hack@x.com", "id": 999}).get("error") == "NO_FIELDS_TO_UPDATE", "non-whitelisted fields (email/id) are ignored, not written")
p = jget(f"/participants/{reg1['participantId']}"); check(p["household_income_bracket"] == "$100K-$150K", "update persisted")
jpost(f"/participants/{reg2['participantId']}/suspend", {"admin": "curtis", "reason": "test"})
check(jget(f"/participants/{reg2['participantId']}")["account_status"] == "suspended", "suspend works")
jpost(f"/participants/{reg2['participantId']}/reinstate", {"admin": "curtis"})
check(jget(f"/participants/{reg2['participantId']}")["account_status"] == "active", "reinstate works")

print("\n=== 14. BATCH 2 — Merge Duplicate: preview must NOT change anything ===")
reg3 = jpost("/participants/register", {"name": "Dupe Person", "email": f"dupe-{suf}@example.com", "country": "Canada"})
jget(f"/participants/verify/{reg3['verificationToken']}")   # now BOTH keep & merge have a credit -> conflict
pv = jpost("/participants/merge-preview", {"keep_id": reg1["participantId"], "merge_id": reg3["participantId"]})
check(pv.get("dry_run") is True and pv["preview"]["credit_conflict"] is True, "preview correctly flags credit conflict, dry_run=true")
check(jget(f"/participants/{reg3['participantId']}")["account_status"] == "active", "preview did NOT suspend the merge target (nothing committed)")
check(jpost("/participants/merge-preview", {"keep_id": reg1["participantId"], "merge_id": reg1["participantId"]}).get("error") == "CANNOT_MERGE_SAME_RECORD", "merging a record with itself refused")
cm = jpost("/participants/merge-commit", {"keep_id": reg1["participantId"], "merge_id": reg3["participantId"]})
check(cm.get("committed") is True, "merge committed")
check(jget(f"/participants/{reg3['participantId']}")["account_status"] == "suspended", "merged-away record suspended")
check(jget(f"/participants/{reg3['participantId']}")["cabin_credit"]["status"] == "cancelled", "duplicate's credit cancelled (one credit per person survives)")
trophies_after = [t["name"] for t in jget(f"/participants/{reg1['participantId']}")["trophies"]]
check("Founding Runner" in trophies_after, "keep record still has its trophies (INSERT IGNORE didn't break existing)")

print("\n=== 15. BATCH 2 — Verification queue + manual verify ===")
reg4 = jpost("/participants/register", {"name": "Pending Person", "email": f"pending-{suf}@example.com", "country": "Canada"})
q = jget("/verification-queue")
check(any(str(x["id"]) == str(reg4["participantId"]) for x in q["pending"]), "unverified participant appears in queue")
check(jpost(f"/participants/{reg4['participantId']}/manual-verify", {"admin": "curtis"}).get("error") == "REASON_REQUIRED", "manual verify without reason refused")
mv = jpost(f"/participants/{reg4['participantId']}/manual-verify", {"admin": "curtis", "reason": "phone-confirmed"})
check(mv.get("error") is None and mv.get("creditResult", mv.get("credit_result", {})) is not None, "manual verify succeeded")
p4 = jget(f"/participants/{reg4['participantId']}")
check(p4["cabin_credit"] is not None and any(t["name"] == "Founding Runner" for t in p4["trophies"]), "manual verify triggered the SAME side effects as a real link (credit + trophy)")
check(jpost(f"/participants/{reg4['participantId']}/manual-verify", {"admin": "curtis", "reason": "again"}).get("error") == "ALREADY_VERIFIED", "re-verifying refused")
check(not any(str(x["id"]) == str(reg4["participantId"]) for x in jget("/verification-queue")["pending"]), "verified participant left the queue")

print("\n=== 16. BATCH 2 — Email templates: version history never destroyed ===")
check(jpost("/email-templates", {"name": "x"}).get("error") == "NAME_AND_SUBJECT_REQUIRED", "template without subject refused")
t = jpost("/email-templates", {"name": "Welcome", "subject": "Welcome aboard!", "html_body": "<p>Hi</p>", "created_by": "curtis"})
tid = t["template_id"]
check(jget(f"/email-templates/{tid}")["version"] in ("1", 1), "created at v1")
u2 = jpost(f"/email-templates/{tid}/update", {"subject": "Welcome aboard! ⚓", "updated_by": "curtis"})
check(u2.get("new_version") == 2, "update -> v2")
vs = jget(f"/email-templates/{tid}/versions")
check(len(vs) == 2 and vs[0]["subject"] == "Welcome aboard! ⚓" and vs[1]["subject"] == "Welcome aboard!", "both v1 and v2 preserved with correct content")
rb = jpost(f"/email-templates/{tid}/rollback", {"to_version": 1, "admin": "curtis"})
check(rb.get("new_version") == 3, "rollback created v3 (did not delete history)")
check(len(jget(f"/email-templates/{tid}/versions")) == 3, "3 versions exist after rollback")
check(jget(f"/email-templates/{tid}")["subject"] == "Welcome aboard!", "current content is v1's content after rollback")
check(jpost(f"/email-templates/{tid}/rollback", {"to_version": 99}).get("error") == "VERSION_NOT_FOUND", "rollback to nonexistent version refused")

print("\n=== 17. BATCH 3 — Cabin Credit: defer (shared-cabin rule) + void-with-reason ===")
# Bulk-void in §10 voided everything; register+verify two fresh people so we have 'issued' credits to act on.
r5 = jpost("/participants/register", {"name": "Credit A", "email": f"creda-{suf}@example.com", "country": "Canada"}); jget(f"/participants/verify/{r5['verificationToken']}")
r6 = jpost("/participants/register", {"name": "Credit B", "email": f"credb-{suf}@example.com", "country": "Canada"}); jget(f"/participants/verify/{r6['verificationToken']}")
ledger = jget("/cabin-credits/ledger"); issued = [c for c in ledger if c["status"] == "issued"]
check(len(issued) >= 2, f"{len(issued)} issued credits available in ledger")
s0 = jget("/cabin-credits/summary-v2")
d = jpost(f"/cabin-credits/{issued[0]['id']}/defer", {"admin": "curtis", "reason": "sharing a cabin"})
check(d.get("error") is None, "defer succeeded")
s1 = jget("/cabin-credits/summary-v2")
check(s1["deferred"] == s0["deferred"] + 1 and s1["issued"] == s0["issued"] - 1, "defer moved one credit issued->deferred")
check(s1["outstanding_liability"] == s0["outstanding_liability"], "deferred credit STILL counted in outstanding liability (not forfeited)")
check(jpost(f"/cabin-credits/{issued[0]['id']}/defer", {"admin": "curtis", "reason": "again"}).get("error") == "INVALID_STATE", "cannot defer a non-issued credit")
check(jpost(f"/cabin-credits/{issued[1]['id']}/void", {"admin": "curtis"}).get("error") == "REASON_REQUIRED", "void without reason refused")
check(jpost(f"/cabin-credits/{issued[1]['id']}/void", {"admin": "curtis", "reason": "dup"}).get("error") is None, "void with reason succeeds")
check(jpost("/cabin-credits/99999/void", {"admin": "curtis", "reason": "x"}).get("error") == "NOT_FOUND", "void nonexistent credit -> NOT_FOUND")

print("\n=== 18. BATCH 3 — Trophies: stats, create, retroactive unlock ===")
ts = jget("/trophies/stats")
check(ts["total_unlocks"] >= ts["unique_holders"], f"unlock EVENTS ({ts['total_unlocks']}) >= unique holders ({ts['unique_holders']}) — matches Quick Reports' documented relationship")
check(sum(ts["distribution"].values()) == ts["total_verified"], "distribution buckets sum to total verified participants")
check(jpost("/trophies", {"name": "x"}).get("error") == "NAME_AND_UNLOCK_RULE_REQUIRED", "trophy without unlock_rule refused")
nt = jpost("/trophies", {"name": "Solo Traveler", "unlock_rule": "solo_test", "created_by": "curtis"})
check(nt.get("trophy_id"), "new trophy created")
# The returned id must be the trophy's REAL id — guards against insert_id being clobbered by the audit-log insert.
check(any(str(t["id"]) == str(nt["trophy_id"]) and t["unlock_rule"] == "solo_test" for t in jget("/trophies/stats")["trophies"]), "returned trophy_id matches the actual new trophy row (not the audit-log row id)")
# Force an eligible-not-unlocked case: delete one Founding Runner unlock, then retroactively restore it.
import subprocess
subprocess.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-e",
  f"DELETE FROM wp_rts_trophy_unlocks WHERE participant_id={r5['participantId']} AND trophy_id=(SELECT id FROM wp_rts_trophies WHERE unlock_rule='founding_runner')"], check=True)
fr_id = next(t["id"] for t in ts["trophies"] if t["unlock_rule"] == "founding_runner")
elig = jget(f"/trophies/{fr_id}/eligible-not-unlocked")
check(any(str(e["id"]) == str(r5["participantId"]) for e in elig), "eligible-not-unlocked correctly finds the participant missing the trophy")
check(jpost(f"/trophies/{fr_id}/retroactive-unlock", {"admin": "curtis"}).get("error") == "PARTICIPANT_IDS_REQUIRED", "retro unlock with no ids refused")
ru = jpost(f"/trophies/{fr_id}/retroactive-unlock", {"participant_ids": [e["id"] for e in elig], "admin": "curtis"})
check(ru.get("unlocked_count") == len(elig), f"retroactive unlock processed all {len(elig)} eligible")
check(not jget(f"/trophies/{fr_id}/eligible-not-unlocked"), "nobody left eligible-not-unlocked afterwards")

print("\n=== 19. BATCH 3 — Draws: reproducible seed ===")
lb = jget("/referrals/leaderboard-v2"); check(len(lb) >= 1, "leaderboard has at least one referrer (Jordan)")
check(jpost("/referrals/draw/B", {"admin": "curtis"}).get("error") == "NO_ELIGIBLE_ENTRIES", "Draw B with nobody at 42+ -> NO_ELIGIBLE_ENTRIES")
da = jpost("/referrals/draw/A", {"admin": "curtis"})
check(da.get("error") is None and da.get("winner"), f"Draw A ran, winner {da.get('winner',{}).get('name')} from {da.get('entry_count')} entries")
# Reproduce: rebuild the entry list exactly as run_draw did, recompute the index from the stored seed.
import hashlib, struct
entries = []
for r in lb:
    entries += [r["id"]] * int(r["verified_referrals"])
idx = struct.unpack(">I", hashlib.sha256(da["seed"].encode()).digest()[:4])[0] % len(entries)
check(str(entries[idx]) == str(da["winner"]["id"]), "SEED REPRODUCES THE WINNER — independently recomputed from the stored seed and entry list")
hist = jget("/referrals/draw-history")
check(any(h["random_seed"] == da["seed"] for h in hist), "draw logged in history with its seed")
check(any(str(h["id"]) == str(da["draw_id"]) and h["random_seed"] == da["seed"] for h in hist), "returned draw_id matches the actual draw row (not the audit-log row id)")

print("\n=== 20. BATCH 3 — Subscription stats + reason capture ===")
check(all(k in jget("/subscription-stats") for k in ("by_category", "recent_unsubscribes")), "subscription stats shape ok")
tok5 = jget(f"/participants/{r5['participantId']}")["unsubscribe_token"]
check(jpost(f"/subscriptions/{tok5}/unsubscribe-with-reason", {"category": "general", "reason": "Too many emails"}).get("error") is None, "unsubscribe-with-reason ok")
check(any(x["unsubscribe_reason"] == "Too many emails" for x in jget("/subscription-stats")["recent_unsubscribes"]), "reason appears in admin stats")
check(jpost("/subscriptions/BOGUS/unsubscribe-with-reason", {"category": "all"}).get("error") == "INVALID_TOKEN", "bad token refused")

print("\n=== 21. BATCH 3 — Broadcast send-gate: enforced server-side ===")
check(jpost("/bulk-email/drafts", {"category": "general"}).get("error") == "SUBJECT_REQUIRED", "draft without subject refused")
check(jpost("/bulk-email/drafts", {"subject": "x", "category": "bogus"}).get("error") == "INVALID_CATEGORY", "draft with bad category refused")
dr = jpost("/bulk-email/drafts", {"subject": "Runners-only note", "category": "general", "audience_filter": "runners_only", "created_by": "curtis"})
did = dr["draft"]["id"]; check(dr.get("ready_for_bulk") is False, "new draft: gate locked")
blocked = jpost(f"/bulk-email/drafts/{did}/send-bulk", {"sent_by": "curtis"})
check(blocked.get("error") == "GATE_NOT_CLEARED" and "haven't sent the test" in blocked.get("message",""), "bulk send REFUSED before tests, with the exact warning copy")
check(jpost(f"/bulk-email/drafts/{did}/test-self", {}).get("error") == "EMAIL_REQUIRED", "test-self without email refused")
g1 = jpost(f"/bulk-email/drafts/{did}/test-self", {"admin_email": "curtis@runtheseas.com"})
check(g1.get("ready_for_bulk") is False, "after test-self only: still locked")
check(jpost(f"/bulk-email/drafts/{did}/send-bulk", {"sent_by": "curtis"}).get("error") == "GATE_NOT_CLEARED", "still refused with 1 of 2 tests")
check(jpost(f"/bulk-email/drafts/{did}/test-group", {"test_emails": "  , ,"}).get("error") == "NO_TEST_EMAILS_PROVIDED", "test-group with no real emails refused")
g2 = jpost(f"/bulk-email/drafts/{did}/test-group", {"test_emails": "sherry@x.com, kim@x.com", "sent_by": "curtis"})
check(g2.get("ready_for_bulk") is True, "after both tests: unlocked")
pv_all = jget("/broadcast/preview&category=general&audience_filter=all"); pv_run = jget("/broadcast/preview&category=general&audience_filter=runners_only")
check(pv_run["final_recipient_count"] <= pv_all["final_recipient_count"], f"audience filter narrows: runners {pv_run['final_recipient_count']} <= all {pv_all['final_recipient_count']}")
sent = jpost(f"/bulk-email/drafts/{did}/send-bulk", {"sent_by": "curtis"})
check(sent.get("error") is None and sent.get("was_forced") is False, "clean bulk send succeeded, not forced")
check(sent["final_recipient_count"] == pv_run["final_recipient_count"], "sent count == filtered preview count (audience filter honoured)")
check(jpost(f"/bulk-email/drafts/{did}/send-bulk", {"sent_by": "curtis"}).get("error") == "ALREADY_SENT", "double-send refused")
# Override path
dr2 = jpost("/bulk-email/drafts", {"subject": "Urgent", "category": "referral", "created_by": "curtis"}); did2 = dr2["draft"]["id"]
check(jpost(f"/bulk-email/drafts/{did2}/send-bulk", {"sent_by": "curtis", "force": True}).get("error") == "FORCE_REASON_REQUIRED", "force without reason refused")
fs = jpost(f"/bulk-email/drafts/{did2}/send-bulk", {"sent_by": "curtis", "force": True, "force_reason": "deadline"})
check(fs.get("was_forced") is True, "forced send succeeded and is flagged was_forced")
check(any("GATE OVERRIDDEN" in a["action"] for a in jget("/audit-log")), "override visible and distinct in audit log — not silent")

print("\n=== 22. BATCH 4 — Executive Dashboard v2 (expanded KPIs) ===")
ex = jget("/reports/executive-summary-v2")
for key in ("conversion_funnel","avg_referrals_per_founding_runner","geographic_distribution","marketing_source_breakdown","week_over_week_growth"):
    check(key in ex, f"v2 summary has '{key}'")
f = ex["conversion_funnel"]; check(f["registered"] >= f["verified"] >= f["credited"], f"funnel is monotone: {f['registered']} >= {f['verified']} >= {f['credited']}")
check(ex["cost_per_founding_runner"] is None, "cost_per_founding_runner honestly null (no ad-spend feed yet)")

print("\n=== 23. BATCH 4 — Global search ===")
check(jget("/search&q=a")["participants"] == [], "1-char query returns empty, not an error")
sr = jget("/search&q=Jordan"); check(any("Jordan" in p["name"] for p in sr["participants"]), "finds Jordan by name")
sr2 = jget("/search&q=Founding"); check(len(sr2["trophies"]) >= 1 or len(sr2["audit_log"]) >= 1, "search spans trophies/audit log too")
check(jget("/search&q=%25")["participants"] == [], "a literal '%' is escaped (esc_like) — does not match everything")

print("\n=== 24. BATCH 4 — Administrators are REAL WordPress users with REAL roles ===")
check(jpost("/admins", {"name": "x"}).get("error") == "NAME_EMAIL_ROLE_REQUIRED", "invite missing fields refused")
check(jpost("/admins", {"name": "X", "email": f"x-{suf}@rts.com", "role": "bogus"}).get("error") == "INVALID_ROLE", "invalid role refused")
check(jpost("/admins", {"name": "X", "email": "not-an-email", "role": "rts_administrator"}).get("error") == "INVALID_EMAIL", "invalid email refused")
sa = jpost("/admins", {"name": "Sherry", "email": f"sherry-{suf}@rts.com", "role": "rts_super_admin", "invited_by": "curtis"})
check(sa.get("error") is None and sa.get("admin_id"), f"invited super admin -> wp user id {sa.get('admin_id')}, login {sa.get('login')}")
ad = jpost("/admins", {"name": "Kim", "email": f"kim-{suf}@rts.com", "role": "rts_administrator", "invited_by": "curtis"})
check(ad.get("admin_id"), "invited administrator")
check(jpost("/admins", {"name": "Dup", "email": f"sherry-{suf}@rts.com", "role": "rts_administrator"}).get("error") == "EMAIL_ALREADY_INVITED", "duplicate email refused")
lst = jget("/admins"); check(any(a["id"] == sa["admin_id"] and a["role"] == "rts_super_admin" for a in lst), "new super admin appears in list with correct WP role")
# Lockout prevention: curtis (wp administrator) + sherry (rts_super_admin) = 2 supers. Demote sherry -> allowed. Then try to demote curtis -> refused (last super).
check(jpost(f"/admins/{sa['admin_id']}/role", {"role": "rts_contributor", "updated_by": "curtis"}).get("error") is None, "demote a super admin when another super exists -> allowed")
check(jpost(f"/admins/{sa['admin_id']}/role", {"role": "bogus"}).get("error") == "INVALID_ROLE", "change to invalid role refused")
curtis_id = next(a["id"] for a in jget("/admins") if a["login"] == "curtis")
check(jpost(f"/admins/{curtis_id}/role", {"role": "rts_contributor", "updated_by": "test"}).get("error") == "CANNOT_REMOVE_LAST_SUPER_ADMIN", "demoting the LAST super admin refused (lockout prevention)")
check(jpost(f"/admins/{curtis_id}/deactivate", {"deactivated_by": "test"}).get("error") == "CANNOT_DEACTIVATE_LAST_SUPER_ADMIN", "deactivating the LAST super admin refused")
check(jpost(f"/admins/{ad['admin_id']}/deactivate", {"deactivated_by": "curtis"}).get("error") is None, "deactivate a regular administrator -> allowed")
check(not any(a["id"] == ad["admin_id"] for a in jget("/admins")), "deactivated user no longer listed (roles stripped)")
check(jpost("/admins/999999/role", {"role": "rts_administrator"}).get("error") == "NOT_FOUND", "role change on nonexistent user -> NOT_FOUND")

print("\n=== 25. BATCH 4 — Backups + Security + Health ===")
bh0 = len(jget("/backups/history"))
bk = jpost("/backups/run", {"triggered_by": "curtis"}); check(bk.get("backup_id"), "backup logged")
check(len(jget("/backups/history")) == bh0 + 1, "backup appears in history")
sec = jget("/security/stats")
check(sec["failed_logins_24h"] is None and sec["active_sessions"] is None and "auth_note" in sec, "security honestly reports null for metrics WP core doesn't track, with a note")
check(any(r["role"] in ("administrator","rts_super_admin") for r in sec["role_distribution"]), "role distribution includes a super-admin-tier role")
check(sec["last_backup"] and str(sec["last_backup"]["id"]) == str(bk["backup_id"]), "security dashboard's last_backup is the one just run  (note: $wpdb returns ids as strings)")
h = jget("/system/health"); check(h["wp_version"] and h["php_version"], f"health reports WP {h['wp_version']} / PHP {h['php_version']}")

print("\n=== 26. BATCH 5 — Email Campaign Builder: real trigger, never double-sends ===")
check(jpost("/email-campaigns", {}).get("error") == "NAME_REQUIRED", "campaign without name refused")
ec = jpost("/email-campaigns", {"name": "Welcome Series", "trigger_type": "days_after_registration", "trigger_days": 0, "audience_filter": "all", "category": "general", "created_by": "curtis"})
cid = ec["campaign_id"]; check(cid, f"campaign created id {cid}")
check(any(str(c["id"]) == str(cid) and c["name"] == "Welcome Series" for c in jget("/email-campaigns")), "returned campaign_id matches the real row (insert_id not clobbered)")
check(jpost(f"/email-campaigns/{cid}/run-trigger-check", {"run_by": "curtis"}).get("error") == "CAMPAIGN_NOT_ACTIVE", "trigger on a DRAFT campaign refused")
check(jpost(f"/email-campaigns/{cid}/status", {"status": "bogus"}).get("error") == "INVALID_STATUS", "invalid status refused")
jpost(f"/email-campaigns/{cid}/status", {"status": "active", "updated_by": "curtis"})
t1 = jpost(f"/email-campaigns/{cid}/run-trigger-check", {"run_by": "curtis"})
check(t1.get("error") is None and t1["newly_sent"] > 0, f"first run sent to {t1.get('newly_sent')} eligible (excluded {t1.get('excluded_unsubscribed')} unsubscribed)")
t2 = jpost(f"/email-campaigns/{cid}/run-trigger-check", {"run_by": "curtis"})
check(t2.get("newly_sent") == 0, "SECOND run sends to ZERO — campaign_sends dedup works, no double-send")
check(t1["excluded_unsubscribed"] >= 1, "unsubscribed participants (e.g. Jordan from §9) were excluded — same audience function as Broadcast")
check(jpost(f"/email-campaigns/{cid}/status", {"status": "paused"}).get("error") is None, "pause works")
check(jpost(f"/email-campaigns/{cid}/run-trigger-check", {}).get("error") == "CAMPAIGN_NOT_ACTIVE", "trigger on paused campaign refused")

print("\n=== 27. BATCH 5 — Email Reporting (honest nulls) ===")
rp = jget("/email-reporting/stats")
check(rp["open_rate"] is None and rp["click_through_rate"] is None and rp["bounce_rate"] is None, "open/click/bounce honestly null (no provider)")
check(rp["total_campaign_sends"] == t1["newly_sent"], f"campaign sends ({rp['total_campaign_sends']}) == what the trigger actually sent")
check(rp["total_broadcast_sends"] >= 2, f"broadcast sends from §21 counted ({rp['total_broadcast_sends']})")

print("\n=== 28. BATCH 5 — Ad Campaign Analysis: REAL UTM attribution ===")
check(jpost("/ad-campaigns", {"name": "x"}).get("error") == "NAME_AND_UTM_REQUIRED", "ad campaign without utm refused")
check(jpost("/ad-campaigns", {"name": "Dup", "utm_campaign_code": "rts_runners_2544_lookalike"}).get("error") == "UTM_CODE_ALREADY_EXISTS", "duplicate UTM code refused")
before = next(c for c in jget("/ad-campaigns/stats") if c["utm_campaign_code"] == "rts_runners_2544_lookalike")
check(before["cac"] is None and before["cost_per_interested"] is None, "zero-conversion campaign shows null CAC/CPI — not a divide-by-zero")
ar = jpost("/participants/register", {"name": "Ad Attributed", "email": f"ad-{suf}@example.com", "runner_status": "runner", "country": "Canada", "utm_campaign": "rts_runners_2544_lookalike"})
check(jpost(f"/participants/{ar['participantId']}/notification-preference", {"wants_notification": True}).get("error") is None, "marked interested")
jget(f"/participants/verify/{ar['verificationToken']}")
after = next(c for c in jget("/ad-campaigns/stats") if c["utm_campaign_code"] == "rts_runners_2544_lookalike")
check(after["interested"] == before["interested"] + 1 and after["verified_credited"] == before["verified_credited"] + 1, "UTM attribution: +1 interested, +1 verified&credited")
check(after["cac"] == round(float(after["cost_charged"]) / after["verified_credited"], 2), f"CAC recomputed = ${after['cac']}")
check(jpost("/participants/999999/notification-preference", {"wants_notification": True}).get("error") == "NOT_FOUND", "pref on nonexistent participant -> NOT_FOUND")

print("\n=== 29. BATCH 5 — Interest & Notification lists are mutually exclusive ===")
pid = ar["participantId"]
check(any(str(p["id"]) == str(pid) for p in jget("/notification-list-v2")), "interested participant on notify list")
check(jpost(f"/participants/{pid}/declined-contact", {"declined": True, "reason": "Too many emails"}).get("error") is None, "declined further contact")
check(not any(str(p["id"]) == str(pid) for p in jget("/notification-list-v2")), "declining REMOVES from notify list in the same action")
check(any(str(p["id"]) == str(pid) for p in jget("/declined-contact-list-v2")), "…and adds to declined list — no overlap")

print("\n=== 30. BATCH 5 — Duplicate detection: heuristic flag, human decision, never reappears ===")
d1 = jpost("/participants/register", {"name": "Duplicate Test Person", "email": f"dup1-{suf}@example.com", "country": "France"})
d2 = jpost("/participants/register", {"name": "duplicate test person", "email": f"dup2-{suf}@example.com", "country": "France"})   # different case -> still flagged (LOWER)
scan = jget("/fraud/duplicate-scan")
pair = next((x for x in scan if {str(x["a"]["id"]), str(x["b"]["id"])} == {str(d1["participantId"]), str(d2["participantId"])}), None)
check(pair is not None, "same-name-same-country pair flagged (case-insensitive)")
fq = jget("/fraud/queue"); check(any({str(r["participant_id_a"]), str(r["participant_id_b"])} == {str(d1["participantId"]), str(d2["participantId"])} for r in fq["duplicate_reviews"]), "pair in pending review queue")
check(jpost("/fraud/duplicate-review", {"id_a": d1["participantId"], "id_b": d2["participantId"], "decision": "bogus"}).get("error") == "INVALID_DECISION", "invalid decision refused")
# pass ids in REVERSE order to prove canonical ordering works
check(jpost("/fraud/duplicate-review", {"id_a": d2["participantId"], "id_b": d1["participantId"], "decision": "approved_as_unique", "reviewed_by": "curtis"}).get("error") is None, "approve-as-unique accepted (ids given in reverse order)")
check(not any({str(x["a"]["id"]), str(x["b"]["id"])} == {str(d1["participantId"]), str(d2["participantId"])} for x in jget("/fraud/duplicate-scan")), "reviewed pair does NOT reappear on re-scan")
check(jpost("/fraud/duplicate-review", {"id_a": 999998, "id_b": 999999, "decision": "approved_as_unique"}).get("error") == "REVIEW_NOT_FOUND", "review of unknown pair -> REVIEW_NOT_FOUND")
# flagged referrals: self-referral was auto-rejected back in §6? no — Maria's was clean. Create a self-referral now to populate the queue.
sr = jpost("/participants/register", {"name": "Self Ref", "email": f"self-{suf}@example.com", "country": "Canada"})
jget(f"/participants/verify/{sr['verificationToken']}")
sref = jget(f"/participants/{sr['participantId']}")["referral_code"]
# a second account using the SAME email can't exist (dup check), so self-referral = same person referring themselves requires same email; emulate via referral row + same email is blocked. Instead assert the queue endpoint shape + reject path on Jordan's referral.
fq2 = jget("/fraud/queue"); check("flagged_referrals" in fq2 and "duplicate_reviews" in fq2, "fraud queue returns both sections")
jref = jget(f"/participants/{reg1['participantId']}")["referrals"]
check(len(jref) >= 1, "Jordan has a referral to act on")
rj = jpost(f"/fraud/referrals/{jref[0]['id']}/reject", {"admin": "curtis", "reason": "test"})
check(rj.get("error") is None, "referral rejected")
check(any(str(r["id"]) == str(jref[0]["id"]) and r["fraud_review_status"] == "rejected" for r in jget("/fraud/queue")["flagged_referrals"]), "rejected referral now appears in flagged queue")
check(jpost("/fraud/referrals/999999/reject", {"admin": "curtis", "reason": "x"}).get("error") == "NOT_FOUND", "reject nonexistent referral -> NOT_FOUND")

print("\n=== 31. BATCH 6 — Customer Feedback from real submitted comments ===")
qs = jget("/surveys/1/questions"); cq = next(q for q in qs if q["question_type"] == "comment")
fs = jpost("/surveys/1/start"); jpost(f"/responses/{fs['responseId']}/answers", {"question_id": cq["id"], "comment_text": "Would love a kids running category for my 8 year old"}); jpost(f"/responses/{fs['responseId']}/complete")
allc = jget("/feedback/comments")
check(any("kids running" in c["comment_text"] for c in allc), "submitted comment appears in the feedback feed")
check(jget(f"/feedback/comments&question_id={cq['id']}") and all(str(c["question_number"]) == str(cq["question_number"]) for c in jget(f"/feedback/comments&question_id={cq['id']}")), "filter by question_id works")
bd = jget(f"/feedback/breakdown/{qs[0]['id']}"); check(bd.get("error") is None and "breakdown" in bd, "response breakdown for Q1")
check(jget("/feedback/breakdown/999999").get("error") == "NOT_FOUND", "breakdown for unknown question -> NOT_FOUND")
th = jget("/feedback/themes"); check(any(t["keyword"] in ("kids","running","category") for t in th), "keyword extraction found a real word from the comment")
check(all(len(t["keyword"]) > 3 and t["keyword"] not in ("would","love") for t in th), "stopwords / short words filtered")
check(any(str(s["id"]) == str(cq["id"]) for s in jget("/feedback/comment-summary")), "comment summary lists the comment question")

print("\n=== 32. BATCH 6 — Question & Response Queue: full loop, append-only history ===")
check(jpost("/questions", {}).get("error") == "QUESTION_TEXT_REQUIRED", "question without text refused")
qn = jpost("/questions", {"question_text": "Is the $100 credit per person or per cabin?", "source": "manual"}); qid = qn["question_id"]
check(any(str(x["id"]) == str(qid) for x in jget("/questions/open")), "new question in open queue (oldest first)")
check(jpost(f"/questions/{qid}/send", {"approved_by": "curtis"}).get("error") == "NO_DRAFT_TO_SEND", "cannot send with no draft")
check(jpost(f"/questions/{qid}/drafts", {}).get("error") == "DRAFT_TEXT_REQUIRED", "draft without text refused")
check(jpost("/questions/999999/drafts", {"draft_text": "x"}).get("error") == "NOT_FOUND", "draft on unknown question -> NOT_FOUND")
check(jpost(f"/questions/{qid}/drafts", {"draft_text": "It is per person.", "created_by": "curtis"}).get("version") == 1, "draft v1")
check(jpost(f"/questions/{qid}/drafts", {"draft_text": "Great question! The $100 Cabin Credit is per person, not per cabin.", "feedback": "too short, add warmth", "created_by": "curtis"}).get("version") == 2, "draft v2 with feedback")
dh = jget(f"/questions/{qid}/drafts"); check(len(dh) == 2 and dh[1]["feedback_that_prompted_this"] == "too short, add warmth", "full history incl. the feedback that prompted v2")
sd = jpost(f"/questions/{qid}/send", {"approved_by": "curtis"}); check(sd.get("error") is None and sd["version_count"] == 2 and "per person" in sd["sent_text"], "approve & send logs latest draft + version count")
check(not any(str(x["id"]) == str(qid) for x in jget("/questions/open")), "answered question left the open queue")
check(any(str(l["customer_question_id"]) == str(qid) and l["approved_by"] == "curtis" for l in jget("/questions/response-log")), "response log has it — the static /response-log route was NOT swallowed by /questions/(?P<id>)")

print("\n=== 33. BATCH 6 — Who Is The Customer (verified + credited only) ===")
cp = jget("/customer-profile")
# "customer" = verified AND has a cabin_credits row (any status). Cross-check against the ledger.
led = jget("/cabin-credits/ledger"); verified_ids = {str(p["id"]) for p in jget("/participants") if str(p["email_verified"]) == "1"}
expected = len([c for c in led if str(c["participant_id"]) in verified_ids])
check(cp["total_customers"] == expected, f"total_customers ({cp['total_customers']}) == verified participants with a credit row ({expected}) — matches the spec definition exactly")
check(all(k in cp for k in ("gender_breakdown","age_breakdown","geographic_distribution","acquisition_breakdown","runner_breakdown","top_themes")), "profile has every breakdown")
check(any(g["k"] == "Canada" for g in cp["geographic_distribution"]), "geographic distribution reflects real data")

print("\n=== 34. BATCH 6 — Website CMS: genuinely wired to the public survey page ===")
check(jget("/content-blocks/survey_intro").get("error") == "NOT_FOUND", "block starts unset")
check(jpost("/content-blocks/survey_intro", {"value": f"Help shape the voyage — {suf}", "updated_by": "curtis"}).get("error") is None, "block saved")
check(jget("/content-blocks/survey_intro")["value"] == f"Help shape the voyage — {suf}", "block readable back")
check(jpost("/content-blocks/survey_intro", {"value": "v2", "updated_by": "curtis"}).get("error") is None and jget("/content-blocks/survey_intro")["value"] == "v2", "REPLACE updates in place (no duplicate key)")
check(any(b["block_key"] == "survey_intro" for b in jget("/content-blocks")), "block in list")
# The real proof that survey.js consumes it is in the browser test; here we at least confirm the public survey page HTML is served.
html = urllib.request.urlopen("http://localhost:8080/?page_id=6").read().decode()  # public page, anonymous on purpose
check("rts-survey-app" in html and "survey.js" in html, "public survey page serves the shortcode + survey.js that fetches the block")

print("\n=== 35. BATCH 6 — Export Center: real CSV ===")
check(jget("/export-center/download&dataset=bogus").get("error") == "INVALID_DATASET", "invalid dataset refused")
raw = _req("GET", "/export-center/download&dataset=participants&requested_by=curtis", raw=True)[1].decode()
first = raw.splitlines()[0]
check(first.startswith("id,name,email"), f"CSV header correct: {first[:40]}")
check(raw.count("\n") >= 5, f"CSV has {raw.count(chr(10))} data rows")
raw2 = _req("GET", "/export-center/download&dataset=comments", raw=True)[1].decode()
check("kids running category" in raw2, "comments export contains the real comment")
eh = jget("/export-center/history"); check(eh and eh[0]["dataset"] in ("comments","participants") and int(eh[0]["row_count"]) >= 1, "export logged to history with row_count")

print("\n=== 36. BATCH 7 — Report Builder: whitelisted, injection-safe ===")
check(jpost("/reports/preview", {"data_source": "bogus"}).get("error") == "INVALID_DATA_SOURCE", "invalid data source refused")
pv = jpost("/reports/preview", {"data_source": "participants", "fields": ["name","email","runner_status"], "filters": [{"field": "runner_status", "op": "equals", "value": "runner"}]})
check(pv.get("error") is None and pv["fields"] == ["name","email","runner_status"], "preview restricts to whitelisted fields")
check(all(r["runner_status"] == "runner" for r in pv["rows"]), "filter applied (all rows runner)")
inj = jpost("/reports/preview", {"data_source": "participants", "fields": ["name", "email; DROP TABLE wp_rts_participants;--"], "filters": [{"field": "name; DROP TABLE x", "op": "equals", "value": "x"}]})
check(inj.get("error") is None and inj["fields"] == ["name"], "non-whitelisted field/filter silently dropped, not interpolated")
check(len(jget("/participants")) > 0, "participants table intact after injection attempt")
check(jpost("/reports/preview", {"data_source": "participants", "fields": ["not_a_column"]}).get("error") == "NO_VALID_FIELDS", "all-invalid fields -> NO_VALID_FIELDS")
cont = jpost("/reports/preview", {"data_source": "participants", "fields": ["email"], "filters": [{"field": "email", "op": "contains", "value": "%"}]})
check(cont["row_count"] == 0, "literal '%' in contains is esc_like'd — matches nothing, not everything")
check(jpost("/reports/save", {"name": "x"}).get("error") == "NAME_AND_DATA_SOURCE_REQUIRED", "save without source refused")
sr = jpost("/reports/save", {"name": "All Runners", "data_source": "participants", "fields": ["name","email"], "filters": [{"field": "runner_status", "op": "equals", "value": "runner"}], "schedule_frequency": "weekly", "created_by": "curtis"})
rid = sr["report_id"]; check(rid, f"report saved id {rid}")
check(any(str(r["id"]) == str(rid) and r["name"] == "All Runners" for r in jget("/reports/saved")), "returned report_id matches real row")
rr = jpost(f"/reports/{rid}/run", {"run_by": "curtis"}); check(rr.get("error") is None and rr["fields"] == ["name","email"], "saved report ran with its stored fields")
check(any(str(r["id"]) == str(rid) and int(r["run_count"]) == 1 for r in jget("/reports/saved")), "run logged (run_count=1)")
check(jpost("/reports/999999/run", {}).get("error") == "NOT_FOUND", "run unknown report -> NOT_FOUND")

print("\n=== 37. BATCH 7 — Segments recount LIVE ===")
check(jpost("/segments/save", {"filters": []}).get("error") == "NAME_REQUIRED", "segment without name refused")
sp = jpost("/segments/preview", {"filters": [{"field": "country", "op": "equals", "value": "Canada"}]}); n0 = sp["row_count"]
ss = jpost("/segments/save", {"name": "Canadians", "filters": [{"field": "country", "op": "equals", "value": "Canada"}], "created_by": "curtis"}); sid = ss["segment_id"]
jpost("/participants/register", {"name": "New Canadian", "email": f"newcan-{suf}@example.com", "country": "Canada"})
seg = next(s for s in jget("/segments/saved") if str(s["id"]) == str(sid))
check(seg["live_count"] == n0 + 1, f"saved segment recounts live: {n0} -> {seg['live_count']} after adding a Canadian (not frozen at save time)")

print("\n=== 38. BATCH 7 — Quick Reports match their source screens ===")
qr = jget("/quick-reports")
cs = jget("/cabin-credits/summary-v2")
check(qr["founding_runners_and_credit"][0]["value"] == cs["issued"] + cs["deferred"], "Quick Reports 'credits issued' == Cabin Credit Management's issued+deferred — no discrepancy")
check(qr["participants_and_surveys"][1]["value"] == jget("/reports/executive-summary-v2")["verified_participants"], "Quick Reports 'verified participants' == Executive Dashboard's")
check(all(m["rel"] in ("independent","subset","overlaps","sum","events") for sec in qr.values() for m in sec), "every metric carries a valid relationship badge")

print("\n=== 39. BATCH 7 — Action Items: real rules, no duplicates, auto-close ===")
g1 = jpost("/action-items/generate", {}); open1 = jget("/action-items&status=open")
check(g1["new_count"] >= 1 and len(open1) >= 1, f"rules generated {g1['new_count']} item(s) from live data (deferred credit / flagged referral / pending dup exist)")
keys = [i["rule_key"] for i in open1]; check(len(keys) == len(set(keys)), "at most one open item per rule_key")
g2 = jpost("/action-items/generate", {}); check(g2["new_count"] == 0 and len(jget("/action-items&status=open")) == len(open1), "re-generate creates NO duplicates")
check(jpost(f"/action-items/{open1[0]['id']}/resolve", {"status": "bogus"}).get("error") == "INVALID_STATUS", "invalid resolve status refused")
check(jpost(f"/action-items/{open1[0]['id']}/resolve", {"status": "actioned", "outcome_note": "done", "resolved_by": "curtis"}).get("error") is None, "resolve works")
check(len(jget("/action-items&status=open")) == len(open1) - 1, "resolved item left the open list")
check(jpost("/action-items/999999/resolve", {"status": "dismissed"}).get("error") == "NOT_FOUND", "resolve unknown item -> NOT_FOUND")
# Auto-close: clear the 'open_questions_aging' condition isn't feasible (needs 3-day-old rows), so test auto-close on deferred credits:
# there IS a deferred credit from §17. Void it -> condition gone -> item should auto-close on next generate.
led = jget("/cabin-credits/ledger"); dfr = [c for c in led if c["status"] == "deferred"]
if dfr and any(i["rule_key"] == "deferred_credits_pending" for i in jget("/action-items&status=open")):
    # can't void a deferred credit (INVALID_STATE) — flip it via a fresh 'issued' path isn't possible either; simulate resolution by direct SQL
    import subprocess as _sp; _sp.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-e","UPDATE wp_rts_cabin_credits SET status='redeemed' WHERE status='deferred'"], check=True)
    g3 = jpost("/action-items/generate", {})
    check(g3["auto_closed"] >= 1 and not any(i["rule_key"] == "deferred_credits_pending" for i in jget("/action-items&status=open")), "condition resolved -> its item AUTO-CLOSED with a note (not left stale)")
    check(any(i["rule_key"] == "deferred_credits_pending" and i["outcome_note"] == "Condition auto-resolved" for i in jget("/action-items&status=actioned")), "auto-closed item carries 'Condition auto-resolved'")

print("\n=== 40. BATCH 7 — Forecast segments are mutually exclusive by construction ===")
fc = jget("/cabin-sales-forecast/segments")
cells = sum(fc[k]["runner"] + fc[k]["non_runner"] for k in ("verified_credited","referred_not_verified","interested_only","cold_traffic"))
check(cells == fc["total_addressable_pool"], f"8 cells sum ({cells}) == total addressable pool ({fc['total_addressable_pool']}) — each person in exactly one segment")
check(fc["total_addressable_pool"] == len(jget("/participants")), "total addressable pool == every participant (nobody dropped, nobody double-counted)")

print("\n=== 41. BATCH 7 — Founding Runner totals: honest 0 for the unbuilt integration ===")
ft = jget("/founding-runners/totals")
# with_credit = verified participants that have a credit row of ANY status (incl. redeemed/void); cross-check against the ledger.
led2 = jget("/cabin-credits/ledger"); ver = {str(p["id"]) for p in jget("/participants") if str(p["email_verified"]) == "1"}
check(ft["with_credit"] == len([c for c in led2 if str(c["participant_id"]) in ver]), f"with_credit ({ft['with_credit']}) == verified participants holding a credit row")
check(ft["without_credit"] == 0 and ft["without_credit_note"], "without_credit is honestly 0 with an explanatory note — not faked")
check(ft["total"] == ft["with_credit"] + ft["without_credit"], "total = with + without")

print("\n=== 42. BATCH 7 — Survey Logic Map from real data ===")
lm = jget("/surveys/1/logic-map")
cq3 = next((q for q in lm if q["conditional_on"]), None)
check(cq3 and cq3["conditional_on"]["required_answer"] == "Yes" and cq3["conditional_on"]["question_number"] == 2, "Q3 shown only if Q2 = Yes — extracted from live question rows")
check(sum(1 for q in lm if not q["conditional_on"]) == len(lm) - 1, "exactly one conditional question in the seeded survey")


print("\n=== 43. SECURITY — REST permission callbacks (generated from the live route registry) ===")
# 43a. Pull the registry WordPress actually registered, so the sweep can't drift from the code.
reg_raw = wp("eval", 'rest_get_server(); echo json_encode(RTS_Auth::registry());')
registry = json.loads(reg_raw)
EXPECTED_PUBLIC = {
    ("GET","/surveys/(?P<id>\\d+)/questions"), ("POST","/surveys/(?P<id>\\d+)/start"),
    ("POST","/responses/(?P<id>\\d+)/answers"), ("POST","/responses/(?P<id>\\d+)/complete"),
    ("POST","/participants/register"), ("GET","/participants/verify/(?P<token>[A-Za-z0-9]+)"),
    ("GET","/subscriptions/(?P<token>[A-Za-z0-9]+)"), ("POST","/subscriptions/(?P<token>[A-Za-z0-9]+)/unsubscribe"),
    ("POST","/subscriptions/(?P<token>[A-Za-z0-9]+)/resubscribe"), ("POST","/subscriptions/(?P<token>[A-Za-z0-9]+)/unsubscribe-with-reason"),
    ("GET","/content-blocks/(?P<key>[a-z0-9_\\-]+)"),
}
actual_public = {(r["methods"], r["route"]) for r in registry if r["cap"] == "public"}
check(len(registry) >= 100, f"registry has {len(registry)} routes")
check(actual_public == EXPECTED_PUBLIC, f"public route set is EXACTLY the 11 participant-facing routes (got {len(actual_public)})")
check(all(r["cap"] in ("public","rts_view","rts_manage","rts_send_bulk","rts_manage_admins","rts_system","rts_content") for r in registry), "every route carries a known capability (no unguarded routes)")

def concrete(route):
    # turn the registered regex into a concrete URL for the sweep
    return (route.replace("(?P<id>\\d+)", "1").replace("(?P<token>[A-Za-z0-9]+)", "NOPE")
                 .replace("(?P<type>[AB])", "A").replace("(?P<key>[a-z0-9_\\-]+)", "survey_intro"))

# 43b. Anonymous sweep — EVERY guarded route must answer 401 to an unauthenticated request.
# We do NOT special-case by HTTP verb: a GET that leaks PII is as bad as a POST that mutates.
leaks = []
for r in registry:
    if r["cap"] == "public": continue
    code = status(r["methods"], concrete(r["route"]), body={}, auth=None)
    if code != 401: leaks.append(f'{r["methods"]} {r["route"]} -> {code}')
check(not leaks, f"anonymous request REFUSED (401) on all {sum(1 for r in registry if r['cap']!='public')} guarded routes" + (f" — LEAKS: {leaks}" if leaks else ""))

# 43c. Public sweep — the 11 public routes must NOT be blocked by auth (any non-401/403 answer is fine; 404/400 are business responses).
blocked = []
for r in registry:
    if r["cap"] != "public": continue
    code = status(r["methods"], concrete(r["route"]), body={}, auth=None)
    if code in (401, 403): blocked.append(f'{r["methods"]} {r["route"]} -> {code}')
check(not blocked, "public participant routes still reachable anonymously" + (f" — BLOCKED: {blocked}" if blocked else ""))

# 43d. Wrong password is not a login.
check(status("GET", "/participants", auth=(ADMIN_USER, "definitely-wrong")) == 401, "wrong app password -> 401")

# 43e. Role matrix — real WordPress users with real RTS roles. Positive AND negative per role.
def mk(login, role):
    try: wp("user", "delete", login, "--yes")
    except RuntimeError: pass
    wp("user", "create", login, f"{login}@rts.test", f"--role={role}", "--user_pass=Xx-" + login)
    return (login, app_password(login, "rts-sec"))
CONTRIB = mk("rts_sec_contrib", "rts_contributor")
EDITOR  = mk("rts_sec_editor",  "rts_content_editor")
ADMIN2  = mk("rts_sec_admin",   "rts_administrator")

# Contributor: no platform-wide access (spec: "None" across the board)
for m, p_ in (("GET","/participants"), ("GET","/audit-log"), ("GET","/content-blocks"), ("POST","/content-blocks/survey_intro"), ("GET","/cabin-credits/summary"), ("GET","/reports/executive-summary-v2")):
    check(status(m, p_, body={"value":"x"}, auth=CONTRIB) == 403, f"contributor -> 403 on {m} {p_}")

# Content Editor: website content ONLY — can write a block, cannot read participants/finance/security
check(status("POST", "/content-blocks/survey_intro", body={"value":"editor was here","updated_by":"editor"}, auth=EDITOR) == 200, "content editor CAN update a content block")
check(jget("/content-blocks/survey_intro", auth=None).get("value") == "editor was here", "…and the change is real (readable anonymously, as the survey page does)")
check(status("GET", "/content-blocks", auth=EDITOR) == 200, "content editor CAN list content blocks")
for m, p_ in (("GET","/participants"), ("GET","/participants/1"), ("GET","/audit-log"), ("GET","/cabin-credits/ledger"), ("GET","/security/stats"), ("GET","/export-center/history"), ("POST","/participants/1/suspend"), ("POST","/bulk-email/drafts")):
    check(status(m, p_, body={"subject":"x"}, auth=EDITOR) == 403, f"content editor -> 403 on {m} {p_}")

# Administrator: operational yes; Tier-3 / admin-management / system NO
check(status("GET", "/participants", auth=ADMIN2) == 200, "administrator CAN read participants")
check(status("GET", "/audit-log", auth=ADMIN2) == 200, "administrator CAN read audit log")
check(jpost(f"/participants/{reg2['participantId']}/suspend", {"admin":"rts_sec_admin","reason":"role test"}, auth=ADMIN2).get("error") is None, "administrator CAN suspend a participant (rts_manage)")
check(jpost(f"/participants/{reg2['participantId']}/reinstate", {"admin":"rts_sec_admin"}, auth=ADMIN2).get("error") is None, "…and reinstate")
check(jpost("/bulk-email/drafts", {"subject":"admin draft","category":"general","created_by":"rts_sec_admin"}, auth=ADMIN2).get("error") is None, "administrator CAN create a broadcast draft (rts_send_bulk)")
for m, p_ in (("POST","/referrals/draw/A"), ("POST","/cabin-credits/void-all"), ("POST","/backups/run"), ("GET","/admins"), ("POST","/admins"), ("POST","/admins/1/role"), ("POST","/admins/1/deactivate")):
    check(status(m, p_, body={"adminUser":"x","reason":"x","name":"x","email":"x@x.com","role":"rts_administrator"}, auth=ADMIN2) == 403, f"administrator -> 403 on Tier-3/admin-mgmt {m} {p_}")

# Deactivation actually revokes access: deactivate ADMIN2 (strips roles) then re-check.
admin2_id = int(wp("user", "get", "rts_sec_admin", "--field=ID"))
check(jpost(f"/admins/{admin2_id}/deactivate", {"deactivated_by":"curtis"}).get("error") is None, "super admin deactivates the test administrator")
check(status("GET", "/participants", auth=ADMIN2) == 403, "deactivated administrator -> 403 (roles stripped; the app password alone grants nothing)")

# Super admin (curtis, WP administrator) — the whole suite above already proves full access; spot-check the three Tier-3 gates open for him:
check(status("GET", "/admins", auth=AUTH_ADMIN) == 200, "super admin CAN list admins (rts_manage_admins)")
check(jpost("/backups/run", {"triggered_by":"curtis"}).get("error") is None, "super admin CAN run backup (rts_system)")

# 43f. Public survey flow still works with NO credentials end-to-end (the thing users actually do).
qs_anon = jget("/surveys/1/questions", auth=None); check(isinstance(qs_anon, list) and len(qs_anon) >= 5, "anon: questions load")
st_anon = jpost("/surveys/1/start", auth=None); check("responseId" in st_anon, "anon: survey start")
rg_anon = jpost("/participants/register", {"name":"Anon Flow","email":f"anon-{suf}@example.com","country":"Canada"}, auth=None)
check(rg_anon.get("error") is None, "anon: register")
vf_anon = jget(f"/participants/verify/{rg_anon['verificationToken']}", auth=None); check(vf_anon.get("error") is None, "anon: verify via email-link token")
check(jget(f"/subscriptions/{vf_anon['participant']['unsubscribe_token']}", auth=None).get("error") is None, "anon: subscription status via token")
check(jpost(f"/subscriptions/{vf_anon['participant']['unsubscribe_token']}/unsubscribe", {"category":"all"}, auth=None).get("error") is None, "anon: unsubscribe via token")
# …but the same anonymous caller cannot read that participant back through the admin route:
check(status("GET", f"/participants/{rg_anon['participantId']}", auth=None) == 401, "anon: CANNOT read their own record through the admin route (PII stays behind auth)")

# cleanup test users + app passwords
for u in ("rts_sec_contrib","rts_sec_editor","rts_sec_admin"):
    try: wp("user","delete",u,"--yes")
    except RuntimeError: pass

print("\n=== 44. PRODUCTION — Emergency Take-Offline (real holding page, Tier-3, type-to-confirm) ===")
import urllib.request as _u2
def http(url, auth=None, method="GET", body=None):
    req=_u2.Request(url, method=method, data=(json.dumps(body).encode() if body is not None else None), headers={"Content-Type":"application/json"})
    if auth: req.add_header("Authorization","Basic "+base64.b64encode(f"{auth[0]}:{auth[1]}".encode()).decode())
    try: r=_u2.urlopen(req); return r.status, r.read()
    except urllib.error.HTTPError as e: return e.code, e.read()
check(jget("/system/status")["online"] is True, "status: online at start")
check(status("POST","/system/take-offline", body={"confirm":"OFFLINE"}, auth=None) == 401, "anon cannot take site offline (401)")
check(jpost("/system/take-offline", {}).get("error") == "CONFIRMATION_REQUIRED", "take-offline without typed confirmation refused")
off = jpost("/system/take-offline", {"confirm":"OFFLINE","admin_user":"curtis","message":"Back soon — test"})
check(off.get("error") is None and off["online"] is False, "site taken offline via REST (rts_system)")
c, body = http("http://localhost:8080/?page_id=6")
check(c == 503 and b"Back soon" in body and b"RUN THE SEAS" in body, f"PUBLIC survey page now serves the 503 holding page with the custom message (got {c})")
c, _ = http("http://localhost:8080/wp-login.php"); check(c == 200, "wp-login.php still reachable while offline")
check(status("GET","/participants", auth=AUTH_ADMIN) == 200, "REST API still works for admins while offline (team can investigate)")
c, _ = http("http://localhost:8080/wp-cron.php"); check(c in (200,), "wp-cron.php not blocked while offline")
check(any("TAKE-OFFLINE" in a["action"] for a in jget("/audit-log")), "take-offline is in the audit log")
ob = jget("/email/outbox"); check(any("taken OFFLINE" in o["subject"] for o in ob), "admin alert email generated (outbox)")
rs = jpost("/system/restore", {"admin_user":"curtis"}); check(rs.get("online") is True, "restored via REST")
c, _ = http("http://localhost:8080/?page_id=6"); check(c == 200, "public page back to 200 after restore")

print("\n=== 45. PRODUCTION — Email via wp_mail() with outbox; 'log' mode default; real verify link ===")
rg = jpost("/participants/register", {"name":"Mail Test","email":f"mail-{suf}@example.com","country":"Canada"}, auth=None)
ob = jget("/email/outbox"); v = next((o for o in ob if o["to_email"] == f"mail-{suf}@example.com" and o["kind"] == "transactional"), None)
check(v is not None, "registration generated a verification email in the outbox (transactional, no unsubscribe footer)")
check(v["mode"] == "log" and v["delivered"] is None, "default email mode is 'log' — nothing sent, delivered=null (not faked as delivered)")
# The outbox body must carry a working verification URL pointing at the public survey page
row = subprocess.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-N","-e",f"SELECT body_html FROM wp_rts_email_outbox WHERE id={v['id']}"],capture_output=True,text=True).stdout
import re as _re; m = _re.search(r'rts_verify=([A-Za-z0-9]+)', row)
check(m and m.group(1) == rg["verificationToken"], "outbox email contains the real ?rts_verify=TOKEN link for this participant")
check(jpost("/participants/register", {"name":"Bad","email":"not-an-email"}, auth=None).get("error") == "INVALID_EMAIL", "public register now validates email format (400)")
check(jpost("/participants/register", {"name":"<script>alert(1)</script>","email":f"xss-{suf}@example.com","country":"Canada","travel_party_size":9999}, auth=None).get("error") is None, "register accepts then sanitizes a hostile name / clamps party size")
xp = next(p for p in jget("/participants") if p["email"] == f"xss-{suf}@example.com")
check("<script>" not in xp["name"] and int(xp["travel_party_size"]) <= 50, f"stored name sanitized ({xp['name']!r}), party size clamped ({xp['travel_party_size']})")

print("\n=== 46. PRODUCTION — send-gate and campaigns now actually produce emails (rules unchanged) ===")
ob0 = len(jget("/email/outbox"))
d = jpost("/bulk-email/drafts", {"subject":"Hello {first_name}","body":"Your link: {referral_url}","category":"general","created_by":"curtis"}); did = d["draft"]["id"]
jpost(f"/bulk-email/drafts/{did}/test-self", {"admin_email":"curtis@runtheseas.com"})
jpost(f"/bulk-email/drafts/{did}/test-group", {"test_emails":"a@x.com, b@x.com","sent_by":"curtis"})
sb = jpost(f"/bulk-email/drafts/{did}/send-bulk", {"sent_by":"curtis"})
check(sb.get("error") is None and "delivery" in sb and sb["delivery"]["attempted"] == sb["final_recipient_count"], f"bulk send attempted {sb['delivery']['attempted']} emails == recipient count {sb['final_recipient_count']}")
ob1 = jget("/email/outbox"); new_ob = len(ob1) - ob0
check(new_ob >= 3 + sb["final_recipient_count"], f"outbox grew by {new_ob} (1 self test + 2 group tests + {sb['final_recipient_count']} bulk)")
mk_row = subprocess.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-N","-e","SELECT body_html FROM wp_rts_email_outbox WHERE kind='marketing' ORDER BY id DESC LIMIT 1"],capture_output=True,text=True).stdout
check("{first_name}" not in mk_row and "ref=RTS-" in mk_row and "Manage or unsubscribe" in mk_row, "marketing email: merge fields resolved AND unsubscribe footer auto-appended (CASL/CAN-SPAM)")
check("Manage or unsubscribe" not in row, "transactional verification email has NO unsubscribe footer")

print("\n=== 47. PRODUCTION — rate limiting on public routes ===")
wp("eval","RTS_Production::set('rate_limit_register', 2);")
subprocess.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-e","DELETE FROM wp_options WHERE option_name LIKE '_transient%rts_rl_%'"],check=True)  # clear counters from earlier registrations in this run
codes=[status("POST","/participants/register", body={"name":"RL","email":f"rl{i}-{suf}@example.com","country":"Canada"}, auth=None) for i in range(4)]
check(codes[:2] == [200,200] and 429 in codes[2:], f"3rd/4th registration from same IP within the hour -> 429 (codes {codes})")
wp("eval","RTS_Production::set('rate_limit_register', 100);")
subprocess.run(["mysql","-u","rts_user","-prts_dev_pass_2026","wordpress_rts","-e","DELETE FROM wp_options WHERE option_name LIKE '_transient%rts_rl_%'"],check=True)
check(status("POST","/participants/register", body={"name":"RL","email":f"rl9-{suf}@example.com","country":"Canada"}, auth=None) == 200, "limit restored and counters cleared -> 200 again")

print("\n=== 48. PRODUCTION — AI integration point: honest when unconfigured; real request shape when configured ===")
r = jpost("/ai/draft", {"task":"question_reply","question":"Is the credit per person?"})
check(r.get("error") == "AI_NOT_CONFIGURED", "no API key -> AI_NOT_CONFIGURED (409), never a fake AI answer")
check(status("POST","/ai/draft", body={"task":"question_reply","question":"x"}, auth=None) == 401, "anon cannot call AI endpoint")
# Configure a dummy key and intercept the outbound HTTP with a must-use plugin that mocks the provider,
# proving the request is built correctly and the response is parsed — without a real key or network.
mu = "/var/www/rts-wordpress/wp-content/mu-plugins"; os.makedirs(mu, exist_ok=True)
open(mu+"/rts-test-ai-mock.php","w").write("""<?php
add_filter('pre_http_request', function($pre, $args, $url){
  if (strpos($url,'api.anthropic.com')===false) return $pre;
  $b = json_decode($args['body'], true);
  $ok = isset($args['headers']['x-api-key']) && $args['headers']['x-api-key']==='test-key-123' && !empty($b['model']) && !empty($b['messages'][0]['content']);
  return array('response'=>array('code'=>$ok?200:400,'message'=>'OK'),'body'=>json_encode(array('content'=>array(array('type'=>'text','text'=>'MOCKED DRAFT for: '.$b['messages'][0]['content'])))),'headers'=>array(),'cookies'=>array());
}, 10, 3);
""")
wp("eval","RTS_Production::set('ai_api_key', 'test-key-123');")
r = jpost("/ai/draft", {"task":"question_reply","question":"Is the credit per person?","facts":"yes per person"})
check(r.get("error") is None and r["draft"].startswith("MOCKED DRAFT") and "Is the credit per person" in r["draft"], "with a key: real wp_remote_post to the provider, correct auth header + payload, response parsed into a draft")
os.remove(mu+"/rts-test-ai-mock.php"); wp("eval","RTS_Production::set('ai_api_key', '');")
check(jpost("/ai/draft", {"task":"question_reply","question":"x"}).get("error") == "AI_NOT_CONFIGURED", "key removed -> back to NOT_CONFIGURED")

print("\n=== 49. PRODUCTION — External Founding Runners: import + email-match sync ===")
t0 = jget("/founding-runners/totals")
imp = jpost("/founding-runners/import", {"rows":[{"name":"Ext One","email":f"ext1-{suf}@example.com"},{"name":"Ext Jordan","email":f"jordan-{suf}@example.com"},{"name":"bad","email":"nope"}],"source":"main_site_test","imported_by":"curtis"})
check(imp.get("error") is None and imp["inserted"] == 2 and imp["skipped"] == 1, f"import: 2 inserted, 1 skipped (invalid email) — {imp}")
check(imp["matched_now"] == 1, "Jordan's external record matched to his local participant by email immediately")
t1 = jget("/founding-runners/totals")
check(t1["without_credit"] == t0["without_credit"] + 1 and t1["without_credit_note"] is None, "Without-Credit now 1 real unmatched external FR (note gone), total = with + without")
check(status("POST","/founding-runners/import", body={"rows":[]}, auth=None) == 401, "anon cannot import")
check(jpost("/founding-runners/sync", {}).get("newly_matched") == 0, "re-sync is idempotent")

print("\n=== 50. PRODUCTION — forms adapter + cron hooks run for real ===")
fa = wp("eval", f'$r = RTS_Production::register_from_form(array("first_name"=>"Form","last_name"=>"Adapter","email"=>"form-{suf}@example.com","country"=>"Canada"), "gravity_forms"); echo json_encode($r);')
fr = json.loads(fa); check(fr.get("error") is None and fr.get("participant_id"), "register_from_form() registered a participant from a forms-plugin payload")
check(any(p["email"] == f"form-{suf}@example.com" and p["marketing_source"] == "gravity_forms" for p in jget("/participants")), "…with marketing_source set from the form source")
check(any(o["to_email"] == f"form-{suf}@example.com" for o in jget("/email/outbox")), "…and a verification email generated for it")
check(wp("eval", 'echo json_encode(RTS_Production::register_from_form(array("email"=>"bad"), "x"));') == '{"error":"INVALID_EMAIL"}', "adapter rejects bad email")
for hook in ("rts_cron_campaign_triggers","rts_cron_scheduled_reports","rts_cron_action_items","rts_cron_fr_sync"):
    out = wp("cron","event","run",hook); check("Executed" in out or "Success" in out, f"WP-Cron hook {hook} executes")
check(wp("option","get","rts_cron_last_fr_sync") != "", "cron run recorded its last-run timestamp")

print("\n=== 51. ROLE CONSISTENCY — rts_administrator can use the wp-admin screens; blocked from Tier-3 + core WP admin ===")
# (browser-level proof is in the Playwright check; here we verify the capability gates via admin_post handlers)
ADMIN3 = mk("rts_sec_admin3", "rts_administrator")
a3_id = int(wp("user","get","rts_sec_admin3","--field=ID"))
caps = json.loads(wp("eval", f'echo json_encode(array_values(array_filter(array_keys(get_userdata({a3_id})->allcaps), fn($c)=>str_starts_with($c,"rts_") || in_array($c,["manage_options","edit_posts","read"]))));'))
check("rts_view" in caps and "rts_manage" in caps and "rts_send_bulk" in caps and "rts_manage_admins" not in caps and "rts_system" not in caps and "manage_options" not in caps, f"rts_administrator caps: {sorted(caps)}")
page_caps = json.loads(wp("eval", 'require_once ABSPATH."wp-admin/includes/plugin.php"; do_action("admin_menu"); echo json_encode(RTS_Auth::pages());'))
check(len(page_caps) >= 35, f"{len(page_caps)} admin pages registered through RTS_Auth::page() (each must name an rts_* cap)")
check(page_caps.get("rts-participants")=="rts_view" and page_caps.get("rts-admins")=="rts_manage_admins" and page_caps.get("rts-security")=="rts_system" and page_caps.get("rts-backup")=="rts_system" and page_caps.get("rts-settings")=="rts_system" and page_caps.get("rts-cms")=="rts_content", "admin pages are gated by the same rts_* caps as the REST routes (spot-checked 6)")
check(all(v != "manage_options" for v in page_caps.values()), "no RTS admin page is gated by manage_options any more")
act_caps = json.loads(wp("eval", 'echo json_encode(RTS_Auth::ACTION_CAPS);'))
check(act_caps["run_draw"]=="rts_system" and act_caps["invite_admin"]=="rts_manage_admins" and act_caps["bc_send_bulk"]=="rts_send_bulk" and act_caps["cms_save"]=="rts_content" and act_caps["suspend"]=="rts_manage", "admin_post actions map to the same caps as their REST counterparts")
check(wp("eval", 'echo RTS_Auth::action_cap("totally_unknown_action");') == "do_not_allow", "unknown admin_post action -> do_not_allow (fails closed)")
try: wp("user","delete","rts_sec_admin3","--yes")
except RuntimeError: pass

try: wp("user","application-password","delete",ADMIN_USER,"--all")
except RuntimeError: pass
print(f"\n{'='*60}\nRESULT: {passed} passed, {failed} failed")
sys.exit(0 if failed == 0 else 1)
