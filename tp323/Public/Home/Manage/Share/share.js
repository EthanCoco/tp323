//提示信息（头部）
var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
function PNotifyMsg(title,text){
	new PNotify({
        title: title,
        text: text,
        type: 'custom',
        addclass: "pnotify-primary stack-bar-top",
        icon: "fa fa-user",
        cornerclass: "pnotify-sharp",
        width: "100%",
        stack: stack_bar_top
    });
}

//弹出框提示
function modalMsg(title,msg){
	var _html ='<div class="modal fade" id="modalmsg" tabindex="-1" role="dialog" aria-labelledby="modal-nofooter-label">';
        _html+='<div class="modal-dialog" role="document">';
        _html+='<div class="modal-content">';
        _html+='<div class="modal-header">';
        _html+='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        _html+='<h4 class="modal-title" id="modal-nofooter-label">'+title+'</h4>';
        _html+='</div>';
        _html+='<div class="modal-body">';
        _html+=	msg;
        _html+='</div>';
        _html+='</div>';
        _html+='</div>';
    	_html+='</div>';
    $("#modalcontent").html(_html);
}


/**
 * 禁止浏览器前进后退功能
 */
function unforward(){
	if (window.history && window.history.pushState) {
		$(window).on('popstate', function () {
		　　window.history.pushState('forward', null, '#');
		　　window.history.forward(1);
	　　	});
	}
　　	window.history.pushState('forward', null, '#'); 
	window.history.forward(1);
}

/**
 * 校验密码
 */
function validatePwd(passWord){
	var regExp = /^[0-9 | A-Z | a-z]{6,20}$/;
	if(!passWord.match(regExp)){
		return false;
	}else{
		return true;
	}
}

//校验邮箱
function validateEmail(email){
	var regExp = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	if(!email.match(regExp)){
		return false;
	}else{
		return true;
	}
}

//校验身份证
function validateIdcard(idcard){
	var regExp = /^\d{15}(\d{2}[A-Za-z0-9])?$/i;
	if(!idcard.match(regExp)){
		return false;
	}else{
		return true;
	}
}

//校验手机号码
function validatePhone(phone){
	var regExp = /^(1)\d{10}$/i;
	if(!phone.match(regExp)){
		return false;
	}else{
		return true;
	}
}
/**
 * 校验账号
 * 字母开头 可带数字和下划线   长度4-16
 */
function validateUcount(ucount){
	var regExp = /^[a-zA-Z]\w{3,15}$/;
	if(!ucount.match(regExp)){
		return false;
	}else{
		return true;
	}
}

/**
 * 校验规则路由
 * module/controller/action 模式
 */
function validateRule(str){
	var strArr = str.split('/');
	if(strArr.length !=3) return false;
	
	return true;
}

/**
 * 校验按钮名称
 * 按钮名称规则为 有字符和数字组成且首字母开头长度为2-10个字符
 * @param {Object} str
 */
function validateBtnName(str){
	var regExp = /^[a-zA-Z_][a-zA-Z0-9_]\w{0,8}$/;
	if(!str.match(regExp)){
		return false;
	}else{
		return true;
	}
}

/**
 * 初始化onblur 及校验
 * @param {Object} pid
 */
function initBlurValidate(pid){
	//初始化添加onblur事件（校验）
	$("#"+pid).find("input[req=1]").each(function(){
		$(this).bind("blur",function(){
			//获取参数值
			var value = $(this).val().trim();
			//获取校验函数名
			var validateFun = $(this).attr("validate");
			//拼接函数
			var validateFunName = validateFun+"('"+value+"')";
			//判断是否为空  如果为空不做处理
			if(value == ""){
				$(this).removeClass("star");
				$(this).removeAttr("error");
				return ;
			}
			
			//执行函数 返回校验结果   添加删除相应样式
			if( eval( validateFunName ) == false){
				PNotifyMsg($(this).attr("reqt"),$(this).attr("reqc"));
				$(this).addClass("star");
				$(this).attr("error",1);
				return ;
			}else{
				$(this).removeClass("star");
				$(this).removeAttr("error");
			}
		});
		
	});
	
	//添加onblur事件（重名） 同上
	$("#"+pid).find("input[ren=1]").each(function(){
		$(this).bind("blur",function(){
			var value = $(this).val().trim();
			var validateFun = $(this).attr("renf");
			var validateFunName = validateFun+"('"+value+"')";
			if(value == ""){
				$(this).removeClass("star");
				$(this).removeAttr("error");
				return ;
			}
			
			if( eval( validateFunName ) == true){
				PNotifyMsg(__tips, $(this).attr("title") + __rename);
				$(this).addClass("star");
				$(this).attr("error",1);
				return ;
			}else{
				$(this).removeClass("star");
				$(this).removeAttr("error");
			}
		});
		
	});
}

/**
 * 校验表单数据完整性
 * @param {Object} pid
 */
function validateForm(pid){
	var msg = __lang=="zh-cn" ? "以下字段值不能为空：</br>" : "The following field values cannot br empty!</br>";
	var msgError = __lang=="zh-cn" ? "以下字段格式错误：</br>" : "The following field format is wrong!</br>";
	var flag = 0;
	var flagError = 0;
	//遍历 input
	$("#"+pid).find("input[r=1]").each(function(){
		//判断是否为空
		if($(this).val().trim() == ""){
			msg += $(this).attr("title")+"</br>";
			//标记空
			flag++;
		}
		//判断是否存在错误值
		if(typeof $(this).attr("error") !== "undefined"){
			msgError +=$(this).attr("title")+"</br>";
			//标记错误值
			flagError++;
		}
	});
	
	//遍历	textarea 同上
	$("#"+pid).find("textarea[r=1]").each(function(){
		if($(this).val().trim() == ""){
			msg += $(this).attr("title")+"</br>";
			flag++;
		}
		if(typeof $(this).attr("error") !== "undefined"){
			msgError +=$(this).attr("title")+"</br>";
			flagError++;
		}
	});
	
	//遍历	select 同上
	$("#"+pid).find("select[r=1]").each(function(){
		if($(this).val() == null  || typeof $(this).val() === "null" || $(this).val() == ""){
			msg += $(this).attr("title")+"</br>";
			flag++;
		}
		if(typeof $(this).attr("error") !== "undefined"){
			msgError +=$(this).attr("title")+"</br>";
			flagError++;
		}
	});
	
	//先判断错误值是否存在
	if(flagError>0){
		PNotifyMsg(__tips,msgError);
		return false;
	}
	//判断空值是否存在
	if(flag>0){
		PNotifyMsg(__tips,msg);
		return false;
	}
	
	return true;
}

/**
 * 获取表单数据
 */
function getFormData(pid){
	var info = {};
	
	//遍历	input
	$("#"+pid).find("input").each(function(){
		//获取图片标识的取值
		if(typeof $(this).attr("image") !== "undefined"){
			info[$(this).attr("name")] = $(this).attr("value");
		//获取单选按钮的取值
		}else if($(this).attr("type") === "radio"){
			info[$(this).attr("name")] = $("input[name='"+$(this).attr("name")+"']:radio:checked").val();
		//普通文本的取值
		}else{
			info[$(this).attr("name")] = $(this).val();
		}
	});
	
	//遍历	textarea
	$("#"+pid).find("textarea").each(function(){
		//获取图片标识的取值
		if(typeof $(this).attr("image") !== "undefined"){
			info[$(this).attr("name")] = $(this).attr("value");
		}else{
			info[$(this).attr("name")] = $(this).val();
		}
	});
	
	//遍历	select 
	$("#"+pid).find("select").each(function(){
		//获取select取值
		info[$(this).attr("name")] = $(this).val();
	});
	
	return info;
}

/**
 * 显示浮动层
 * @param {Object} obj
 * @param {Object} id
 */
function manager_showMore(obj,id){
	//隐藏ul列表
	$(".tabsMoreList").hide();
	objs_idname = $(obj).next();
	if(typeof(id)!='undefined')
		objs_idname = $("#"+id);
	if(objs_idname.css('display')=='none'){
		var bottom=$(obj).position().bottom;
		var left=$(obj).position().left;
		objs_idname.css("bottom",bottom);
		objs_idname.css("left",left);
		objs_idname.show();;
		showFlag = true;
		$("body").unbind("click", noneMore_list).bind("click", noneMore_list);
	}else{
		noneMore_list();
	} 
}

/**
 * 隐藏浮动层
 */
function noneMore_list(){
	if(!showFlag && objs_idname.css('display')=="block"){
		objs_idname.hide();
		$("body").unbind("click", noneMore_list);
	}
	showFlag = false;
}

/**
 * 日期转换
 * @param {Object} format
 */
Date.prototype.format = function(format) {
       var date = {
              "M+": this.getMonth() + 1,
              "d+": this.getDate(),
              "h+": this.getHours(),
              "m+": this.getMinutes(),
              "s+": this.getSeconds(),
              "q+": Math.floor((this.getMonth() + 3) / 3),
              "S+": this.getMilliseconds()
       };
       if (/(y+)/i.test(format)) {
              format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
       }
       for (var k in date) {
              if (new RegExp("(" + k + ")").test(format)) {
                     format = format.replace(RegExp.$1, RegExp.$1.length == 1
                            ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
              }
       }
       return format;
}

/**
 * 时间轴格式转换
 * @param {Object} format
 * @param {Object} timestamp
 */
function transDate(format, timestamp){ 
	var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date()); 
	var pad = function(n, c){ 
	if((n = n + "").length < c){ 
		return new Array(++c - n.length).join("0") + n; 
	}else{ 
		return n; 
	} 
	}; 
	var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]; 
	var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"}; 
	var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]; 
	var f = { 
		// Day 
		d: function(){return pad(f.j(), 2)}, 
		D: function(){return f.l().substr(0,3)}, 
		j: function(){return jsdate.getDate()}, 
		l: function(){return txt_weekdays[f.w()]}, 
		N: function(){return f.w() + 1}, 
		S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'}, 
		w: function(){return jsdate.getDay()}, 
		z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0}, 
   
		// Week 
		W: function(){ 
			var a = f.z(), b = 364 + f.L() - a; 
			var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1; 
			if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){ 
				return 1; 
			}else{ 
				if(a <= 2 && nd >= 4 && a >= (6 - nd)){ 
					nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31"); 
					return date("W", Math.round(nd2.getTime()/1000)); 
				}else{ 
					return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0); 
				} 
			} 
		}, 
   
		// Month 
		F: function(){return txt_months[f.n()]}, 
		m: function(){return pad(f.n(), 2)}, 
		M: function(){return f.F().substr(0,3)}, 
		n: function(){return jsdate.getMonth() + 1}, 
		t: function(){ 
			var n; 
			if( (n = jsdate.getMonth() + 1) == 2 ){ 
				return 28 + f.L(); 
			}else{ 
				if( n & 1 && n < 8 || !(n & 1) && n > 7 ){ 
					return 31; 
				}else{ 
					return 30; 
				}	 
			} 
		}, 
   
		// Year 
		L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0}, 
		//o not supported yet 
		Y: function(){return jsdate.getFullYear()}, 
		y: function(){return (jsdate.getFullYear() + "").slice(2)}, 
   
		// Time 
		a: function(){return jsdate.getHours() > 11 ? "pm" : "am"}, 
		A: function(){return f.a().toUpperCase()}, 
		B: function(){ 
			// peter paul koch: 
			var off = (jsdate.getTimezoneOffset() + 60)*60; 
			var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off; 
			var beat = Math.floor(theSeconds/86.4); 
			if (beat > 1000) beat -= 1000; 
			if (beat < 0) beat += 1000; 
			if ((String(beat)).length == 1) beat = "00"+beat; 
			if ((String(beat)).length == 2) beat = "0"+beat; 
			return beat; 
		}, 
		g: function(){return jsdate.getHours() % 12 || 12}, 
		G: function(){return jsdate.getHours()}, 
		h: function(){return pad(f.g(), 2)}, 
		H: function(){return pad(jsdate.getHours(), 2)}, 
		i: function(){return pad(jsdate.getMinutes(), 2)}, 
		s: function(){return pad(jsdate.getSeconds(), 2)}, 
		//u not supported yet 
   
		// Timezone 
		//e not supported yet 
		//I not supported yet 
		O: function(){ 
			var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4); 
			if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t; 
				return t; 
		}, 
		P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))}, 
		//T not supported yet 
		//Z not supported yet 
	   
		// Full Date/Time 
		c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()}, 
		//r not supported yet 
		U: function(){return Math.round(jsdate.getTime()/1000)} 
	}; 
   
   	var reg = /[\\]?([a-zA-Z])/g;
	return format.replace(reg, function(t,s){ 
		if( t!=s ){ 
			// escaped 
			ret = s; 
		}else if( f[s] ){ 
			// a date function exists 
			ret = f[s](); 
		}else{ 
			// nothing special 
			ret = s; 
		} 
		return ret; 
	}); 
}

/**
 * 私有函数
 */
function validateRename(value){
	var result = false;
	$.ajax({
		type:"post",
		url:constLJL.UsertabRenameUrl,
		async:false,
		dataType:'json',
		data:{data:value},
		success:function(json){
			json.result == "1" ? (result=true) : (result=false);  
		}
	});
	return result;
}

/**
 * 初始化fileinput插件
 * 返回回调数据
 */
function imageUpload(pid){
	var projectfileoptions  = {
        showUpload : false,
        showRemove : true,
        language : __lang,
        dropZoneEnabled:false,
        allowedPreviewTypes : [ 'image' ],
        allowedFileExtensions : [ 'jpg', 'png', 'gif','jpeg' ],
        maxFileSize : 2000,
        //上传的地址
        uploadUrl: constLJL.ImageUpload
   };
	// 文件上传框
	$('input[class=projectfile]').each(function() {
	    var imageurl = $(this).attr("value");
	    if (imageurl) {
	        var op = $.extend({
	            initialPreview : [ // 预览图片的设置
	            "<img src='" + imageurl + "' class='file-preview-image'>" ]
	        }, projectfileoptions);
	
	        $(this).fileinput(op);
	    } else {
	        $(this).fileinput(projectfileoptions);
	    }
	    
	});
   //异步上传返回结果处理
	$('input[class=projectfile]').on("fileuploaded",function(event, data, previewId, index) {
	 	return $("#"+pid).attr("value",data.response.result);
	});
}

/**
 * 获取地区信息
 * @param {Object} sel
 * @param {Object} type_id
 * @param {Object} selName
 * @param {Object} url
 */
function loadRegion(sel,type_id,selName,url){
	var defaultOption = __lang == "zh-cn" ? "请选择" : "Please Choose";
    $("#"+selName+" option").each(function(){
        $(this).remove();
    });
    $("<option value=''>"+defaultOption+"</option>").appendTo($("#"+selName));
    if($("#"+sel).val()==""){
        return;
    }
    $.getJSON(url,{pid:$("#"+sel).val(),type:type_id},
        function(data){
            if(data){
                $.each(data,function(idx,item){
                    $("<option value="+item.id+">"+item.name+"</option>").appendTo($("#"+selName));
                });
            }else{
                $("<option value=''>"+defaultOption+"</option>").appendTo($("#"+selName));
            }
        }
    );
}

