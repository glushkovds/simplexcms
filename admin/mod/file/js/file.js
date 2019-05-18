var JSSFFile = function () {

    var getModuleId = function () {
        return $('#file-module-id').val();
    };

    this.onSubmit = function (form) {
        
        if ($('[type="file"]', form).get(0).files[0].size > $('#file-max-size').val()) {
            bodyBlockShowExclusive('rows');
            alert('Превышен максимальный размер загружаемого файла: ' + $('#file-max-size').attr('value-user'));
            return false;
        }
        
        var formData = new FormData(form);
        var ajaxPostCallback = function (r) {
            r = r.substr(r.indexOf('{'));
            r = JSON.parse(r);
            if (r.success) {
                $('.body-block-rows').append(r.item_html);
            } else {
                alert(r.error);
            }
            bodyBlockShowExclusive('rows');
        };
        $.ajax({
            url: $(form).attr('action'),
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'post',
            success: ajaxPostCallback,
            error: ajaxPostCallback
        });
        return false;
    };

    this.onAttach = function (input) {
        bodyBlockShowExclusive('loading', false);
        $(input).closest('form').submit();
    };

    this.delete = function (fileId) {
        $.getJSON('./?sf_module_id=' + getModuleId() + '&action=delete&file_id=' + fileId, function (r) {
            if (r.success) {
                $('[file-id="' + fileId + '"]').remove();
                bodyBlockShowExclusive('rows');
            } else {
                alert(r.error);
            }
        });
    };

    var bodyBlockShowExclusive = function (blockName, withButtons) {
        $('.body-block').addClass('hide');

        if ('rows' == blockName && !$('.body-block-rows li').length) {
            blockName = 'empty-files';
        }

        $('.body-block-' + blockName).removeClass('hide');
        if (typeof withButtons == 'undefined') {
            withButtons = true;
        }
        withButtons ? $('.files-buttons').show() : $('.files-buttons').hide();
    };

}, SFFile;

$(function () {
    SFFile = new JSSFFile();
});