function onChangeField(select) {
    var fieldId = select.value;
    var fieldName = $(select).attr('name');
    var params = {action: 'field_param', field_id: fieldId, table: $('#info-table').val(), key_value: $('#info-key-value').val()};
    params.key_name = $('#info-key-name').val();
    params.field_name = fieldName;
    
    if(!$('.ajax-params').length){
        $('.row-content .col-md-12').removeClass('col-md-12').addClass('col-md-8').after('<div class="col-md-4"><div class="ajax-params"></div></div>');
    }
    
    $.get('./', params, function (r) {
        var $portlet = $('.field-params-' + fieldName);
        if ($portlet.length) {
            $portlet.replaceWith(r);
        }
        else {
            $('.ajax-params').append(r);
        }
        $(':checkbox', $('.field-params-' + fieldName)).uniform();
    });
}

$(function () {
    if ($('#info-key-value').length) {
        $('[onchange]').trigger('change');
    }
});
