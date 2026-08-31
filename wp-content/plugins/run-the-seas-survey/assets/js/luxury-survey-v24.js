(function () {
  "use strict";

  function parseSections(wrapper) {
    try {
      var sections = JSON.parse(wrapper.getAttribute("data-sections") || "[]");
      return Array.isArray(sections) ? sections : [];
    } catch (error) {
      return [];
    }
  }

  function parseQuestionImages(wrapper) {
    try {
      var images = JSON.parse(wrapper.getAttribute("data-question-images") || "[]");
      return Array.isArray(images) ? images : [];
    } catch (error) {
      return [];
    }
  }

  function getSteps(form) {
    var steps = form.querySelectorAll(".ff-step-body .fluentform-step");
    if (!steps.length) {
      steps = form.querySelectorAll(".fluentform-step");
    }
    return Array.prototype.slice.call(steps);
  }

  function canOpenStepTitle(stepTitle) {
    return !!(stepTitle && (
      stepTitle.classList.contains("ff_completed") ||
      stepTitle.classList.contains("ff_active") ||
      stepTitle.classList.contains("rts-question-nav-is-complete") ||
      stepTitle.classList.contains("rts-question-nav-is-active")
    ));
  }

  function openStepFromTitle(form, stepNumber) {
    if (!form) return false;

    var zeroBasedStep = Math.max(0, (parseInt(stepNumber, 10) || 1) - 1);
    var stepTitle = form.querySelector(
      ".ff-step-titles [data-step-number='" + zeroBasedStep + "']"
    );

    if (!canOpenStepTitle(stepTitle)) return false;

    stepTitle.click();
    return true;
  }

  function setupQuestionNavigator(form) {
    var header = form.querySelector(".ff-step-header--tabs");
    var stepBody = form.querySelector(".ff-step-body");
    if (!header || !stepBody) return null;

    var titles = header.querySelector(".ff-step-titles");
    if (!titles) return null;

    header.classList.add("rts-luxury-survey__question-nav");

    // Fluent Forms renders tab headers before the questions. The reference
    // layout places the same native control after the active step's buttons.
    if (stepBody.nextElementSibling !== header) {
      stepBody.insertAdjacentElement("afterend", header);
    }

    if (!header.querySelector("[data-rts-question-nav-status]")) {
      var previous = document.createElement("button");
      previous.type = "button";
      previous.className = "rts-luxury-survey__question-nav-arrow rts-luxury-survey__question-nav-arrow--previous";
      previous.setAttribute("data-rts-question-nav-direction", "previous");
      previous.setAttribute("aria-label", "Go to the previous completed question");
      previous.textContent = "\u2039";

      var status = document.createElement("strong");
      status.className = "rts-luxury-survey__question-nav-status";
      status.setAttribute("data-rts-question-nav-status", "");
      status.setAttribute("aria-live", "polite");

      var next = document.createElement("button");
      next.type = "button";
      next.className = "rts-luxury-survey__question-nav-arrow rts-luxury-survey__question-nav-arrow--next";
      next.setAttribute("data-rts-question-nav-direction", "next");
      next.setAttribute("aria-label", "Go to the next completed question");
      next.textContent = "\u203a";

      header.insertBefore(previous, titles);
      header.insertBefore(status, titles);
      header.appendChild(next);

      header.addEventListener("click", function (event) {
        var arrow = event.target.closest("[data-rts-question-nav-direction]");
        if (!arrow || arrow.disabled) return;

        var targetStep = parseInt(arrow.getAttribute("data-rts-target-step"), 10);
        if (targetStep) openStepFromTitle(form, targetStep);
      });
    }

    return header;
  }

  function updateQuestionNavigator(form, questionNumber, totalQuestions) {
    var header = setupQuestionNavigator(form);
    if (!header) return;

    var status = header.querySelector("[data-rts-question-nav-status]");
    if (status) {
      var statusText = "Question " + questionNumber + " of " + totalQuestions;
      if (status.textContent !== statusText) {
        status.textContent = statusText;
      }
    }

    var highestReached = Math.max(
      parseInt(header.getAttribute("data-rts-highest-question"), 10) || 0,
      questionNumber
    );
    if (header.getAttribute("data-rts-highest-question") !== String(highestReached)) {
      header.setAttribute("data-rts-highest-question", String(highestReached));
    }

    var previous = header.querySelector("[data-rts-question-nav-direction='previous']");
    var next = header.querySelector("[data-rts-question-nav-direction='next']");
    var previousTitle = header.querySelector(
      ".ff-step-titles [data-step-number='" + (questionNumber - 2) + "']"
    );
    var nextTitle = header.querySelector(
      ".ff-step-titles [data-step-number='" + questionNumber + "']"
    );

    if (previous) {
      previous.disabled = !canOpenStepTitle(previousTitle);
      previous.setAttribute("data-rts-target-step", String(questionNumber - 1));
    }
    if (next) {
      next.disabled = !canOpenStepTitle(nextTitle);
      next.setAttribute("data-rts-target-step", String(questionNumber + 1));
    }

    var navProgress =
      (totalQuestions > 1 ? ((questionNumber - 1) / (totalQuestions - 1)) * 100 : 100) + "%";
    if (header.style.getPropertyValue("--rts-question-nav-progress") !== navProgress) {
      header.style.setProperty("--rts-question-nav-progress", navProgress);
    }

    header.querySelectorAll(".ff-step-titles > li").forEach(function (title) {
      var titleStep = (parseInt(title.getAttribute("data-step-number"), 10) || 0) + 1;
      var isActive = titleStep === questionNumber;
      var isComplete = !isActive && (
        title.classList.contains("ff_completed") || titleStep <= highestReached
      );
      var available = canOpenStepTitle(title);
      title.classList.toggle("rts-question-nav-is-active", isActive);
      title.classList.toggle("rts-question-nav-is-complete", isComplete);
      title.classList.toggle("rts-question-nav-is-available", available);
      title.setAttribute("aria-label", "Go to question " + titleStep);
      if (!available) {
        title.setAttribute("aria-disabled", "true");
      } else {
        title.removeAttribute("aria-disabled");
      }
    });
  }

  // The review screen uses this public bridge to jump straight to the native
  // Fluent Forms tab instead of walking through every intermediate question.
  window.rtsSurveyGoToStep = openStepFromTitle;

  function isVisible(element) {
    if (!element) return false;
    var style = window.getComputedStyle(element);
    return style.display !== "none" && style.visibility !== "hidden";
  }

  function getActiveQuestion(steps) {
    var activeIndex = steps.findIndex(function (step) {
      return step.classList.contains("active") || step.getAttribute("aria-hidden") === "false";
    });

    if (activeIndex < 0) {
      activeIndex = steps.findIndex(isVisible);
    }

    return activeIndex < 0 ? 1 : activeIndex + 1;
  }

  function getSectionIndex(sections, questionNumber) {
    var index = sections.findIndex(function (section) {
      return questionNumber <= parseInt(section.end, 10);
    });
    return index < 0 ? Math.max(0, sections.length - 1) : index;
  }

  function getPrimaryQuestionGroup(step) {
    if (!step) return null;

    var groups = step.querySelectorAll(".ff-el-group");
    return Array.prototype.find.call(groups, function (group) {
      return group.querySelector("input:not([type='hidden']), select, textarea");
    }) || null;
  }

  function isQuestionAnswered(step) {
    var ratingFields = step.querySelectorAll("input[type='number'][min='1'][max='10']");
    if (ratingFields.length > 1) {
      return Array.prototype.every.call(ratingFields, function (field) {
        var value = parseInt(field.value, 10);
        return value >= 1 && value <= 10;
      });
    }

    var group = getPrimaryQuestionGroup(step);
    if (!group) return false;

    var choices = group.querySelectorAll("input[type='radio'], input[type='checkbox']");
    if (choices.length) {
      return Array.prototype.some.call(choices, function (choice) {
        return choice.checked;
      });
    }

    var field = group.querySelector("select, textarea, input:not([type='hidden'])");
    return !!(field && String(field.value || "").trim());
  }

  function decorateQuestions(steps, questionNumber) {
    var activeStep = steps[questionNumber - 1];
    if (!activeStep) return;

    var groups = activeStep.querySelectorAll(".ff-el-group");

    groups.forEach(function (group) {
      var label = group.querySelector(".ff-el-input--label > label");
      if (!label) return;

      var originalLabel = label.getAttribute("data-rts-original-label") ||
        String(label.textContent || "").trim();
      if (group.querySelector("textarea")) {
        group.classList.toggle("rts-luxury-survey__comment-field", /\bcomments?\b/i.test(originalLabel));
      }
      var match = originalLabel.match(/^\s*(\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*)\s*[.)]\s*(.+)$/);
      var code = match ? match[1] : "";
      var questionText = match ? match[2] : originalLabel;

      if (!code) return;

      if (!label.hasAttribute("data-rts-original-label")) {
        label.setAttribute("data-rts-original-label", originalLabel);
      }
      if (match && String(label.textContent || "").trim() !== questionText) {
        label.textContent = questionText;
      }

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

      var badgeText = badge.querySelector(".rts-luxury-survey__question-number-text");
      if (!badgeText) {
        badge.textContent = "";
        badgeText = document.createElement("b");
        badgeText.className = "rts-luxury-survey__question-number-text";
        badge.appendChild(badgeText);
      }
      if (badgeText.textContent !== code) {
        badgeText.textContent = code;
      }
    });
  }

  function decorateHtmlBlocks(steps) {
    steps.forEach(function (step) {
      // The number-rules script identifies rating matrices from their original
      // Custom HTML question code (for example, "20."). Do not rewrite that
      // HTML before it gets the chance to configure the matrix.
      var hasRatingMatrixInputs = step.querySelectorAll("input[type='number'][min='1'][max='10']").length >= 2;
      if (hasRatingMatrixInputs || step.classList.contains("rts-luxury-survey__rating-matrix")) {
        step.querySelectorAll(".rts-luxury-survey__html-copy").forEach(function (group) {
          group.classList.remove("rts-luxury-survey__html-copy");
        });
        step.querySelectorAll(".rts-luxury-survey__html-heading").forEach(function (group) {
          group.classList.remove("rts-luxury-survey__html-heading");
        });
        return;
      }
      step.querySelectorAll(".ff-el-group").forEach(function (group) {
        // Only HTML-only Fluent Forms blocks are handled here. Form fields keep
        // their normal controls and labels.
        if (group.querySelector("input:not([type='hidden']), select, textarea")) return;

        var content = group.querySelector(".ff-el-input--content") || group;
        var text = String(content.textContent || "").replace(/\s+/g, " ").trim();
        if (!text) return;

        var match = text.match(/^(\d+[A-Za-z]*(?:-[A-Za-z0-9]+)*)\s*[.)]\s*(.+)$/);
        if (match) {
          if (group.classList.contains("rts-luxury-survey__html-heading")) return;
          group.classList.add("rts-luxury-survey__html-heading");
          content.textContent = "";

          var number = document.createElement("span");
          number.className = "rts-luxury-survey__html-question-number";
          number.setAttribute("aria-hidden", "true");
          number.textContent = match[1];

          var title = document.createElement("span");
          title.className = "rts-luxury-survey__html-question-title";
          title.textContent = match[2];
          content.appendChild(number);
          content.appendChild(title);
        } else {
          group.classList.add("rts-luxury-survey__html-copy");
        }
      });
    });
  }

  function updateSurvey(wrapper, form, sections, questionImages) {
    var steps = getSteps(form);
    if (!steps.length || !sections.length) return;

    var questionNumber = getActiveQuestion(steps);
    var totalQuestions = steps.length;
    updateQuestionNavigator(form, questionNumber, totalQuestions);
    var sectionIndex = getSectionIndex(sections, questionNumber);
    var section = sections[sectionIndex];
    var completedQuestions = Math.max(0, questionNumber - 1) +
      (isQuestionAnswered(steps[questionNumber - 1]) ? 1 : 0);
    var percent = Math.round((completedQuestions / totalQuestions) * 100);

    wrapper.style.setProperty("--rts-survey-progress", percent + "%");
    // As in the approved artwork, progress replaces the gold base ring.
    var progressColour = percent >= 100 ? "#34b978" :
      percent >= 75 ? "#1ba8a0" :
      percent >= 50 ? "#168dcc" :
      percent >= 25 ? "#123d6e" : "#0d2d58";
    wrapper.style.setProperty("--rts-survey-progress-colour", progressColour);

    var ring = wrapper.querySelector(".rts-luxury-survey__progress-ring");
    var percentElement = wrapper.querySelector("[data-rts-progress-percent]");
    var countElement = wrapper.querySelector("[data-rts-question-count]");
    var sectionNumber = wrapper.querySelector("[data-rts-section-number]");
    var sectionName = wrapper.querySelector("[data-rts-section-name]");
    var sectionDescription = wrapper.querySelector("[data-rts-section-description]");
    var hero = wrapper.querySelector("[data-rts-section-hero]");

    if (ring) {
      ring.setAttribute("aria-valuenow", String(percent));
      ring.setAttribute("aria-valuetext", completedQuestions + " of " + totalQuestions + " questions completed");
    }
    if (percentElement) percentElement.textContent = percent + "%";
    if (countElement) countElement.textContent = questionNumber + " of " + totalQuestions;
    if (sectionNumber) sectionNumber.textContent = String(sectionIndex + 1);
    if (sectionName) sectionName.textContent = section.name || "";
    if (sectionDescription) sectionDescription.textContent = section.description || "";

    if (hero) {
      var bannerImage = questionImages[questionNumber - 1] || section.image || hero.getAttribute("data-rts-default-image") || "";
      if (bannerImage) {
        hero.style.backgroundImage =
          "linear-gradient(90deg, rgba(2, 17, 29, .20) 0%, rgba(2, 17, 29, .08) 48%, rgba(2, 17, 29, .04) 100%), url(\"" +
          String(bannerImage).replace(/\"/g, "%22") +
          "\")";
      } else {
        hero.style.backgroundImage = "linear-gradient(115deg, rgba(5, 31, 46, .98), rgba(7, 55, 69, .8))";
      }
    }

    wrapper.querySelectorAll("[data-rts-section]").forEach(function (item, index) {
      item.classList.toggle("is-active", index === sectionIndex);
      item.classList.toggle("is-complete", index < sectionIndex);
      if (index === sectionIndex) {
        item.setAttribute("aria-current", "step");
      } else {
        item.removeAttribute("aria-current");
      }
    });

    decorateQuestions(steps, questionNumber);
    decorateHtmlBlocks(steps);
    steps.forEach(function (step) {
      step.querySelectorAll("select").forEach(function (select) {
        var container = select.closest(".ff-el-input--content");
        if (container) {
          container.classList.toggle("rts-select-has-value", String(select.value || "").trim() !== "");
        }
      });
    });
  }

  function initialiseSurvey(wrapper) {
    if (wrapper.getAttribute("data-rts-survey-ready") === "1") return;

    var formId = wrapper.getAttribute("data-form-id");
    var form = wrapper.querySelector("form[data-form_id='" + formId + "']") ||
      wrapper.querySelector(".fluentform form") || wrapper.querySelector("form");

    if (!form) {
      window.setTimeout(function () {
        initialiseSurvey(wrapper);
      }, 250);
      return;
    }

    var sections = parseSections(wrapper);
    if (!sections.length) return;
    var questionImages = parseQuestionImages(wrapper);

    wrapper.setAttribute("data-rts-survey-ready", "1");
    var updateQueued = false;
    var scheduleUpdate = function () {
      if (updateQueued) return;
      updateQueued = true;
      window.requestAnimationFrame(function () {
        updateQueued = false;
        updateSurvey(wrapper, form, sections, questionImages);
      });
    };

    var observer = new MutationObserver(scheduleUpdate);
    observer.observe(form, {
      subtree: true,
      childList: true,
      attributes: true,
      attributeFilter: ["class", "style", "aria-hidden"],
    });

    form.addEventListener("change", scheduleUpdate);
    form.addEventListener("input", scheduleUpdate);
    form.addEventListener("click", function (event) {
      if (event.target.closest(".ff-btn-next, .ff-btn-prev, .ff-btn-submit, .ff-step-titles > li, [data-rts-question-nav-direction]")) {
        window.setTimeout(scheduleUpdate, 50);
        window.setTimeout(scheduleUpdate, 300);
      }
    });

    scheduleUpdate();
  }

  function initialiseAll() {
    document.querySelectorAll("[data-rts-luxury-survey]").forEach(initialiseSurvey);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initialiseAll);
  } else {
    initialiseAll();
  }

  document.addEventListener("fluentform_loaded", initialiseAll);
})();
