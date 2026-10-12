document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.post-faq-question').forEach(function (button) {
        button.addEventListener('click', function () {
            var item = button.closest('.post-faq-item');
            var shouldOpen = !item.classList.contains('is-open');

            item.closest('.post-faq-list').querySelectorAll('.post-faq-item').forEach(function (faq) {
                faq.classList.remove('is-open');
                faq.querySelector('.post-faq-question').setAttribute('aria-expanded', 'false');
            });

            if (shouldOpen) {
                item.classList.add('is-open');
                button.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
