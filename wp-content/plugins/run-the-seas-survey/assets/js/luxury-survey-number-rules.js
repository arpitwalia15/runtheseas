(function () {
  "use strict";

  function addQuestionBadge(group, heading) {
    if (!heading || group.querySelector(":scope > .rts-luxury-survey__question-number")) return false;

    var walker = document.createTreeWalker(heading, NodeFilter.SHOW_TEXT);
    var textNode;
    while ((textNode = walker.nextNode())) {
      var match = String(textNode.nodeValue || "").match(/^\s*(\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*)\s*[.)]\s*/);
      if (!match) continue;

      textNode.nodeValue = String(textNode.nodeValue || "").replace(match[0], "");
      var badge = document.createElement("span");
      badge.className = "rts-luxury-survey__question-number";
      badge.setAttribute("aria-hidden", "true");
      badge.innerHTML = '<b class="rts-luxury-survey__question-number-text"></b>';
      badge.firstChild.textContent = match[1];
      group.insertBefore(badge, group.firstChild);
      group.classList.toggle("has-long-question-code", match[1].length > 2);
      return true;
    }

    return false;
  }

  function configureRatingMatrix(step) {
    var ratingInputs = step.querySelectorAll("input[type='number'][min='1'][max='10']");
    if (ratingInputs.length < 2) return;

    var headingGroup = Array.prototype.find.call(
      step.querySelectorAll(":scope > .ff-custom_html"),
      function (group) {
        return /^\s*\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*\s*[.)]\s*/.test(String(group.textContent || ""));
      }
    );
    if (!headingGroup) return;

    step.classList.add("rts-luxury-survey__rating-matrix");
    headingGroup.classList.add("rts-luxury-survey__rating-matrix-heading");
    addQuestionBadge(
      headingGroup,
      headingGroup.querySelector("h1, h2, h3, h4, h5, h6, p, legend, strong")
    );

    var columnContainers = step.querySelectorAll(":scope > .ff-t-container");
    Array.prototype.forEach.call(columnContainers, function (container) {
      if (container.querySelector("input[type='number'][min='1'][max='10']")) {
        container.classList.add("rts-luxury-survey__rating-row");
      } else if (/activity/i.test(container.textContent || "") && /rating/i.test(container.textContent || "")) {
        container.classList.add("rts-luxury-survey__rating-header");
      }
    });

    ratingInputs.forEach(function (input) {
      input.classList.add("rts-luxury-survey__rating-input");
      input.setAttribute("step", "1");
      input.setAttribute("inputmode", "numeric");
      var fieldGroup = input.closest(".ff-el-group");
      if (fieldGroup) {
        fieldGroup.classList.add("rts-luxury-survey__rating-field");
        fieldGroup.classList.remove("rts-luxury-survey__unnumbered-question");
      }
    });
  }

  function applyNumberRules(wrapper) {
    wrapper.querySelectorAll(".fluentform-step").forEach(configureRatingMatrix);

    wrapper.querySelectorAll(".fluentform-step .ff-el-group").forEach(function (group) {
      var label = group.querySelector(".ff-el-input--label > label");

      if (!label) return;

      var original = label.getAttribute("data-rts-original-label") ||
        String(label.textContent || "").trim();
      var hasExplicitCode = /^\s*\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*\s*[.)]\s*.+$/.test(original);
      var isQuestionInput = !!group.querySelector("input:not([type='hidden']):not([type='button']):not([type='submit']), select");

      var isRatingField = group.classList.contains("rts-luxury-survey__rating-field");
      group.classList.toggle("rts-luxury-survey__unnumbered-question", !isRatingField && isQuestionInput && !hasExplicitCode);
    });
  }

  function initialise(wrapper) {
    if (wrapper.getAttribute("data-rts-number-rules-ready") === "1") {
      applyNumberRules(wrapper);
      return;
    }

    wrapper.setAttribute("data-rts-number-rules-ready", "1");
    var queued = false;
    var schedule = function () {
      if (queued) return;
      queued = true;
      window.requestAnimationFrame(function () {
        queued = false;
        applyNumberRules(wrapper);
      });
    };

    new MutationObserver(schedule).observe(wrapper, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ["class"],
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
