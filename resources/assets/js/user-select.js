$(document).ready(function () {
    let userSelect = $('#user-select')

    let ajaxUrl = userSelect.data('url');
    let selectedUserId = userSelect.data('selected-id');
    let selectedUserText = userSelect.data('selected-text');
    let placeholderText = userSelect.data('placeholder');

    // Initialize Select2
    userSelect.select2({
        allowClear: true,
        ajax: {
            url: ajaxUrl,
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: $.map(response?.data, function (item) {
                        return {
                            text: (item.first_name ?? '') + ' ' + (item.last_name ?? ''),
                            id: item.id
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 2,
    });

    // Set the selected user if one is provided
    if (selectedUserId) {
        let newOption = new Option(selectedUserText, selectedUserId, true, true);
        $('.user-select').append(newOption).trigger('change');
    }
});