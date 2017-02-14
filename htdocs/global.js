if ('/modules/spamexperts-extension/index.php/index/settings' == window.location.pathname) {
    document.observe('dom:loaded', function () {
        var affectedFields = ['#spampanel_url', '#apihost', '#mx1', '#mx2', '#mx3', '#mx4'];

        if (! $('apiuser').disabled) {
            affectedFields.push('#apiuser');
            affectedFields.push('#apipass');
        }

        $$('#use_config_from_license').each(function (checkbox) {
            Event.on(checkbox, 'click', function () {
                $$(affectedFields.join(',')).each(function (input) {
                    input.disabled = checkbox.checked;
                });
            });
        });
    });
}