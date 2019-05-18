var JSSFUser = function () {

    var info = {};
    var privs = {};


    this.ican = function (priv) {
        return !!privs[priv];
    };


    if ($('#sfuser-info').length) {
        info = $.parseJSON($('#sfuser-info').val().split("'").join('"'));
    }
    if ($('#sfuser-privs').length) {
        privs = $.parseJSON($('#sfuser-privs').val().split("'").join('"'));
    }


}, SFUser;

$(function () {
    SFUser = new JSSFUser();
});
