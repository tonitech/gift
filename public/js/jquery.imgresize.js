// @charset "utf-8";
/**
 * jquery版本要求：1.3 ~ 1.8，HTML声明请遵循W3C标准
 * 用来修改图片大小并保持长宽比的jQuery插件
 * 兼容IE6浏览器
 * @author wangzhiangtony@qq.com
 * @version 1.1
 * @date 2013-4-11 14:09:55
 */
(function($) {
    $.fn.imgresize = function(opts) {
        var defaults = {
            width : null
        };
        var options = $.extend(defaults, opts);
        
		if(options.width != null) {
            if($.browser.msie) {
                var images = $('img');
                var len = images.length;
                for(var i = 0; i < len; i++) {
                    var image = images[i];
                    var src = $(image).attr('src');
                    $(image).load(function() {
                        iresize(this);
                    });
                    $(image).attr('src', src + '?' + new Date().getTime());
                }
            } else {
                $('img').load(function() {
                    iresize(this);
                });
            }
        }

        function iresize(self) {
            var width = $(self).width();
            var height = $(self).height();
            if(width > options.width) {
                height = options.width * height / width;
                $(self).css('width', options.width);
                $(self).css('height', Math.round(height));
            }
        }

    };
})(jQuery);
