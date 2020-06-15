$(document).ready(function () {
    $('#profileForm').submit(function (e) {
        e.preventDefault();
        $('.form-control').removeClass('input-error');
        $('.error-msg').remove();

        let successMsg = $('#successMsg'),
            errorMsg = $('#errorMsg');

        successMsg.addClass('hidden');
        errorMsg.addClass('hidden');

        validateMainFields();

        if ($(this).find('.form-control.input-error').length === 0) {
            // Валидация прошла
            $.ajax({
                url: '/profile-save',
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status === 'ok') {
                        successMsg.removeClass('hidden');
                    } else {
                        errorMsg.html(res.errorMsg);
                        errorMsg.removeClass('hidden');
                    }
                },
                error: function (res) {
                    errorMsg.html('При сохранении данных возникла ошибка :(');
                    errorMsg.removeClass('hidden');
                },
            });
        }
    });
});