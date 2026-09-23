/**
 * Cart UX: catalog qty steppers + cart page empty-cart confirm.
 */
document.addEventListener('DOMContentLoaded', function () {
    initQtySteppers();
    initCartEmptyConfirm();
});

function initQtySteppers() {
    document.querySelectorAll('.quick-add-form').forEach(function (form) {
        var maxQty = parseInt(form.getAttribute('data-max-qty'), 10) || 1;
        var input = form.querySelector('.qty-input');
        if (!input) {
            return;
        }

        form.querySelectorAll('.qty-step').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var step = parseInt(btn.getAttribute('data-step'), 10);
                var value = parseInt(input.value, 10) || 1;
                value = Math.min(maxQty, Math.max(1, value + step));
                input.value = String(value);
            });
        });

        input.addEventListener('change', function () {
            var value = parseInt(input.value, 10) || 1;
            input.value = String(Math.min(maxQty, Math.max(1, value)));
        });
    });
}

function initCartEmptyConfirm() {
    var cartForm = document.getElementById('cart-update-form');
    if (!cartForm) {
        return;
    }

    cartForm.addEventListener('submit', function (e) {
        var inputs = cartForm.querySelectorAll('input[name^="qty"]');
        var allZero = true;
        inputs.forEach(function (input) {
            if (parseInt(input.value, 10) > 0) {
                allZero = false;
            }
        });
        if (allZero && !window.confirm('Remove all items from your cart?')) {
            e.preventDefault();
        }
    });
}
