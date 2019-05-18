var JSSlider = function () {

    var params = {
        nav: true,
        navText: false,
        dots: false,
        items: 1,
        slideBy: 1,
        smartSpeed: 900
    };

    if ($(".module-slider .module-body").length > 1) {
        params.loop = true;
        params.dots = true;
    }

    $(".module-slider .module-body").owlCarousel(params);

}, Slider;

$(function () {
    Slider = new JSSlider();
});
