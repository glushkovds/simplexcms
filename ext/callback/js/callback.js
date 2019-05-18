var JSModCallback = function () {

    this.toggle = function (a) {
        var $form = $(".callback-form", $(a).closest('.module-callback'));
        if ($form.is(':visible')) {
            $form.hide();
        }
        else {
            $form.popupblock();
        }
    };

    $(".callback-switcher a").click(function () {

    });

    $('.module-callback form').submit(function () {
        var $form = $(this);
        $.post($(this).attr('action'), $(this).serializeArray(), function (r) {
            r = $.parseJSON(r);
            if (r.success) {
//                toastr.success('Ваша заявка на обратный звонок принята! В ближайшее время с Вами свяжется наш менеджер', 'Обраный звонок');
                $form.html('<p class="success">Ваша заявка на обратный звонок принята! В ближайшее время с Вами свяжется наш менеджер</p>');
            }
            else {
                var error = r.error ? r.error : 'На сервере произошла ошибка, заявка не принята';
                $('.error', $form).html(error).show();
//                toastr.error(error, 'Обратный звонок');
            }
        });
        return false;
    });

    $('.module-callback .edit').focus(function () {
        var $form = $(this).closest('form');
        $('.error', $form).hide();
    });

    $(".module-callback .phoneinput").mask("+7-(999)-999-9999");

}, ModCallback;

$(document).ready(function () {

    ModCallback = new JSModCallback();

});