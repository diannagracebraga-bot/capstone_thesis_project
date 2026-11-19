document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="email"][data-email-check-url]').forEach(function (input) {
        var form = input.closest('form');
        if (!form) return;

        var status = document.createElement('small');
        status.className = 'email-availability-status';
        status.setAttribute('aria-live', 'polite');
        input.insertAdjacentElement('afterend', status);

        var state = 'unknown';
        var requestId = 0;
        var debounceTimer;
        var latestCheck = Promise.resolve(false);

        function setStatus(message, className) {
            status.textContent = message;
            status.classList.remove('is-checking', 'is-available', 'is-taken', 'is-error');
            if (className) status.classList.add(className);
        }

        function checkAvailability() {
            var email = input.value.trim().toLowerCase();
            var currentRequest = ++requestId;
            if (!email || !input.validity.valid) {
                state = 'unknown';
                setStatus('', '');
                latestCheck = Promise.resolve(false);
                return latestCheck;
            }

            state = 'checking';
            setStatus('Checking email availability…', 'is-checking');
            var body = new URLSearchParams();
            body.set('email', email);
            latestCheck = fetch(input.dataset.emailCheckUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: body.toString(),
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok || !data.checked) throw new Error('Email check unavailable');
                    if (currentRequest !== requestId || email !== input.value.trim().toLowerCase()) return false;
                    state = data.available ? 'available' : 'taken';
                    setStatus(data.available ? 'Email is available.' : 'This email is already in use.', data.available ? 'is-available' : 'is-taken');
                    return data.available;
                });
            }).catch(function () {
                if (currentRequest === requestId) {
                    state = 'error';
                    setStatus('Could not check this email. Please try again.', 'is-error');
                }
                return false;
            });
            return latestCheck;
        }

        input.addEventListener('input', function () {
            state = 'unknown';
            ++requestId;
            setStatus('', '');
            window.clearTimeout(debounceTimer);
            if (input.value.trim()) {
                debounceTimer = window.setTimeout(checkAvailability, 350);
            }
        });
        input.addEventListener('blur', function () {
            if (input.value.trim()) checkAvailability();
        });
        form.addEventListener('submit', function (event) {
            if (state === 'available') return;
            event.preventDefault();
            window.clearTimeout(debounceTimer);
            checkAvailability().then(function (available) {
                if (available && form.reportValidity()) form.requestSubmit();
                else if (state === 'taken') input.focus();
            });
        });
    });
});
