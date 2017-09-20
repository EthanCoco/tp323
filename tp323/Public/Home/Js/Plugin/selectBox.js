/* * name:selectBox * by :aniu * 选择文字显示隐藏层插件 */
;(function($) {
    var methods = {
        text: undefined,
        init: function(options) {
            return this.each(function() {
                var $this = $(this);
                var $default = {
                    text: "p",
                    items: [{
                        title: "",
                        style: "",
                        onClick: function() {}
                    }]
                };
                var $settings = $.extend({},$default, options);
                var html = "<div class='selectBox_con'><span>选择操作：</span>";
                $this.append(html);
                var $selectBox_con = $('.selectBox_con');
                $.each($settings.items, function(index, it) {
                    html = "<a href='javascript:void(0);' class='box_icon " + it.style + "' title='" + it.title + "' id='" + it.style + "_id'>"+it.title+"</a>";
                    $selectBox_con.append(html);
                    $("#" + it.style + "_id").click(function() {
                        it.onClick()
                    });
                });
                $($settings.text).bind("mouseup", function(e) {
                    var selObj
                    if(document.selection) {
                        selObj = document.selection.createRange();
                        text = document.selection.createRange().text;
                    } else {
                        selObj = document.getSelection();
                        text = document.getSelection().toString();
                    }
                    if(text.length > 0) {
                        var conW = 650;
                        var left = e.pageX + $this.width() > $(document).width() ? e.pageX - $this.width() : e.pageX;
                        $this.css({
                            "left": left - conW,
                            "top": e.pageY - 160,
                            "display": "block"
                        });
                    }
                });
                $(document).bind("mousedown", function() {
                    if ($this.is(":visible")) {
                        $this.fadeOut();
                    }
                });
                return true;
            });
        },
        getSelectText: function() {
            return text;
        },
        destroy: function() {}
    };

    jQuery.fn.selectBox = function() {
        var method = arguments[0];
        if (methods[method]) {
            method = methods[method];
            arguments = Array.prototype.slice.call(arguments, 1);
        } else if (typeof(method) == 'object' || !method) {
            method = methods.init;
        } else {
            alert('插件selectBox没有方法为： \"' + method + '\" 的方法！');
            return this;
        }
        //return method.call(this)
        return method.apply(this, arguments);
    }
})(jQuery)