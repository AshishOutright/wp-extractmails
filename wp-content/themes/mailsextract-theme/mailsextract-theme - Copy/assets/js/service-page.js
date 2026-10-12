document.addEventListener('DOMContentLoaded', function () {
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) entry.target.classList.add('active');
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.reveal-up').forEach(function (element) {
        revealObserver.observe(element);
    });

    document.querySelectorAll('.mails-faq-question').forEach(function (button) {
        button.addEventListener('click', function () {
            var item = button.closest('.mails-faq-item');
            var shouldOpen = !item.classList.contains('mails-active');
            document.querySelectorAll('.mails-faq-item').forEach(function (faq) {
                faq.classList.remove('mails-active');
                faq.querySelector('.mails-faq-question').setAttribute('aria-expanded', 'false');
            });
            if (shouldOpen) {
                item.classList.add('mails-active');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.querySelectorAll('.email-intelligence__row').forEach(function (row) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.25 });
        observer.observe(row);
    });

    document.querySelectorAll('.why-businesses-carousel').forEach(function (slider) {
        var services = JSON.parse(slider.dataset.services || '[]');
        var slides = Array.from(slider.querySelectorAll('.parser-slide'));
        var title = slider.querySelector('.parser-slide-title');
        var description = slider.querySelector('.parser-slide-description');
        var previous = slider.querySelector('.parser-slider-prev');
        var next = slider.querySelector('.parser-slider-next');
        var positions = [
            { left: -8, top: 0 }, { left: 8, top: 25 },
            { left: 27, top: 48 }, { left: 50, top: 56 },
            { left: 73, top: 48 }, { left: 92, top: 25 },
            { left: 108, top: 0 }
        ];
        var visibleRange = 3;
        var activeIndex = Math.min(2, slides.length - 1);
        var previousOffsets = new Map();
        var autoplayTimer = null;

        if (!slides.length) return;

        function stopAutoplay() {
            if (autoplayTimer) window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
        function startAutoplay() {
            stopAutoplay();
            if (document.hidden || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            autoplayTimer = window.setInterval(function () {
                activeIndex = (activeIndex + 1) % slides.length;
                render();
            }, 4000);
        }
        function getCircularOffset(index) {
            var offset = (index - activeIndex + slides.length) % slides.length;
            if (offset > Math.floor(slides.length / 2)) offset -= slides.length;
            return offset;
        }
        function render() {
            var isMobile = window.matchMedia('(max-width: 600px)').matches;
            slides.forEach(function (slide, index) {
                var offset = getCircularOffset(index);
                var oldOffset = previousOffsets.get(index);
                var isVisible = Math.abs(offset) <= visibleRange;
                var isWrapping = oldOffset !== undefined && Math.abs(oldOffset - offset) > 1;
                if (!isVisible) {
                    slide.style.transition = 'none'; slide.style.visibility = 'hidden';
                    slide.style.opacity = '0'; slide.style.pointerEvents = 'none';
                    slide.style.zIndex = '0'; slide.setAttribute('aria-hidden', 'true');
                    previousOffsets.set(index, offset); return;
                }
                var position = positions[offset + visibleRange];
                if (isWrapping) slide.style.transition = 'none'; else slide.style.transition = '';
                slide.style.visibility = 'visible';
                slide.style.left = position.left + '%';
                slide.style.top = (isMobile ? position.top + 6 : position.top) + '%';
                slide.style.transform = 'translate(-50%, -50%) scale(' + (offset === 0 ? '1.35' : '1') + ')';
                slide.style.opacity = isMobile && Math.abs(offset) === 2 ? '0.45' : '1';
                slide.style.pointerEvents = 'auto';
                slide.style.zIndex = offset === 0 ? '10' : String(5 - Math.abs(offset));
                slide.setAttribute('aria-current', String(offset === 0));
                slide.setAttribute('aria-hidden', 'false');
                previousOffsets.set(index, offset);
                if (isWrapping) requestAnimationFrame(function () { requestAnimationFrame(function () { slide.style.transition = ''; }); });
            });
            if (services[activeIndex]) {
                title.textContent = services[activeIndex].title;
                description.textContent = services[activeIndex].description;
            }
        }
        slides.forEach(function (slide, index) {
            slide.addEventListener('click', function () { activeIndex = index; render(); startAutoplay(); });
        });
        previous.addEventListener('click', function () { activeIndex = (activeIndex - 1 + slides.length) % slides.length; render(); startAutoplay(); });
        next.addEventListener('click', function () { activeIndex = (activeIndex + 1) % slides.length; render(); startAutoplay(); });
        window.addEventListener('resize', render);
        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);
        slider.addEventListener('focusin', stopAutoplay);
        slider.addEventListener('focusout', function (event) { if (!slider.contains(event.relatedTarget)) startAutoplay(); });
        document.addEventListener('visibilitychange', function () { if (document.hidden) stopAutoplay(); else startAutoplay(); });
        render();
        startAutoplay();
    });

    document.querySelectorAll('.mx-stat-value[data-count]').forEach(function (counter) {
        var target = Number(counter.dataset.count);
        var suffix = counter.dataset.suffix || '';

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            counter.textContent = target + suffix;
            return;
        }

        var startTime = null;
        function updateCounter(time) {
            startTime = startTime || time;
            var progress = Math.min((time - startTime) / 1500, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.floor(target * eased) + suffix;
            if (progress < 1) {
                window.requestAnimationFrame(updateCounter);
            }
        }
        window.requestAnimationFrame(updateCounter);
    });

    document.querySelectorAll('.mx-faq-question').forEach(function (button) {
        button.addEventListener('click', function () {
            var selected = button.closest('.mx-faq-item');
            var shouldOpen = !selected.classList.contains('open');

            document.querySelectorAll('.mx-faq-item').forEach(function (item) {
                item.classList.remove('open');
                item.querySelector('.mx-faq-question').setAttribute('aria-expanded', 'false');
            });

            if (shouldOpen) {
                selected.classList.add('open');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
