(function () {
  const API = rtsConfig.apiUrl;
  const surveyId = rtsConfig.surveyId;
  let questions = [], answers = {}, responseId = null, currentIndex = 0;

  async function jget(url) { return (await fetch(url)).json(); }
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); // never inject user text as HTML
  async function jpost(url, body) { return (await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body || {}) })).json(); }

  function visibleQuestions() {
    return questions.filter(q => {
      if (!q.conditional_on_question_id) return true;
      return answers[q.conditional_on_question_id] === q.conditional_equals;
    });
  }

  async function initSurvey() {
    const app = document.getElementById('rts-survey-app');
    if (!app) return;
    // Real verification link from the email: /survey/?rts_verify=TOKEN
    const vt = new URLSearchParams(window.location.search).get('rts_verify');
    if (vt) { await verifyParticipant(vt); return; }
    // Pull the intro from the REAL CMS (Batch 6, Website Content Management). If an admin edits
    // the block in wp-admin, this page shows the new text on next load — not a disconnected form.
    try {
      const blk = await jget(`${API}/content-blocks/survey_intro`);
      if (blk && blk.value) {
        const intro = document.createElement('div');
        intro.id = 'rts-survey-intro';
        intro.style.cssText = 'background:#FBF3DD;border:1px solid #C9A24B;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;';
        intro.textContent = blk.value;
        app.parentNode.insertBefore(intro, app);
      }
    } catch (e) { /* block not set yet — nothing shown */ }
    questions = await jget(`${API}/surveys/${surveyId}/questions`);
    const start = await jpost(`${API}/surveys/${surveyId}/start`);
    responseId = start.responseId;
    renderQuestion();
  }

  function renderQuestion() {
    const app = document.getElementById('rts-survey-app');
    const vis = visibleQuestions();
    if (currentIndex >= vis.length) return renderRegistration();
    const q = vis[currentIndex];
    const pct = Math.round((currentIndex / vis.length) * 100);

    let inputHtml = '';
    if (['multiple_choice', 'yes_no', 'dropdown'].includes(q.question_type)) {
      inputHtml = '<div class="rts-options">' + q.options_json.map((opt, idx) =>
        `<div class="rts-option ${answers[q.id] === opt ? 'selected' : ''}" data-idx="${idx}">${esc(opt)}</div>`
      ).join('') + '</div>';
    } else if (q.question_type === 'comment') {
      inputHtml = `<textarea id="rts-free-input" placeholder="Optional — anything you'd like us to know">${answers[q.id] || ''}</textarea>`;
    } else {
      inputHtml = `<input type="text" id="rts-free-input" value="${answers[q.id] || ''}">`;
    }

    app.innerHTML = `
      <div class="rts-progress"><div class="rts-progress-bar" style="width:${pct}%"></div></div>
      <div class="rts-card">
        <div class="rts-qcounter">Question ${currentIndex + 1} of ${vis.length}</div>
        <div class="rts-qprompt">${esc(q.prompt)}</div>
        ${inputHtml}
        <div class="rts-btnrow">
          <button class="rts-btn rts-secondary" id="rts-prev" ${currentIndex === 0 ? 'disabled' : ''}>← Previous</button>
          <button class="rts-btn" id="rts-next">Next →</button>
        </div>
      </div>
    `;

    document.querySelectorAll('.rts-option').forEach(el => {
      el.addEventListener('click', () => {
        answers[q.id] = q.options_json[parseInt(el.dataset.idx, 10)];
        submitAndAdvance(q);
      });
    });
    document.getElementById('rts-prev').addEventListener('click', () => { currentIndex--; renderQuestion(); });
    document.getElementById('rts-next').addEventListener('click', () => {
      const freeInput = document.getElementById('rts-free-input');
      if (freeInput) answers[q.id] = freeInput.value;
      // q.required arrives from $wpdb as the STRING "1"/"0" — "0" is truthy, so coerce explicitly,
      // otherwise the optional comment question would be wrongly enforced as required.
      if (String(q.required) === '1' && !answers[q.id]) { alert('This question is required.'); return; }
      submitAndAdvance(q);
    });
  }

  function submitAndAdvance(q) {
    const isComment = q.question_type === 'comment';
    jpost(`${API}/responses/${responseId}/answers`, {
      question_id: q.id,
      answer_value: isComment ? null : answers[q.id],
      comment_text: isComment ? answers[q.id] : null,
    });
    currentIndex++;
    renderQuestion();
  }

  function renderRegistration() {
    jpost(`${API}/responses/${responseId}/complete`);
    const app = document.getElementById('rts-survey-app');
    app.innerHTML = `
      <div class="rts-card">
        <h2>Thanks for completing the survey! 🎉</h2>
        <p>Want a $100 Founding Runner Cabin Credit? Register below.</p>
        <label>Full Name</label><input type="text" id="rts-reg-name">
        <label>Email</label><input type="email" id="rts-reg-email">
        <label>Country</label><input type="text" id="rts-reg-country" value="Canada">
        <button class="rts-btn" id="rts-register-btn">Claim My $100 Cabin Credit →</button>
        <div id="rts-reg-msg"></div>
      </div>
    `;
    document.getElementById('rts-register-btn').addEventListener('click', registerParticipant);
  }

  async function registerParticipant() {
    const name = document.getElementById('rts-reg-name').value;
    const email = document.getElementById('rts-reg-email').value;
    const country = document.getElementById('rts-reg-country').value;
    if (!name || !email) { alert('Name and email are required.'); return; }

    const params = new URLSearchParams(window.location.search);
    const refCode = params.get('ref');

    const result = await jpost(`${API}/participants/register`, { name, email, country, marketing_source: refCode ? 'referral' : 'direct', referred_by_code: refCode });

    if (result.error === 'DUPLICATE_EMAIL') {
      document.getElementById('rts-survey-app').innerHTML = '<div class="rts-card"><h2>Already registered!</h2><p>That email is already a Founding Runner.</p></div>';
      return;
    }

    const sendMode = rtsConfig.emailMode === 'send';
    document.getElementById('rts-survey-app').innerHTML = `
      <div class="rts-card">
        <h2>Check your email 📬</h2>
        <p>${sendMode ? 'We just sent a confirmation link to <b>' + esc(email) + '</b>. Click it to claim your $100 Cabin Credit.' : '(Email delivery is in <b>log</b> mode on this site — the verification email was written to the outbox, not sent. Click below to follow the link it contains.)'}</p>
        ${sendMode ? '' : '<button class="rts-btn" id="rts-verify-btn">Follow verification link</button>'}
      </div>
    `;
    if (!sendMode) document.getElementById('rts-verify-btn').addEventListener('click', () => verifyParticipant(result.verificationToken));
  }

  async function verifyParticipant(token) {
    const result = await jget(`${API}/participants/verify/${token}`);
    const app = document.getElementById('rts-survey-app');
    if (result.error) {
      app.innerHTML = `<div class="rts-card"><h2>Link not recognized</h2><p>This verification link is invalid or was already used. ${esc(result.error)}</p></div>`; return;
    }
    const p = result.participant;
    const already = result.already_verified ? '<p style="color:#9A6B10">This email was already verified — here are your details again.</p>' : '';
    app.innerHTML = `
      <div class="rts-card">
        <h2>You're verified, ${esc(String(p.name||'').split(' ')[0])}! ⚓</h2>${already}
        <p>Founding Runner Number: <b>${esc(p.founding_runner_number)}</b></p>
        <p style="color:#1E7B4D;font-weight:700;">$100 Cabin Credit Issued</p>
        <p>Your referral link: <code>${esc(window.location.origin + window.location.pathname)}?ref=${esc(p.referral_code)}</code></p>
      </div>
    `;
  }

  // ----- Unsubscribe page -----

  async function initUnsubscribe() {
    const app = document.getElementById('rts-unsubscribe-app');
    if (!app) return;
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    if (!token) { app.innerHTML = '<div class="rts-card"><h2>Missing link</h2></div>'; return; }

    const status = await jget(`${API}/subscriptions/${token}`);
    if (status.error) { app.innerHTML = '<div class="rts-card"><h2>Link not recognized</h2></div>'; return; }

    const labels = { survey: 'Survey & research updates', referral: 'Referral updates', trophy: 'Trophy notifications', general: 'General news' };
    app.innerHTML = `
      <div class="rts-card">
        <h2>Hi ${esc(String(status.participant.name||'').split(' ')[0])},</h2>
        <p>Manage what you hear from us.</p>
        ${status.subscriptions.map(s => {
          // $wpdb returns numeric columns as STRINGS ("1"/"0"). The string "0" is truthy in JS,
          // so a naive `s.subscribed ? ...` would never show the unsubscribed state. Coerce first.
          const isSub = String(s.subscribed) === '1';
          return `
          <div class="rts-sub-row">
            <span>${labels[s.category] || s.category}</span>
            <button class="rts-btn rts-secondary rts-toggle" data-cat="${s.category}" data-subscribed="${isSub ? '1' : '0'}">
              ${isSub ? 'Subscribed — click to unsubscribe' : 'Unsubscribed — click to resubscribe'}
            </button>
          </div>
        `; }).join('')}
      </div>
    `;
    document.querySelectorAll('.rts-toggle').forEach(btn => {
      btn.addEventListener('click', async () => {
        const cat = btn.dataset.cat;
        const subscribed = btn.dataset.subscribed === '1';
        if (subscribed) { await jpost(`${API}/subscriptions/${token}/unsubscribe`, { category: cat }); }
        else { await jpost(`${API}/subscriptions/${token}/resubscribe`, { category: cat }); }
        initUnsubscribe();
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initSurvey();
    initUnsubscribe();
  });
})();
