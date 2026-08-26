/**
 * Estatein — front-end behaviour.
 *
 * Vanilla ES2017, no dependencies, ~5KB. Every module is progressive: the
 * markup works without JavaScript, and this file upgrades it.
 *
 * Modules
 *   1. Navigation      — mobile panel, focus handling, sticky header
 *   2. Accordion       — FAQ open/close with animated height
 *   3. Carousel        — arrow controls over CSS scroll-snap tracks
 *   4. Reveal          — IntersectionObserver entrance animations
 *   5. Forms           — inline validation + AJAX submit
 */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/** Shorthand helpers. */
	function $(selector, scope) {
		return (scope || doc).querySelector(selector);
	}
	function $$(selector, scope) {
		return Array.prototype.slice.call((scope || doc).querySelectorAll(selector));
	}

	/* =====================================================================
	 * 0. Flag JS availability
	 * ================================================================== */
	doc.body.classList.remove('no-js');
	doc.body.classList.add('has-js');

	/* =====================================================================
	 * 1. NAVIGATION
	 * ================================================================== */
	function initNavigation() {
		var toggle = $('#nav-toggle');
		var nav = $('#primary-nav');
		var header = $('#site-header');

		if (toggle && nav) {
			var setOpen = function (open) {
				toggle.setAttribute('aria-expanded', String(open));
				nav.classList.toggle('is-open', open);
				// Stop the page scrolling behind the open panel.
				doc.body.style.overflow = open ? 'hidden' : '';
			};

			var isOpen = function () {
				return toggle.getAttribute('aria-expanded') === 'true';
			};

			toggle.addEventListener('click', function () {
				setOpen(!isOpen());
			});

			// Escape closes and returns focus to the toggle.
			doc.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && isOpen()) {
					setOpen(false);
					toggle.focus();
				}
			});

			// A click outside the panel closes it.
			doc.addEventListener('click', function (event) {
				if (!isOpen()) {
					return;
				}
				if (!nav.contains(event.target) && !toggle.contains(event.target)) {
					setOpen(false);
				}
			});

			// Following a link closes the panel.
			nav.addEventListener('click', function (event) {
				if (event.target.closest('a') && isOpen()) {
					setOpen(false);
				}
			});

			// Returning to desktop must never leave the page scroll-locked.
			var desktop = window.matchMedia('(min-width: 1025px)');
			var onBreakpoint = function () {
				if (desktop.matches && isOpen()) {
					setOpen(false);
				}
			};
			if (desktop.addEventListener) {
				desktop.addEventListener('change', onBreakpoint);
			} else if (desktop.addListener) {
				desktop.addListener(onBreakpoint);
			}
		}

		// Solidify the header background once the page has scrolled.
		if (header) {
			var onScroll = function () {
				header.classList.toggle('is-stuck', window.scrollY > 8);
			};
			onScroll();
			window.addEventListener('scroll', onScroll, { passive: true });
		}
	}

	/* =====================================================================
	 * 2. ACCORDION
	 * ================================================================== */
	function initAccordion() {
		$$('[data-faq]').forEach(function (group) {
			var items = $$('.faq-item', group);

			items.forEach(function (item) {
				var trigger = $('.faq-item__trigger', item);
				var panel = $('.faq-item__panel', item);

				if (!trigger || !panel) {
					return;
				}

				// Panels start closed; CSS keeps them open when JS is absent.
				panel.style.maxHeight = '0px';

				var setState = function (open) {
					trigger.setAttribute('aria-expanded', String(open));
					item.classList.toggle('is-open', open);
					panel.style.maxHeight = open ? panel.scrollHeight + 'px' : '0px';
				};

				trigger.addEventListener('click', function () {
					var willOpen = trigger.getAttribute('aria-expanded') !== 'true';

					// One panel at a time keeps the section from jumping around.
					items.forEach(function (other) {
						if (other !== item) {
							var otherTrigger = $('.faq-item__trigger', other);
							var otherPanel = $('.faq-item__panel', other);
							if (otherTrigger && otherPanel) {
								otherTrigger.setAttribute('aria-expanded', 'false');
								other.classList.remove('is-open');
								otherPanel.style.maxHeight = '0px';
							}
						}
					});

					setState(willOpen);
				});
			});

			// Open the first item so the section never reads as empty.
			var first = items[0];
			if (first) {
				var firstTrigger = $('.faq-item__trigger', first);
				if (firstTrigger) {
					firstTrigger.click();
				}
			}
		});

		// Re-measure open panels when the viewport reflows the text.
		var resizeTimer;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () {
				$$('.faq-item.is-open .faq-item__panel').forEach(function (panel) {
					panel.style.maxHeight = panel.scrollHeight + 'px';
				});
			}, 150);
		});
	}

	/* =====================================================================
	 * 3. CAROUSEL
	 * ================================================================== */
	function initCarousels() {
		$$('[data-carousel-nav]').forEach(function (nav) {
			var track = doc.getElementById(nav.getAttribute('data-carousel-nav'));

			if (!track) {
				return;
			}

			var prev = $('[data-carousel-prev]', nav);
			var next = $('[data-carousel-next]', nav);
			var counter = $('[data-carousel-current]', nav);
			var slides = Array.prototype.slice.call(track.children);

			var step = function () {
				var first = slides[0];
				if (!first) {
					return track.clientWidth;
				}
				var gap = parseFloat(getComputedStyle(track).columnGap || '0') || 0;
				return first.getBoundingClientRect().width + gap;
			};

			var scrollBy = function (direction) {
				track.scrollBy({
					left: step() * direction,
					behavior: prefersReducedMotion ? 'auto' : 'smooth'
				});
			};

			if (prev) {
				prev.addEventListener('click', function () {
					scrollBy(-1);
				});
			}
			if (next) {
				next.addEventListener('click', function () {
					scrollBy(1);
				});
			}

			// Keep the arrows and counter in sync with manual scrolling/swiping.
			var sync = function () {
				var max = track.scrollWidth - track.clientWidth;
				var atStart = track.scrollLeft <= 2;
				var atEnd = track.scrollLeft >= max - 2;

				if (prev) {
					prev.disabled = atStart;
				}
				if (next) {
					next.disabled = atEnd;
				}

				if (counter) {
					var index = Math.round(track.scrollLeft / step()) + 1;
					index = Math.max(1, Math.min(slides.length, index));
					counter.textContent = String(index).padStart(2, '0');
				}
			};

			var frame;
			track.addEventListener(
				'scroll',
				function () {
					cancelAnimationFrame(frame);
					frame = requestAnimationFrame(sync);
				},
				{ passive: true }
			);

			window.addEventListener('resize', sync);
			sync();
		});
	}

	/* =====================================================================
	 * 4. REVEAL ON SCROLL
	 * ================================================================== */
	function initReveal() {
		var targets = $$('[data-reveal]');

		if (!targets.length) {
			return;
		}

		// Honour the OS setting, and degrade safely on very old browsers.
		if (prefersReducedMotion || !('IntersectionObserver' in window)) {
			targets.forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.05 }
		);

		targets.forEach(function (el) {
			observer.observe(el);
		});
	}

	/* =====================================================================
	 * 5. FORMS
	 * ================================================================== */
	function initForms() {
		var config = window.estateinData || {};
		var messages = config.i18n || {};

		$$('[data-estatein-form]').forEach(function (form) {
			var status = $('[data-form-status]', form);
			var submitButton = form.querySelector('button[type="submit"]');
			var originalLabel = submitButton ? submitButton.innerHTML : '';

			/** Show or clear an inline error under one field. */
			var setFieldError = function (input, message) {
				var field = input.closest('.field') || input.parentElement;
				if (!field) {
					return;
				}

				var existing = $('.field__error', field);

				if (!message) {
					field.classList.remove('has-error');
					input.removeAttribute('aria-invalid');
					if (existing) {
						existing.remove();
					}
					return;
				}

				field.classList.add('has-error');
				input.setAttribute('aria-invalid', 'true');

				if (existing) {
					existing.textContent = message;
					return;
				}

				var note = doc.createElement('p');
				note.className = 'field__error';
				note.textContent = message;
				field.appendChild(note);
			};

			/** Validate one input. Returns true when valid. */
			var validateField = function (input) {
				var value = (input.value || '').trim();

				if (input.hasAttribute('required') && !value && input.type !== 'checkbox') {
					setFieldError(input, messages.required || 'This field is required.');
					return false;
				}

				if (input.type === 'checkbox' && input.hasAttribute('required') && !input.checked) {
					setFieldError(input, messages.required || 'This field is required.');
					return false;
				}

				if (input.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
					setFieldError(input, messages.email || 'Please enter a valid email address.');
					return false;
				}

				setFieldError(input, '');
				return true;
			};

			// Validate on blur, and clear the error as soon as it is fixed.
			$$('input, textarea, select', form).forEach(function (input) {
				if (input.type === 'hidden') {
					return;
				}
				input.addEventListener('blur', function () {
					validateField(input);
				});
				input.addEventListener('input', function () {
					if ((input.closest('.field') || {}).classList && input.closest('.field').classList.contains('has-error')) {
						validateField(input);
					}
				});
			});

			var showStatus = function (message, ok) {
				if (!status) {
					return;
				}
				status.textContent = message;
				status.hidden = false;
				status.className = 'form-note ' + (ok ? 'form-note--ok' : 'form-note--err');
			};

			form.addEventListener('submit', function (event) {
				// Validate first, whether or not we can post over fetch.
				var inputs = $$('input, textarea, select', form).filter(function (input) {
					return input.type !== 'hidden';
				});

				var firstInvalid = null;
				inputs.forEach(function (input) {
					if (!validateField(input) && !firstInvalid) {
						firstInvalid = input;
					}
				});

				if (firstInvalid) {
					event.preventDefault();
					firstInvalid.focus();
					return;
				}

				// Without an AJAX endpoint, let the normal POST through.
				if (!config.ajaxUrl || !window.fetch) {
					return;
				}

				event.preventDefault();

				var data = new FormData(form);
				data.append('action', 'estatein_submit');
				data.append('nonce', config.nonce || '');

				if (submitButton) {
					submitButton.disabled = true;
					submitButton.textContent = messages.sending || 'Sending…';
				}

				fetch(config.ajaxUrl, {
					method: 'POST',
					body: data,
					credentials: 'same-origin'
				})
					.then(function (response) {
						return response.json().catch(function () {
							return { success: false, data: {} };
						});
					})
					.then(function (payload) {
						var body = payload.data || {};

						if (payload.success) {
							showStatus(body.message || 'Thank you.', true);
							form.reset();
							return;
						}

						showStatus(body.message || messages.error || 'Something went wrong.', false);

						// Re-surface server-side field errors inline.
						Object.keys(body.errors || {}).forEach(function (name) {
							var input = form.querySelector('[name="' + name + '"]');
							if (input) {
								setFieldError(input, body.errors[name]);
							}
						});
					})
					.catch(function () {
						showStatus(messages.error || 'Something went wrong.', false);
					})
					.finally(function () {
						if (submitButton) {
							submitButton.disabled = false;
							submitButton.innerHTML = originalLabel;
						}
					});
			});
		});
	}

	/* =====================================================================
	 * Boot
	 * ================================================================== */
	function init() {
		initNavigation();
		initAccordion();
		initCarousels();
		initReveal();
		initForms();
	}

	if (doc.readyState === 'loading') {
		doc.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	/**
	 * Public API.
	 *
	 * `initContent` re-binds only the modules that live inside <main>, for use
	 * after content is swapped in asynchronously (filtered results, load-more).
	 * It deliberately skips the navigation, which is bound once on the header.
	 */
	window.Estatein = {
		init: init,
		initContent: function () {
			initAccordion();
			initCarousels();
			initReveal();
			initForms();
		}
	};

	// Expose the reduced-motion state for any inline scripts that need it.
	root.dataset.motion = prefersReducedMotion ? 'reduced' : 'full';
})();
