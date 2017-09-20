(function($){
    $.fn.combox = function(opt){
        var op = $.extend({}, {
            'className' : 'ui_combox'
        }, opt);
        return this.each(function(){
            var $this = $(this);
            var thisval = $this.find('option:selected').val();

            if($this.hasClass("ui_lock")){
                return ;
            }else{
                $this.addClass("ui_lock");
            }

            $item = $this.wrap("<div class='"+op.className+"'></div>");
            var _valuetxt = '';
            var _str = "<ul class='ui_combox_list'>";

            $this.find("option,optgroup").each(function(i, item){
                var _thisVal = $(item).val();
                var _obj = $(item).context;

                if(thisval == _thisVal && _obj.localName != 'optgroup'){
                    _valuetxt = $(item).text();
                }

                var _class = (thisval == _thisVal) ? "ui_combox_option selected" : "ui_combox_option";

                if(_obj.localName == 'optgroup'){
                    _str+= "<li><span class='disable'>"+_obj.label+"</span></li>";
                }else{
                    _str+= "<li data-value='"+$(item).val()+"'><a class='"+_class+"' href='#'>"+$(item).text()+"</a></li>";
                }
            });
            _str += "</ul>";
            _str = "<span class='ui_combox_txt'>" + _valuetxt + "</span>"+_str;
            var _parent = $this.parent(),
                _width  = $this.outerWidth()+25;
            $this.hide();
            _parent.width(_width).append(_str);
            _parent.find('.ui_combox_list').width(_width);
            _parent.off('click').on('click', function(){
                var _offset = $(this).offset();
                var _thisHeight = $(this).height();
                var _bottom = $(document).height() - _offset.top - _thisHeight;
                var _ul = $(this).find('ul');
                if(_bottom > _ul.height()){
                    _ul.css('bottom', 'auto');
                    _ul.css('top', _thisHeight + 'px');
                }else{
                    _ul.css('top', 'auto');
                    _ul.css('bottom', _thisHeight + 'px');
                }
                $(this).siblings().find('ul').hide();
                _ul.toggle();
                return false;
            })
            $('a.ui_combox_option').clickChange(op);

            $('body').on('click', function(){
                $('ul.ui_combox_list').hide();
            })

        });
    };
    $.fn.clickChange = function(opt){
        function removeNextOption(obj){
            var $obj = $(obj);
            var $opt = $obj.data() || {};
            var _defaultName = $opt.name;
            var _ref = $opt.ref;
            var _defaultOptionStr = "<option value=''>" + _defaultName + "</option>";
            var _defaultLiStr = "<li data-value=''><a class='ui_combox_option selected' href='javascript:void(0);'>" + _defaultName + "</a></li>";

            $obj.html(_defaultOptionStr);
            $obj.siblings('.ui_combox_list').html(_defaultLiStr);
            $obj.siblings('.ui_combox_txt').html(_defaultName);
            if(_ref){
                var next = $('select[name='+_ref+']');
                removeNextOption(next);
            }
        }

        var op = $.extend({}, {
            'className' : 'ui_combox'
        }, opt);

        return this.each(function(){
            var $this = $(this);
            var _parent = $this.parents('.'+op.className);

            $this.off('click').on('click', function(){
                if(!$this.hasClass('selected')){
                    var _txt = $this.text();
                    var _val = $this.parent('li').attr('data-value') || "";
                    var $opt = $('select', _parent).data();

                    var _type = $opt.stype || "";
                    var _ref = $opt.ref || null;
                    var _url = $opt.url || "";
                    var _data = {"id": _val};

                    if(_url != "" && _val != ""){
                        if(_type == 'selectBack'){
                            var ids = $opt.ids || '';
                            if(ids != '') _data.ids = ids;
                            Juuz.ajaxHtml(_url, _data, 'html', function(str){
                                $('#' + _ref).html(str);
                                Manage.uiInit($('#' + _ref));
                            }, Juuz.noop)
                        }else{
                            Juuz.ajaxGet(_url, _data, function(data){
                                if(data.statusCode == Juuz.code.ajaxNotLogin){
                                    var message = data.message || "没有权限访问";
                                    Juuz.errorMsg(message, 1500, function(){
                                        window.location.href = data.url;
                                    });
                                    return false;
                                }

                                var _data = data.res;
                                var _optionStr = "";
                                var _liStr = "";
                                for(var i = 0; i < _data.length; i++){
                                    var isSelected = i == 0 ? 'selected' : '';
                                    _optionStr += "<option value='" + _data[i].id + "'>" + _data[i].name + "</option>";
                                    _liStr += "<li data-value='" + _data[i].id + "'>";
                                    _liStr += "<a class='ui_combox_option " + isSelected + "' href='javascript:void(0);'>" + _data[i].name + "</a>";
                                    _liStr += "</li>";
                                }

                                var _select = $("select[name="+_ref+"]");
                                var _defaultName = $(_select).data().name;
                                _select.html(_optionStr);
                                _select.siblings('.ui_combox_list').html(_liStr);
                                _select.siblings('.ui_combox_txt').html(_defaultName);

                                var _rref = _select.data().ref;
                                var $rselect = $("select[name="+_rref+"]");
                                if(_rref && $rselect.size() > 0){
                                    removeNextOption($rselect);
                                }

                                $('a.ui_combox_option').clickChange(op);
                            }, Juuz.noop);
                        }
                    }

                    if(_val == ""){
                        var $sselect = $("select[name="+_ref+"]");
                        if($sselect.size() > 0){
                            removeNextOption($sselect);
                        }
                    }

                    _parent.find('.ui_combox_txt').text(_txt);
                    $this.addClass('selected').parent('li').siblings('li').find('a').removeClass('selected');
                    _parent.find('select').val(_val).trigger('change');
                }
                $this.parents('.ui_combox_list').hide();
                return false;
            });
        });
    }
})(jQuery);
