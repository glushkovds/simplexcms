$(document).ready(function () {

    $.fn.formLayout = function (layout) {
        if ($(this).attr('layout') == layout) {
            return;
        }
        if ('wide' == layout) {
            $(this).addClass('form-horizontal');
            $('.form-group', this).each(function () {
                $('.control-label', this).addClass('col-md-3').next().addClass('col-md-9');
            });
        }
        if ('slim' == layout) {
            $(this).removeClass('form-horizontal');
            $('.form-group', this).each(function () {
                $('.col-md-3, .col-md-9', this).removeClass('col-md-3 col-md-9');
            });
        }
        $(this).attr('layout', layout);
    };

    window.onresize = function () {
        $('#table-data-body').height(document.documentElement.clientHeight - 360 - ($('#admin-filter').length ? 37 : 0));
        $('.row-content').css({'max-height': document.documentElement.clientHeight - 260 - ($('#admin-filter').length ? 37 : 0)});
        if ($(window).width() <= 1370) {
            $('.form-responsitive').formLayout('slim');
        }
        else {
            $('.form-responsitive').formLayout('wide');
        }
    };
    window.onresize();

    var hideAside = false;
    var cookieSidebarClosed = Cookie.get('sidebar-closed');
    if (cookieSidebarClosed.length > 0) {
        hideAside = cookieSidebarClosed == '1';
    }
    else if ($(window).width() <= 1370) {
        hideAside = true;
    }
    if (hideAside) {
        $('body').addClass("page-sidebar-closed");
    }

    $('a.sff-bool').click(function () {
        var $td = $(this).parents('td:first');
        var arr = $td.attr('id').split('-');
        if (arr.length == 3) {
            $td.find('a').load('?ajax=1&action=boolChange&field=' + arr[1] + '&pk=' + arr[2]);
        }
    });

    $('a.sff-image').fancybox();

    $(document).keydown(function (e) {
        if (e.ctrlKey) {
            $('#table-data-body').addClass('clear-selection ctrl-pressed');
        }
        if (e.shiftKey) {
            $('#table-data-body').addClass('clear-selection shift-pressed');
        }
    });
    $(document).keyup(function (e) {
        if (!e.ctrlKey) {
            $('#table-data-body').removeClass('clear-selection ctrl-pressed');
        }
        if (!e.shiftKey) {
            $('#table-data-body').removeClass('clear-selection shift-pressed');
        }
    });

    var $shiftFirst = false;
    $('#table-data-body tr').click(function () {
        var $tr = $(this);
        if ($('#table-data-body').hasClass('ctrl-pressed')) {
            $('.model-row-check', $tr).trigger('click');
        }
        else if ($('#table-data-body').hasClass('shift-pressed')) {
            if ($shiftFirst) {
                var start = Math.min($shiftFirst.index(), $tr.index());
                var end = Math.max($shiftFirst.index(), $tr.index());
                var checkAll = $(':checked', $shiftFirst).length > 0;
                $('#table-data-body tr').each(function () {
                    var check = $(this).index() >= start && $(this).index() <= end && checkAll;
                    modelCheck($('input', this).get(0), check);
                });
            }
            else {
                $shiftFirst = $tr;
                modelCheck($('input', $tr).get(0), true);
            }
            window.getSelection().removeAllRanges();
            document.getSelection().removeAllRanges();
        }
        else {
            $shiftFirst = false;
        }
    });

    // init form
    if ($('#info-table').length) {
        $('.form-control').focus(function () {
            var $formGroup = $(this).closest('.form-group');
            $formGroup.removeClass('has-error');
            $('.help-errors', $formGroup).text('').hide();
        });
    }

    if ($('#group-ids').length && $('#group-ids').val() != '') {
        $('input:not(.group-set), select, textarea', $('.form-group')).bind('keyup change', function () {
            $('.group-set', $(this).closest('.form-group')).attr('checked', true).uniform();
        });
        if (tinymce) {
            setTimeout(function () {
                for (var i = 0; i < tinymce.editors.length; i++) {
                    tinymce.editors[i].onKeyUp.add(function (ed, e) {
                        $(ed.getElement()).trigger('change');
                    });
                }
            }, 1000);
        }
    }

    // init show
    if ($('.table-data-head').length) {
        $('.filter [type="text"]').keypress(function (e) {
            if (e.which == 13) {
                $(this).closest('form').submit();
            }
        });

        $.fn.filterExtraApply = function () {
            $('#model-show-form').append($(this).clone(true).css({position: 'absolute', opacity: 0}).val($(this).val())).submit();
        };

        $('.btn-group :radio, select', '.filter-extra-field').each(function () {
            $(this).removeAttr('onchange').change($.fn.filterExtraApply);
        });
        $('.filter-extra [type="text"]').keypress(function (e) {
            if (e.which == 13) {
                $(this).filterExtraApply();
            }
        });
        
        $('.table-data-head').css('padding-right', $('<div style="overflow-y:scroll; float: left; opacity: 0; left: -30000px" />').appendTo('body').width());
        if ($('.row-actions:first a').length > 1) {
            var setWidth = 14 + 31 * $('.row-actions:first a').length;
            if ($('.col-row-actions').width() < setWidth) {
                $('.col-row-actions').css('width', setWidth);
            }
        }

        var saveId = parseInt($('#save-id').val());
        if (saveId) {
            var $row = $('#row-' + saveId);
            if ($row.length) {
                $row[0].scrollIntoView(true);
            }
        }

    }

    jQuery.fn.forceNumericOnly = function () {
        return this.each(function () {
            if (!$(this).data('numeric_inited')) {
                $(this).keydown(function (e) {
                    var key = e.charCode || e.keyCode || 0;
                    var isMinus = key == 173 || key == 109 || key == 189 || e.key == '-';
                    if (isMinus) {
                        if (this.value.indexOf('-') > -1) {
                            return false;
                        }
                        if (this.selectionStart != 0) {
                            return false;
                        }
                    }

                    // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
                    return (
                            key == 8 ||
                            key == 9 ||
                            key == 13 ||
                            key == 35 || key == 36 || // Home End
                            key == 46 ||
                            key == 116 || // F5
                            isMinus ||
                            (key >= 37 && key <= 40) ||
                            (key >= 48 && key <= 57) ||
                            (key >= 96 && key <= 105));
                });
                $(this).data('numeric_inited', 1);
            }
        });
    };
    $(".numeric, .spinner input").forceNumericOnly();

});

function modelCheckAll(ob) {
    ob.checked ? $('#table-data-body tr').addClass('checked') : $('#table-data-body tr').removeClass('checked');
    $('input.model-row-check').attr('checked', ob.checked);
    $('input.model-row-check:checked').length ? $('.action-with-select').removeAttr('disabled') : $('.action-with-select').attr('disabled', 'disabled');
    $('input.model-row-check').uniform();
}
function modelCheck(ob, force) {
    if (!isNaN(force)) {
        ob.checked = force;
        $(ob).uniform();
    }
    ob.checked ? $(ob).parents('tr:first').addClass('checked') : $(ob).parents('tr:first').removeClass('checked');
    $('#model-check-all').attr('checked', $('input.model-row-check:not(:checked)').length ? false : true);
    $('input.model-row-check:checked').length ? $('.action-with-select').removeAttr('disabled') : $('.action-with-select').attr('disabled', 'disabled');
    $('#model-check-all').uniform();
}

function deleteRowsForce() {
    var data = {'rows': []};
    $('input.model-row-check:checked').each(function () {
        data['rows'][data['rows'].length] = this.value;
    });
    window.location.href = './?ajax=1&action=delete&' + http_build_query(data);
}

function editRows() {
    var data = [];
    $('input.model-row-check:checked').each(function () {
        data.push(this.value);
    });
    if (!data.length) {
        return;
    }
    location.href = '?action=form&ids=' + data.join(',');
}

function copyRows() {
    var data = [];
    $('input.model-row-check:checked').each(function () {
        data.push(this.value);
    });
    if (!data.length) {
        return;
    }
    location.href = '?action=copy&ids=' + data.join(',');
}

function modelFormCheck(btn) {

    if ($('#group-ids').length && $('#group-ids').val() != '') {
        return true;
    }

    var d = {};
    $(btn).parents('form:first').find('input, select, textarea').each(function () {
        d[this.name] = this.value;
    });
    $.post('?ajax=1&action=validate', d, function (data) {
        if (data.valid) {
            $('#sf-form-submit').attr('name', btn.name);
            $(btn).parents('form:first').submit();
        } else {
            $('.help-errors').hide();
            $('.form-group').removeClass('has-error');
            $.each(data.errors, function (key, val) {
                $('#help-error-' + key).html(val).show()
                        .closest('.form-group').addClass('has-error');
            });
            $('.row-content').animate({scrollTop: $('.has-error:first').position().top - 15}, 500);
        }

    }, 'JSON');
    return false;
}

function componentChange(val) {
    alert('?ajax=1&action=component&id=' + val);
    return;
    $.post('?ajax=1&action=component&id=' + val, d, function (data) {
        if (data.valid) {
            $(btn).parents('form:first').submit();
        } else {
            $('.sf-form-field-errors').hide();
            $.each(data.errors, function (key, val) {
                $('#form-field-' + key).html(val).show();
            });
        }

    }, 'JSON');
}


function moduleUpdate(ob) {
    var a = location.href.split('/');
    var link = a[a.length - 1] + '&load_module_id=' + $(ob).val();
    $('#form-right-col').load(link + ' #form-right-col>div');
    $('#params-left').load(link + ' #params-left>table');
}

function resetFilter() {
    $('input[type="text"], select, :radio', $('#model-show-form .filter, .filter-extra')).val('');
    $('input[type="text"], select, :radio', $('.filter-extra')).each(function () {
        $('#model-show-form').append($(this).clone(true).css({position: 'absolute', opacity: 0}).val($(this).val()));
    });
    $('#model-show-form').submit();
}

function deleteField(a) {
    var $formGroup = $(a).closest('.form-group');
    var fieldLabel = $('.control-label', $formGroup).text().replace('*', '').trim();
    var fieldName = $('input[name]', $formGroup).attr('name');
    if (!confirm('Удалить ' + fieldLabel + '?')) {
        return;
    }
    var params = {action: 'delete_field', table: $('#info-table').val(), key_value: $('#info-key-value').val()};
    params.key_name = $('#info-key-name').val();
    params.field_name = fieldName;
    $.get('./', params, function (r) {
        r = $.parseJSON(r);
        if (r.success) {
            if ($('.fileinput', $formGroup).length) {
                $('.form-control-static', $formGroup).remove();
            }
            if ($('.thumb-noimage').length) {
                $('img', $formGroup).attr('src', $('.thumb-noimage').eq(0).val());
            }
        }
        else {
            alert('Ошибка при удалении');
        }
    });
}

function openModal(href, callback) {
    $('#modal-ajax').modal();
    $('#modal-ajax .modal-content').load(href, callback ? callback : null);
}

function openModalHTML(html) {
    $('#modal-ajax').modal();
    $('#modal-ajax .modal-content').html(html);
}

function http_build_query(formdata, numeric_prefix, arg_separator) {
    //  discuss at: http://phpjs.org/functions/http_build_query/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Legaev Andrey
    // improved by: Michael White (http://getsprink.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Brett Zamir (http://brett-zamir.me)
    //  revised by: stag019
    //    input by: Dreamer
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
    //        note: If the value is null, key and value are skipped in the http_build_query of PHP while in phpjs they are not.
    //  depends on: urlencode
    //   example 1: http_build_query({foo: 'bar', php: 'hypertext processor', baz: 'boom', cow: 'milk'}, '', '&amp;');
    //   returns 1: 'foo=bar&amp;php=hypertext+processor&amp;baz=boom&amp;cow=milk'
    //   example 2: http_build_query({'php': 'hypertext processor', 0: 'foo', 1: 'bar', 2: 'baz', 3: 'boom', 'cow': 'milk'}, 'myvar_');
    //   returns 2: 'myvar_0=foo&myvar_1=bar&myvar_2=baz&myvar_3=boom&php=hypertext+processor&cow=milk'

    var value, key, tmp = [],
            that = this;

    var _http_build_query_helper = function (key, val, arg_separator) {
        var k, tmp = [];
        if (val === true) {
            val = '1';
        } else if (val === false) {
            val = '0';
        }
        if (val != null) {
            if (typeof val === 'object') {
                for (k in val) {
                    if (val[k] != null) {
                        tmp.push(_http_build_query_helper(key + '[' + k + ']', val[k], arg_separator));
                    }
                }
                return tmp.join(arg_separator);
            } else if (typeof val !== 'function') {
                return encodeURIComponent(key) + '=' + encodeURIComponent(val);
            } else {
                throw new Error('There was an error processing for http_build_query().');
            }
        } else {
            return '';
        }
    };

    if (!arg_separator) {
        arg_separator = '&';
    }
    for (key in formdata) {
        value = formdata[key];
        if (numeric_prefix && !isNaN(key)) {
            key = String(numeric_prefix) + key;
        }
        var query = _http_build_query_helper(key, value, arg_separator);
        if (query !== '') {
            tmp.push(query);
        }
    }

    return tmp.join(arg_separator);
}
