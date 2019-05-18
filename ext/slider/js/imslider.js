jQuery.fn.imslider = function(opt) {

    $(this).each(function() {
        var launchcount = 0;
        if(launchcount ++){
            return;
        }
        var defaults = {
            animation: 'slide',
            speed: 500,
            duration: 8000,
            active: true
        }
        opt = $.extend({}, defaults, opt || {});

        var $slider = $(this);
        var $ch = $('.slide', this);
        if ($ch.length < 1) {
            this.hide();
        } else {
            if ($ch.length < 2) {
                $('.action', this).hide();
                return;
            }

            var slideWidth = $(this).width();

            var css = {
                'slide': {
                    'cur': {zIndex: 4},
                    'next': {left: slideWidth},
                    'prev': {left: -slideWidth}
                },
                'fade': {
                    'cur': {zIndex: 4},
                    'next': {left: 0},
                    'prev': {left: 0}
                }
            }

            var animate = {
                'slide': {
                    'cur-prev': {left: slideWidth},
                    'cur-next': {left: -slideWidth},
                    'next': {left: 0, opacity: 1},
                    'prev': {left: 0, opacity: 1}
                },
                'fade': {
                    'cur-prev': {opacity: 0},
                    'cur-next': {opacity: 0},
                    'next': {opacity: 1},
                    'prev': {opacity: 1}
                }
            }

            $('.slide:first', this).animate({
                opacity: 1
            }).addClass('slide-cur');
            $('.slide:not(:first)', this).css({opacity:1});

            var intervalID;
            
            var slideDo = function($next){
                var $cur = $('.slide-cur', $slider);
                var curIndex = parseInt($cur.attr("id").replace("slide", ""));
                var nextIndex = parseInt($next.attr("id").replace("slide", ""));
                
                if (curIndex == nextIndex) {
                    return false;
                }
                var nextWhat = curIndex < nextIndex ? 'next' : 'prev';
                
                $cur.css(css[opt.animation]['cur']).animate(animate[opt.animation]['cur-'+nextWhat], opt.speed, function() {
                    $(this).removeClass('slide-cur');
                });
                $next.css(css[opt.animation][nextWhat]).animate(animate[opt.animation][nextWhat], opt.speed, function() {
                    $(this).addClass('slide-cur');
                });

                $(".dots a").removeClass("current");
                $("#to" + nextIndex).addClass("current");

                return false;
            }

            var slideNext = function() {
                var $cur = $('.slide-cur', $slider);
                var $next = $cur.next('.slide').length ? $cur.next() : $('.slide:first', $slider);
                return slideDo($next);
            }

            var slidePrev = function() {
                var $cur = $('.slide-cur', $slider);
                var $next = $cur.prev('.slide').length ? $cur.prev() : $('.slide:last', $slider);
                return slideDo($next);
            }

            var slideTo = function(index) {
                var $cur = $('.slide-cur', $slider);
                var curIndex = parseInt($cur.attr("id").replace("slide", ""));
                var $next = $("#slide" + index);
                return slideDo($next);
            }

            var i = 1;
            $(".slide", $slider).each(function() {
                $(this).attr("id", "slide" + i);
                $(".dots", $slider).append('<a id="to' + i + '" href=""></a> ');
                i++;
            })
            $("#to1").addClass("current");

            intervalID = setInterval(function(){
                if(!opt.active){
                    return false;
                }
                slideNext();
            }, opt.duration);

            $('.action.left', this).click(function() {
                slidePrev();
                clearInterval(intervalID);
                intervalID = setInterval(slideNext, opt.duration);
                return false;
            });

            $('.action.right', this).click(function() {
                slideNext();
                clearInterval(intervalID);
                intervalID = setInterval(slideNext, opt.duration);
                return false;
            });

            $('.dots a', this).click(function() {
                slideTo($(this).attr("id").replace("to", ""));
                clearInterval(intervalID);
                intervalID = setInterval(slideNext, opt.duration);
                return false
            });
            
            if($('.controls',$slider).length){
                $('.c-first', this).click(function() {
                    return slideDo($('.slide:first', $slider));
                });
                $('.c-last', this).click(function() {
                    return slideDo($('.slide:last', $slider));
                });
                $('.c-play', this).click(function() {
                    opt.active = true;
                    $('.c-a',$slider).removeClass('active');
                    $(this).addClass('active');
                });
                $('.c-pause', this).click(function() {
                    opt.active = false;
                    $('.c-a',$slider).removeClass('active');
                    $(this).addClass('active');
                });
                $('.c-play', this).addClass('active');
            }

        }
    })
}

$(function() {
    $(".slider").imslider();
})
