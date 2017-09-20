(function($){
    $.fn.extend({
        focusClass: function(opt){
            var op = $.extend({
                className: 'focus'
            }, opt);

            return this.each(function(){
                var $this = $(this);
                $this.on('focus', function(){
                    $this.addClass(op.className);
                }).on('blur', function(){
                    $this.removeClass(op.className);
                })
            });
        },

        addRequireClass: function(opt){
            var op = $.extend({
                className: 'require'
            }, opt);

            return this.each(function(){
                var $this = $(this);
                var $opt = $this.data();
                if($opt){
                    var reg = $opt.type;
                    if(reg && reg.indexOf('require') >= 0){
                        $this.addClass(op.className);
                    }
                }
            });
        },

        radio: function(opt){
            var op = $.extend({
                className: 'selected'
            }, opt);

            return this.each(function(){
                var $this = $(this);
                if($this.hasClass('ui_lock')){
                    return ;
                }else{
                    $this.addClass('ui_lock');
                }
                if($this.attr('checked')){
                    $this.parent().addClass(op.className);
                }
                $this.parent().off('click').on('click', function(){
                    var _name = $this.attr('name');
                    $('input[name="' + _name + '"]').parent().removeClass(op.className);
                    $this.parent().addClass(op.className);
                })
            });
        },

        checkbox: function(opt){
            var op = $.extend({
                className: 'selected'
            }, opt);

            return this.each(function(){
                var $this = $(this);
                if($this.hasClass('ui_lock')){
                    return ;
                }else{
                    $this.addClass('ui_lock');
                }
                if($this.attr('checked')){
                    $this.parent().addClass(op.className);
                }
                $this.parent().off('click').on('click', function(){
                    if($this.attr('checked')){
                        $this.parent().addClass(op.className);
                    }else{
                        $this.parent().removeClass(op.className);
                    }
                })
            });
        },

        colorInput : function(){
            return this.each(function(){
                var $this = $(this);
                $this.off('keydown').on('keydown',function(){
                    setTimeout(function(){
                        var str = $this.val().trim();
                        var obj = $this.siblings('span');
                        if(str != ""){
                            obj.css('background', "#"+str);
                        }else{
                            obj.css('background', '');
                        }
                    })
                })
            })
        }
    });
})(jQuery)