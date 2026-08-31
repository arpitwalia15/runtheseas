(function () {
  "use strict";

  function renderBadge(group, code) {
    group.classList.add("rts-luxury-survey__numbered-question");
    group.classList.toggle("has-long-question-code", code.length > 2);

    var badge = group.querySelector(":scope > .rts-luxury-survey__question-number");
    if (!badge) {
      badge = document.createElement("span");
      badge.className = "rts-luxury-survey__question-number";
      badge.setAttribute("aria-hidden", "true");
      group.insertBefore(badge, group.firstChild);
    }

    badge.setAttribute("data-rts-question-code", code);
    var text = badge.querySelector(".rts-luxury-survey__question-number-text");
    if (!text) {
      badge.textContent = "";
      text = document.createElement("b");
      text.className = "rts-luxury-survey__question-number-text";
      badge.appendChild(text);
    }
    if (text.textContent !== code) text.textContent = code;
  }

  function upgradeStep(step, stepNumber) {
    step.querySelectorAll(".ff-el-group").forEach(function (group) {
      var label = group.querySelector(".ff-el-input--label > label");
      if (!label) return;

      var original = label.getAttribute("data-rts-original-label") ||
        String(label.textContent || "").trim();
      var match = original.match(/^\s*(\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*)\s*[.)]\s*(.+)$/);
      var code = match ? match[1] : "";

      if (!code) return;

      if (!label.hasAttribute("data-rts-original-label")) {
        label.setAttribute("data-rts-original-label", original);
      }
      if (match && String(label.textContent || "").trim() !== match[2]) {
        label.textContent = match[2];
      }

      renderBadge(group, code);
    });
  }

  function upgradeSurvey(wrapper) {
    var steps = wrapper.querySelectorAll(".fluentform-step");
    steps.forEach(function (step, index) {
      upgradeStep(step, index + 1);
    });
  }

  function initialise(wrapper) {
    if (wrapper.getAttribute("data-rts-badges-ready") === "1") {
      upgradeSurvey(wrapper);
      return;
    }

    wrapper.setAttribute("data-rts-badges-ready", "1");
    var queued = false;
    var schedule = function () {
      if (queued) return;
      queued = true;
      window.requestAnimationFrame(function () {
        queued = false;
        upgradeSurvey(wrapper);
      });
    };

    new MutationObserver(schedule).observe(wrapper, {
      childList: true,
      subtree: true,
    });
    schedule();
  }

  function initialiseAll() {
    document.querySelectorAll("[data-rts-luxury-survey]").forEach(initialise);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initialiseAll);
  } else {
    initialiseAll();
  }
  document.addEventListener("fluentform_loaded", initialiseAll);
})();
