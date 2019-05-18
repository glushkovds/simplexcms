var JSAlert = function () {

    var instance = this;

    /**
     * 
     * @param string type success|error|warning
     * @param string msg
     * @returns void
     */
    this.push = function (type, msg) {
        var $msg = $('<div class="alert alert-' + type + '">' + msg + '</div>');
        setTimeout(function () {
            $msg.fadeOut(function () {
                $msg.remove();
            });
        }, 5000);
        $('.alert-hub').append($msg);
    };

    this.success = function (msg) {
        instance.push('success', msg);
    };

    this.error = function (msg) {
        instance.push('error', msg);
    };

    var hubWidth = Math.round(($('body').width() - $('#doc').width()) / 2 - 40);

//    $('body').append('<div class="alert-hub" style="width: '+hubWidth+'px">');
    $('body').append('<div class="alert-hub col-md-4">');


}, Alert;

$(function () {
    Alert = new JSAlert();
});
