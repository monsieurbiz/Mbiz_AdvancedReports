document.observe("dom:loaded", function() {
    $$('.js-no-autocomplete').each(function (element) {
        if (typeof(ADVANCED_REPORTS_REQUEST) !== 'undefined') {
            element.value = ADVANCED_REPORTS_REQUEST;
            var event = new Event('change');
            element.dispatchEvent(event);
        } else {
            element.value = "";
        }
    });
});