document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input').forEach(function (input) {
        var fieldName = ((input.name || '') + ' ' + (input.id || '')).toLowerCase();
        var isContactField = input.type === 'tel'
            || input.type === 'contact'
            || fieldName.indexOf('contact') !== -1
            || fieldName.indexOf('phone') !== -1;

        if (!isContactField || input.readOnly || input.disabled) return;

        input.type = 'tel';
        input.inputMode = 'numeric';
        input.addEventListener('beforeinput', function (event) {
            if (event.data && /[^0-9]/.test(event.data) && event.inputType.indexOf('insert') === 0) {
                event.preventDefault();
            }
        });
        input.addEventListener('input', function () {
            var cursor = input.selectionStart;
            var beforeCursor = input.value.slice(0, cursor === null ? input.value.length : cursor);
            var cleanValue = input.value.replace(/[^0-9]/g, '');
            var cleanCursor = beforeCursor.replace(/[^0-9]/g, '').length;
            if (input.value !== cleanValue) {
                input.value = cleanValue;
                if (input === document.activeElement) input.setSelectionRange(cleanCursor, cleanCursor);
            }
        });
    });
});
