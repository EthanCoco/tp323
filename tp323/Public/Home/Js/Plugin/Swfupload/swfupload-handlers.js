/*
	图片上传
*/
var jcrop_api = null;
function jcropImgInit(){
	jcrop_api = $.Jcrop('#cropbox', {
        aspectRatio: 640/1008,             //选框比例
        bgOpacity: 0.5,
        boxWidth: 718,
        boxHeight: 3000,
        onSelect: function(c){
            $('#jcropX').val(c.x);
            $('#jcropY').val(c.y);
            $('#jcropW').val(c.w);
            $('#jcropH').val(c.h);
        },
        onRelease: function(){
            $('#jcropX').val('');
            $('#jcropY').val('');
            $('#jcropW').val('');
            $('#jcropH').val('');
        }
    });
}

function SWFInit(){
	$('input.js_hidden_file').each(function(i, item){
		var $this = $(item);
		if($this.hasClass("swfupload-ui")){
			return ;
		}else{
			$this.addClass("swfupload-ui");
		}
		var _random = '' + new Date().getTime() + "_" + Juuz.randNum(4);

		var $opt = $this.data(),
			_type = $opt.fileType || 'img',
			_width = $opt.width.split(',')[0] || 200,
			_height = $opt.height.split(',')[0] || 200,
			_btnHeight = $opt.btnHeight || 30,
			_name = $opt.name || '上传图片',
			_title = $opt.title || '缩略图',
			_value = $opt.thumb || "",
			_x = $opt.x || '',
			_y = $opt.y || '',
			_w = $opt.w || '',
			_h = $opt.h || '',
			_isWatermark = $opt.watermark || 0;

		if(_value == ''){
			_value = $this.val();
		}

		var uploadClass = _type == 'img' ? 'upload_img_wrap' : 'upload_file_wrap';
		if(_type == 'jcropImg'){
			$this.wrap('<div id="SWFWrap' + _random + '" class="' + uploadClass + '" style="width:' + _width + 'px;min-height:' + _height + 'px;margin-top:-'+(_height/2)+'px;margin-left:-'+(_width/2)+'px;"></div>');
			var jcropImgStr = "";
			jcropImgStr += '<div id="jcropImg'+_random+'" class="jcrop_img" style="display:none;">';
			jcropImgStr += '	<input type="hidden" id="jcropX" name="x" value="'+_x+'" />';
            jcropImgStr += '    <input type="hidden" id="jcropY" name="y" value="'+_y+'" />';
            jcropImgStr += '    <input type="hidden" id="jcropW" name="w" value="'+_w+'" />';
            jcropImgStr += '    <input type="hidden" id="jcropH" name="h" value="'+_h+'" />';
            jcropImgStr += '    <img id="cropbox" src="'+_value+'" style="width:718px;" />';
            jcropImgStr += '	<a href="#" class="js_jcrop_close"></a>';
			jcropImgStr += '</div>';
			$this.parents('.ui_jcrop').append(jcropImgStr);
			if(_value != '' && _x != '' && _w != ''){
				$('#jcropImg'+_random).show();
				$('#SWFWrap'+_random).hide();
				setTimeout(function(){
					jcropImgInit();
					jcrop_api.setSelect([_x, _y, _x+_w, _y+_h]);
				}, 100);
			}
		}else{
			$this.wrap('<div id="SWFWrap' + _random + '" class="' + uploadClass + '" style="width:' + _width + 'px;min-height:' + _height + 'px;"></div>');
		}

		var previewValue = _type == 'img' ? '<img src="' + _value + '" />' : _value;
		if(_type == 'file' && _value != ""){
			var uploadBoxHide = "10";
		}else{
			var uploadBoxHide = "30";
		}

		var _uploadStr = "";
		_uploadStr += '<div class="js_upload_preview" style="width:' + _width + 'px;height:' + _height + 'px;">';
        if(_value == ""){
        	_uploadStr += 	'<a id="SWFCancel' + _random + '" class="js_upload_cancel" href="javascript:void(0);"></a>';
        	_uploadStr += 	'<p style="line-height:' + _height + 'px;">' + _title + '</p>';
        }else{
        	_uploadStr += 	'<a id="SWFCancel' + _random + '" class="js_btn_cancel" data-type="' + _type + '" href="javascript:void(0);"></a>';
			_uploadStr += 	'<p style="line-height:' + _height + 'px;">' + previewValue + '</p>';
        }
        _uploadStr += '</div>';
        _uploadStr += '<div class="js_upload_box" style="z-index:' + uploadBoxHide + ';">';
        _uploadStr += 	'<a class="js_btn_upload" href="javascript:void(0);">' + _name + '</a>';
        _uploadStr += 	'<span id="SWFObj' + _random + '"></span>';
        _uploadStr += 	'<div id="SWFPro' + _random + '" class="js_upload_progress"></div>';
        _uploadStr += '</div>';


        $('#SWFWrap' + _random).append(_uploadStr);

        var fileTypes = "", fileTypesDesc = "", fileSize = "";
        if(_type == 'img'){
        	fileTypes = "*.jpg;*.png;*.gif";
        	fileTypesDesc = "图片";
        	fileSize = "2 MB";
        }else if(_type == 'file'){
        	fileTypes = "*";
        	fileTypesDesc = "文件";
        	fileSize = "2048 MB";
        }else if(_type == 'jcropImg'){
        	fileTypes = "*.jpg;*.png;*.gif";
        	fileTypesDesc = "图片";
        	fileSize = "4 MB";
        }
        var swfConfig = {
            file_types: fileTypes,
            file_types_description: fileTypesDesc,
            file_size_limit: fileSize,
            post_params: {"type": _type, "width":$opt.width, "height":$opt.height, "watermark": _isWatermark},
            button_action: SWFUpload.BUTTON_ACTION.SELECT_FILE,
            //按钮设置
            button_placeholder_id: "SWFObj" + _random,
            button_width: _width,
            button_height: _btnHeight
        }
        var settings = SWFSetConfigInfo(swfConfig, _random);
		new SWFUpload(settings);


		$('.js_btn_cancel').off('click').on('click', function(){
			var _this = $(this);
			var uploadType = _this.attr('data-type');
			var inputObj = _this.parent().siblings('input');
			var previewObj = _this.parent();
			var boxObj = _this.parent().siblings('.js_upload_box');

			if(uploadType == 'img'){
				previewObj.find('p').html(_title);
				inputObj.val('');
				_this.hide();
			}else if(uploadType == "file"){
				previewObj.find('p').html('');
				inputObj.val('');
				boxObj.css('z-index', 30);
				_this.hide();
			}
		});

		$('.js_jcrop_close').off('click').on('click', function(){
			var $this = $(this);
			$('#jcropX,#jcropY,#jcropW,#jcropH').val('');
			$('#cropbox').attr('src', '');
			$this.parents('.jcrop_img').hide();
			$this.parents('.jcrop_img').siblings('.upload_file_wrap').show();

			return false;
		});
	});

	//组图渲染
	$('.js_hidden_group').each(function(i, item){
		var $this = $(item);
		if($this.hasClass('swfupload-ui')){
			return;
		}else{
			$this.addClass("swfupload-ui");
		}
		var _random = '' + new Date().getTime() + "_" + Juuz.randNum(4);

		var $opt = $this.data(),
			_width = $opt.width.split(',')[0] || 100,
			_height = $opt.height.split(',')[0] || 100,
			_name = $opt.name || '点击上传',
			_inputName = $opt.inputName || 'img',
			_isWatermark = $opt.watermark || 0;

		$this.wrap('<div id="SWFWrap' + _random + '" class="upload_group_wrap dib-wrap"></div>').hide();

		var _uploadStr = "";
        _uploadStr += '<div id="SWFGroupBtn'+_random+'" class="dib upload_group_btn" style="width:'+_width+'px;height:'+_height+'px;">';
        _uploadStr += '    <span id="SWFObj' + _random + '"></span>';
        _uploadStr += '    <span class="upload_group_text" style="line-height:'+_height+'px;">' + _name + '</span>';
        _uploadStr += '</div>';

        $('#SWFWrap' + _random).append(_uploadStr);

        var swfConfig = {
            file_types: "*.jpg;*.png;*.gif",
            file_types_description: "图片",
            file_size_limit: "2 MB",
            post_params: {"type":'group', "inputName":_inputName, "random":_random, "width":$opt.width, "height":$opt.height, "watermark": _isWatermark},
            button_action: SWFUpload.BUTTON_ACTION.SELECT_FILES,

            //按钮设置
            button_placeholder_id: "SWFObj" + _random,
            button_width: _width,
            button_height: _height
        }
        var settings = SWFSetConfigInfo(swfConfig, _random);
		new SWFUpload(settings);

		//初始化已上传的图片
		if($this.find('input').size() > 0){
			var _uploadStrHas = "";

			$this.find('input').each(function(i,item){
				var $imgOpt = $(item).data();

				_uploadStrHas += '<div class="dib upload_group_item" style="width:'+_width+'px;height:'+_height+'px;">';
	            _uploadStrHas += '    <div class="upload_group_fieldset">';
	            _uploadStrHas += '        <div class="progressWrapper blue">';
	            _uploadStrHas += '            <div class="progressContainer">';
	            _uploadStrHas += '                <a class="js_group_btn_close" href="javascript:void(0);"></a>';
	            _uploadStrHas += '            </div>';
	            _uploadStrHas += '        </div>';
	            _uploadStrHas += '    </div>';
	            _uploadStrHas += '    <input name="'+_inputName+'[]" type="hidden" value="'+$imgOpt.img+'" autocomplete="off">';
	            _uploadStrHas += '    <img src="'+$imgOpt.thumb+'" />';
	            _uploadStrHas += '</div>';
			});

			$('#SWFGroupBtn' + _random).before(_uploadStrHas);
		}

		$('a.js_group_btn_close').off('click').on('click',function(){
			$(this).parents('.upload_group_item').fadeOut(500,function(){
				$(this).remove();
			})
		});
	});
}

/*上传配置信息*/
function SWFSetConfigInfo(config, random){
	var settings = {
		flash_url : CONF.UPLOAD_SWF_DIR + "swfupload.swf",
		upload_url: CONF.UPLOAD_PHP_FILE,
		file_types : config.file_types,
		file_types_description : config.file_types_description,
		file_size_limit : config.file_size_limit,
		file_upload_limit : 100,
		file_queue_limit : 0,
		prevent_swf_caching: true,
		use_query_string: true,
		post_params: config.post_params,
		custom_settings : {
			wrapTarget : "SWFWrap" + random,
			progressTarget : "SWFPro" + random,
			cancelButtonId : "SWFCancel" + random,
			random: random
		},
		debug: false,
		callback:config.callback || function(){},

		//按钮设置
		button_placeholder_id: config.button_placeholder_id,
		button_image_url: "",
		button_width: config.button_width,
		button_height: config.button_height,
		button_text: '<span class="theFont"></span>',
		button_text_style: ".theFont{font-size:14px;}",
		button_text_left_padding: 0,
		button_text_top_padding: 0,
		button_disabled: false,											//按钮状态
		button_cursor: SWFUpload.CURSOR.HAND,							//鼠标以上效果
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_action:config.button_action,								//一次选择文件数量

		file_queued_handler : fileQueued,								//文件列队事件侦听
		file_queue_error_handler : fileQueueError,						//文件列队错误事件侦听
		file_dialog_complete_handler : fileDialogComplete,				//文件选择完成事件侦听
		upload_start_handler : uploadStart,								//文件开始上传事件侦听
		upload_progress_handler : uploadProgress,						//上传进度事件侦听
		upload_error_handler : uploadError,								//上传错误事件侦听
		upload_success_handler : uploadSuccess,							//上传成功事件侦听
		upload_complete_handler : uploadComplete,						//上传完成事件侦听
		queue_complete_handler : queueComplete							//列队完成事件侦听
	}

	return settings;
}


/*文件列队事件侦听*/
function fileQueued(file) {
	this.setButtonDisabled(true);
	try {
		if(this.settings.post_params.type == "group"){
			var targetID = "SWF_" + file.id;
			var random = this.settings.post_params.random;
			var inputName = this.settings.post_params.inputName;
			var width = this.settings.post_params.width.split(',')[0] || 100;
			var height = this.settings.post_params.height.split(',')[0] || 100;

			if($("#SWF_UUID_"+targetID).size() == 0){
				var imgItem = "	<div id='SWF_UUID_"+targetID+"' style='width:"+width+"px;height:"+height+"px;' class='dib upload_group_item'>";
				imgItem += "		<div id='"+targetID+"' class='upload_group_fieldset'></div>";
				imgItem += "		<input name='"+inputName+"[]' type='hidden' >";
				imgItem += "		<img src='./Images/SWF_loading.gif' />";
				imgItem += "	</div>";

				$('#SWFGroupBtn'+random).before(imgItem);
			}
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setStatus("正在等待...");
		progress.toggleCancel(true, this);
	} catch (ex) {
		this.debug(ex);
	}

}

/*文件列队错误事件侦听*/
function fileQueueError(file, errorCode, message) {
	try {
		if(this.settings.post_params.type == "group"){
			var targetID = "SWF_" + file.id;
			var random = this.settings.post_params.random;
			var inputName = this.settings.post_params.inputName;
			var width = this.settings.post_params.width.split(',')[0] || 100;
			var height = this.settings.post_params.height.split(',')[0] || 100;

			if($("#SWF_UUID_"+targetID).size() == 0){
				var imgItem = "<div id='SWF_UUID_"+targetID+"' style='width:"+width+"px;height:"+height+"px;' class='dib upload_group_item'>";
				imgItem += "<div id='"+targetID+"' class='upload_group_fieldset'></div>";
				imgItem += "</div>";

				$('#SWFGroupBtn'+random).before(imgItem);
			}
			$('#SWF_UUID_'+targetID).addClass('error_item');
		}

		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("您正在上传的文件队列过多.\n" + (message === 0 ? "您已达到上传限制" : "您最多能选择 " + (message > 1 ? "上传 " + message + " 文件." : "一个文件.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setError();
		progress.toggleCancel(true, this);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("尺寸过大");
			this.debug("错误代码: 文件尺寸过大, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("零字节文件");
			this.debug("错误代码: 零字节文件, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("类型错误");
			this.debug("错误代码: 不支持的文件类型, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("未处理的错误");
			}
			this.debug("错误代码: " + errorCode + ", 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

/*文件选择完成事件侦听*/
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			//document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}

		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

/*文件开始上传事件侦听*/
function uploadStart(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setStatus("正在上传...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}

	return true;
}

/*上传进度事件侦听*/
function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setProgress(percent);
		progress.setStatus("正在上传...");
	} catch (ex) {
		this.debug(ex);
	}
}

/*上传成功事件侦听*/
function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setProgress(100);
		progress.setStatus("上传成功");

		if(this.settings.post_params.type == "group"){
			progress.setComplete();
			progress.toggleCancel(false,this);
			var data = Juuz.str2json(serverData);

			$('#SWF_UUID_SWF_'+file.id).find('input').val(data.img);
			$('#SWF_'+file.id+' .progressBarStatus').fadeOut(500);
			$('#SWF_'+file.id+' .progressBarComplete').fadeOut(500,function(){
				$('#SWF_UUID_SWF_' + file.id + ' img').attr('src', data.thumb);
			});
		}else{
			var _this = this;
			setTimeout(function(){
				progress.setComplete();
				progress.toggleCancel(false, _this);
				var data = Juuz.str2json(serverData);

				var proObj = $("#" + _this.settings.custom_settings.progressTarget);
				var cancelObj = $("#" + _this.settings.custom_settings.cancelButtonId);
				var inputObj = $("#" + _this.settings.custom_settings.wrapTarget).find('input');
				var previewObj = $("#" + _this.settings.custom_settings.wrapTarget).find('.js_upload_preview');
				var boxObj = $("#" + _this.settings.custom_settings.wrapTarget).find('.js_upload_box');

				if(_this.settings.post_params.type == "img"){
					proObj.fadeOut(500, function(){
						inputObj.val(data.img);
						previewObj.find('p').html('<img src="' + data.thumb + '" />');
						cancelObj.show();
					});
				}else if(_this.settings.post_params.type == "file"){
					inputObj.val(data.img);
					previewObj.find('p').html(data.img);
					proObj.fadeOut();
					cancelObj.show();
					boxObj.css('z-index', 10);
				}else if(_this.settings.post_params.type == "jcropImg"){
					proObj.fadeOut();
					cancelObj.show();
					$('#cropbox').attr('src', data.img);
					$('#SWFWrap'+_this.settings.custom_settings.random).hide();
					$('#jcropImg'+_this.settings.custom_settings.random).show();
					if(jcrop_api){
						jcrop_api.setImage(data.img);
					}else{
						setTimeout(function(){
							jcropImgInit();
						}, 100);
					}
				}
			},800)
		}
	} catch (ex) {
		this.debug(ex);
	}
}

/*上传错误事件侦听*/
function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget, this);
		progress.setError();
		progress.toggleCancel(true,this);

		if(this.settings.post_params.type == "group"){
			$('#SWF_UUID_SWF_'+file.id).addClass('error_item').find('input,img').remove();
		}

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("上传错误");
			this.debug("错误代码: HTTP错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("上传失败");
			this.debug("错误代码: 上传失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("服务器错误");
			this.debug("错误代码: IO 错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("安全错误");
			this.debug("错误代码: 安全错误, 文件名: " + file.name + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("超出上传限制.");
			this.debug("错误代码: 超出上传限制, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("无法验证.  跳过上传.");
			this.debug("错误代码: 文件验证失败, 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			//If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("取消");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("停止");
			break;
		default:
			progress.setStatus("未处理的错误: " + errorCode);
			this.debug("错误代码: " + errorCode + ", 文件名: " + file.name + ", 文件尺寸: " + file.size + ", 信息: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

/*上传完成事件侦听*/
function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
		this.setButtonDisabled(false);
	}
}

//列队完成事件侦听（列队插件）
function queueComplete(numFilesUploaded) {
	//var status = document.getElementById("divStatus");
	//status.innerHTML = numFilesUploaded + " 个文件" + (numFilesUploaded === 1 ? "" : "s") + "已上传.";
}



/*---------------------------------------------------------------------分割线----------------------------------------------------------------------------*/

/*文件上传进度函数*/
function FileProgress(file, targetID, swfu) {
	this.fileProgressID = file.id;
	this.opacity = 100;
	this.height = 0;
	var fileSize = parseFloat(file.size/1024).toFixed(2);

	if(swfu.settings.post_params.type == "group"){
		var targetID = "SWF_" + file.id;
	}else{
		$("#"+targetID).show();
	}

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressSize = document.createElement("div");
		progressSize.className = "progressSize";
		progressSize.appendChild(document.createTextNode("("+fileSize+"kb)"));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressBarText = document.createElement("div");
		progressBarText.className = "progressBarInProgressText";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		progressBar.appendChild(progressBarText);
		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressSize);
		this.fileProgressElement.appendChild(progressBar);
		this.fileProgressElement.appendChild(progressStatus);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		if(swfu.settings.post_params.type == "group"){
			document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		}else{
			$("#"+targetID).html(this.fileProgressWrapper);
		}
	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}

//设置上传进度条
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressWrapper.className = "progressWrapper green";
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].firstChild.style.width = percentage + "%";
};

//设置上传完成
FileProgress.prototype.setComplete = function () {
	this.fileProgressWrapper.className = "progressWrapper blue";
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].firstChild.style.width = "0%";
};

//设置上传错误
FileProgress.prototype.setError = function () {
	this.fileProgressWrapper.className = "progressWrapper red";
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].firstChild.style.width = "";
};

//设置上传取消按钮
FileProgress.prototype.setCancelled = function () {
	this.fileProgressWrapper.className = "progressWrapper";
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].firstChild.style.width = "";
};

//设置上传状态
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[4].innerHTML = status;
	if(status != "正在等待..." && status != "正在上传..." && status != "上传成功"){
		var _this = this;
		setTimeout(function(){
			if($("#JS_upload_progress")) {
				$("#JS_upload_progress").fadeOut(500);
			}

			var _parent = $('#' + _this.fileProgressID);
			if(_parent.size() > 0){
				_parent.parent('.js_upload_progress').fadeOut(500);
			}
		},1000)
	}
};

//上传取消按钮点击事件
FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
	if (swfUploadInstance) {
		var fileID = this.fileProgressID;
		var type = swfUploadInstance.settings.post_params.type;

		if(type == "group"){
			this.fileProgressElement.childNodes[0].onclick = function () {
				swfUploadInstance.cancelUpload(fileID);
				$('#SWF_UUID_SWF_'+fileID).fadeOut(500,function(){
					$('#SWF_UUID_SWF_'+fileID).remove();
				})
				return false;
			}
		}else{
			var cancelObj = $("#" + swfUploadInstance.settings.custom_settings.cancelButtonId);
			var inputObj = $("#" + swfUploadInstance.settings.custom_settings.wrapTarget).find('input');
			var previewObj = $("#" + swfUploadInstance.settings.custom_settings.wrapTarget).find('.js_upload_preview');
			var boxObj = $("#" + swfUploadInstance.settings.custom_settings.wrapTarget).find('.js_upload_box');
			var previewText = inputObj.data().title;

			cancelObj.off('click').on('click',function(){
				if(type == 'img'){
					swfUploadInstance.cancelUpload(fileID);
					previewObj.find('p').html(previewText);
					inputObj.val('');
					cancelObj.hide();
				}else if(type == "file"){
					previewObj.find('p').html('');
					inputObj.val('');
					cancelObj.hide();
					boxObj.css('z-index', 30);
				}
			})
		}
	}
};

//上传文字信息的隐藏
FileProgress.prototype.disappear = function () {
	var reduceOpacityBy = 15;
	var reduceHeightBy = 4;
	var rate = 30;	// 15 fps

	if (this.opacity > 0) {
		this.opacity -= reduceOpacityBy;
		if (this.opacity < 0) {
			this.opacity = 0;
		}

		if (this.fileProgressWrapper.filters) {
			try {
				this.fileProgressWrapper.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.opacity;
			} catch (e) {
				this.fileProgressWrapper.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + this.opacity + ")";
			}
		} else {
			this.fileProgressWrapper.style.opacity = this.opacity / 100;
		}
	}

	if (this.height > 0) {
		this.height -= reduceHeightBy;
		if (this.height < 0) {
			this.height = 0;
		}

		this.fileProgressWrapper.style.height = this.height + "px";
	}

	if (this.height > 0 || this.opacity > 0) {
		var oSelf = this;
		setTimeout(function () {
			oSelf.disappear();
		}, rate);
	} else {
		this.fileProgressWrapper.style.display = "none";
	}
};




