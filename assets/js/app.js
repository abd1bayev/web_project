document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm-delete="true"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!window.confirm("Haqiqatan ham ushbu ma'lumotni o'chirmoqchimisiz?")) {
                event.preventDefault();
            }
        });
    });
});