//@charset "utf-8";
/**
 * jquery版本要求：1.3 ~ 1.8，HTML声明请遵循W3C标准
 * 用来处理弹出框的jQuery插件
 * 兼容IE6浏览器，从此您再也不用担心IE6的弹框
 * 不用写css hack，否则会出问题，现代浏览器怎么写就怎么写
 * @author wangzhiangtony@qq.com
 * @version 1.2
 * @date 2013-3-27 10:09:59
 */
(function($) {
    //弹出的窗口处于屏幕中间
    $.fn.popup = function(opts) {
        var defaults = {
            filter : ''
        };
        var options = $.extend(defaults, opts);

        if(defaults.filter != '') {
            var maskHeight = $('body').height();
            $(options.filter).css('height', maskHeight);
            $(options.filter).show();
        }
        var str = $(this);

        var pos = str.css('position');
        if(pos == 'fixed') {
            var top = ($(window).height() - str.height()) / 2;
            //fix兼容ie6
            if($.browser.msie && $.browser.version == "6.0") {
                str.css('position', 'absolute');
                var dom = str[0];
                $('body').css({
                    'background-image' : 'url(about:blank)',
                    'background-attachment' : 'fixed'
                });
                dom.style.setExpression('top', 'eval((document.documentElement.scrollTop)+' + top + ')');
            }
        } else {
            var top = $(window).scrollTop() + ($(window).height() - str.height()) / 2;
        }
        if(top > 0) {
            str.css('top', top);
        } else {
            str.css('top', 0);
        }

        var width = $(window).width();
        var left = (width - str.width()) / 2;
        if(left > 0) {
            str.css('left', left);
        } else {
            str.css('left', 0);
        }
        str.css('z-index', '1000');
        str.show();
    };

    $.fn.pophide = function(opts) {
        var defaults = {
            filter : ''
        };
        var options = $.extend(defaults, opts);

        if(defaults.filter != '') {
            $(options.filter).hide();
        }
        $(this).hide();
    };
})(jQuery);
