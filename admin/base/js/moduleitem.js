function onChangeModule(select) {
    var moduleId = select.value;
    var params = {action: 'module_param', module_id: moduleId, item_id: $('#info-key-value').val()};

    if (!$('.ajax-params-left').length) {
        $('.row-content .col-main').append('<div class="ajax-params-left">');
    }

    $.get('./', params, function (r) {
        r = $.parseJSON(r);
        if (r.right && !$('.ajax-params').length) {
            $('.row-content .col-md-12').removeClass('col-md-12').addClass('col-md-8').after('<div class="col-md-4"><div class="ajax-params"></div></div>');
        }

        $('.ajax-params').html(r.right);
        $('.ajax-params-left').html(r.left);
        $('.ajax-params :checkbox, .ajax-params-left :checkbox').uniform();
        
        var $menuId = $('[name="menu_id"]');
        $menuId.data('val',$menuId.val()).html(r.menu_items).val($menuId.data('val'));
    });
}

$(function () {
    if ($('#info-key-value').length) {
        $('[onchange]').trigger('change');
    }
});
