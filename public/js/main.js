$(document).ready(function () {

    let orgs = [];
    // Получаем список организаций из БД
    $.ajax({
        url: '/get-organizations',
        type: 'POST',
        success: function (res) {
            if (res.status === 'ok') {
                orgs = res.orgs;
            }
        },
        error: function (res) {
            console.log('Что-то пошло не так.');
        },
        async: false
    });

    let orgInput = $('#inputIdOrg');
    // Настройки автозаполнения поля "Организация"
    let options = {
        data: orgs,
        getValue: "name",
        list: {
            match: {
                enabled: true
            },
            onSelectItemEvent: function() {
                let value = orgInput.getSelectedItemData().id;
                $("#idOrg").val(value).trigger('change');
            }
        },
    };
    orgInput.easyAutocomplete(options);

    $('#signupForm').on('submit', function(e) {
        $('.form-control').removeClass('input-error');
        $('.error-msg').remove();

        validateMainFields();
        validatePassword();

        return $('.form-control.input-error').length === 0;
    });
});

function validateMainFields() {
    let simpleString = new RegExp('^[а-яА-Яa-zA-Z]{2,}$'),
        simplePhone = new RegExp('^[0-9]{6,15}$');

    let inputFirstName = $('#inputFirstName'),
        inputLastName = $('#inputLastName'),
        inputPhone = $('#inputPhone'),
        inputIdUser = $('#inputIdUser'),
        inputIdOrg = $('#inputIdOrg');

    if (!simpleString.test(inputFirstName.val())) {
        showErrorMsg(inputFirstName);
    }
    if (!simpleString.test(inputLastName.val())) {
        showErrorMsg(inputLastName);
    }
    if (!simplePhone.test(inputPhone.val())) {
        showErrorMsg(inputPhone);
    }
    if (inputIdUser.val() === "0") {
        showErrorMsg(inputIdUser, 'Выберите того, кто вас пригласил');
    }
    if (!inputIdOrg.val()) {
        showErrorMsg(inputIdOrg);
    }
}

function validatePassword() {
    let password = new RegExp('^(?=.*[A-Za-z])(?=.*\\d)[A-Za-z\\d]{8,}$');

    let inputPassword = $('#inputPassword'),
        inputPassword2 = $('#inputPassword2');

    if (!password.test(inputPassword.val())) {
        showErrorMsg(inputPassword, 'Пароль должен состоять как минимум из 8 символов, среди которых хотя бы 1 буква и 1 цифра');
    }
    if (inputPassword.val() !== inputPassword2.val()) {
        inputPassword.addClass('input-error');
        showErrorMsg(inputPassword2, 'Пароли не совпадают!');
    }
}

function showErrorMsg(element, text = null) {
    element.addClass('input-error');
    element.parent().after('<div class="error-msg">' + (text ? text : 'Заполните поле!') + '</div>');
}