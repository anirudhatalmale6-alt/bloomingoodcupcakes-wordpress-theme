/*
 * Bloomin' Good Cupcakes -- the small amount of behaviour this page needs.
 *
 * Everything here is an enhancement. With JavaScript switched off the page
 * still reads, the links still work and the form still submits and validates,
 * because the validation that matters happens on the server.
 */
(function () {
  'use strict';

  /* Smooth scrolling for the in-page links, but only for people who have not
     asked their machine to stop moving things about. */
  var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!calm) {
    document.addEventListener('click', function (e) {
      var a = e.target.closest && e.target.closest('a[href^="#"]');
      if (!a) { return; }
      var id = a.getAttribute('href').slice(1);
      if (!id) { return; }
      var target = document.getElementById(id);
      if (!target) { return; }
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      /* Move focus as well as the viewport, or a keyboard user's next Tab
         carries on from wherever they were before the jump. */
      target.setAttribute('tabindex', '-1');
      target.focus({ preventScroll: true });
      history.replaceState(null, '', '#' + id);
    });
  }

  /* If the page came back with errors, put the person where the problem is. */
  var bad = document.querySelector('.field.bad input, .field.bad textarea');
  if (bad) {
    bad.focus({ preventScroll: true });
    bad.scrollIntoView({ behavior: calm ? 'auto' : 'smooth', block: 'center' });
  }

  /* One press per enquiry. Re-enabled on pageshow so that going Back to a
     cached copy of the page does not leave a permanently dead button. */
  var form = document.querySelector('form[method="post"]');
  if (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (!btn) { return; }
      window.setTimeout(function () {
        btn.disabled = true;
        btn.textContent = 'SENDING…';
      }, 0);
    });
    window.addEventListener('pageshow', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn) { btn.disabled = false; btn.textContent = 'SEND ENQUIRY'; }
    });
  }
})();
