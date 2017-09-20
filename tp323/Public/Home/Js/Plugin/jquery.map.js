/**
	地图经纬度获取
**/

(function($){
	$.fn.mapInit = function(){
		this.each(function(){
			var $this = $(this);
			if($this.hasClass('ui_lock')){
				return;
			}else{
				$this.addClass('ui_lock ui_map_wrap');
			}
			var map, marker, searchValue, R;
			R = '' + new Date().getTime() + "_" + Juuz.randNum(4);
			var $opt = $this.data();

			var op = $.extend({
				type : '',
				lng : '116.403891',
				lat : '39.915129',
				width : '800',
				height : '400',
				lookupGroup : '',
				R : R
			}, $opt);

			if(op.type == 'dialog'){
				mapDialogShow($this);
			}else{
				$this.append(TPL.mapContent(op));
		    	baiduMapShow(map, marker, searchValue, op);
			}
		});

		function mapDialogShow(obj){
			var $this = obj;
			var R = '' + new Date().getTime() + "_" + Juuz.randNum(4);
			var $opt = $this.data();
			var op = $.extend({
				type : '',
				lng : '',
				lat : '',
				txLng : '',
				txLat : '',
				width : '500',
				height : '400',
				lookupGroup : '',
				R : R
			}, $opt);

			//转换百度坐标为腾讯坐标
			var position = GPS.bd_decrypt(op.lat, op.lng);
		    op.txLng = position.lon;
	    	op.txLat = position.lat;

	    	$this.append(TPL.mapInput(op));
	    	Juuz.uiInit('.ui_map_wrap');

	    	$('a.btn_map_dialog').each(function(){
				var $this = $(this);

				$this.off('click').on('click', function(){
					var $opt = $this.data();
					var op = $.extend({
						type : '',
						width : '500',
						height : '400',
						lng : null,
						lat : null,
						lookupGroup : ''
					}, $opt);

					var lng = parseFloat($('#mapDialogBDLng_' + op.R).val());
					var lat = parseFloat($('#mapDialogBDLat_' + op.R).val());
					if(lng != 0){
						op.lng = lng;
					}
					if(lat != 0){
						op.lat = lat;
					}

					var $obj = {
						title: '地图获取经纬度',
						content: TPL.mapDialog(op),
						quickClose: false,
						okValue: '保存',
						ok: function(){
							mapUpdateLatlng(op.lookupGroup, op.R);
						}
					}
					Juuz.popBox($obj, function(d){
						var $box = $('.ui-popup');
						Juuz.uiInit($box);
						if($('.js_map_warp', $box).size() > 0){
				            $('.js_map_warp').mapInit();
				        };
						d.reset();
					});

			        return false;
				})
			});
		};

		function baiduMapShow(map, marker, searchValue, op){
			map = new BMap.Map('allmap_' + op.R);
			var point = new BMap.Point(op.lng, op.lat);
			map.centerAndZoom(point, 15);

			//启用滚轮放大缩小，默认禁用
			map.enableScrollWheelZoom();
			map.enableContinuousZoom();

			//创建拖拽标注
			marker = new BMap.Marker(point);
			map.addOverlay(marker);
			marker.enableDragging();
			marker.addEventListener("dragend", function(e){
				setInputValue(e.point.lng, e.point.lat);
			});

		    //转换百度坐标为腾讯坐标
		    var position = GPS.bd_decrypt(op.lat, op.lng);
		    $('#mapTXLng_' + op.R).val(position.lon);
			$('#mapTXLat_' + op.R).val(position.lat);

			//下拉提示及搜索
			var ac = new BMap.Autocomplete({
				"input": 'searchInput_' + op.R,
				"location": map
			});
			//鼠标点击下拉列表后的事件
			ac.addEventListener("onconfirm", function(e) {
				var _value = e.item.value;
				searchValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;

				setPlace();
			});

			var setPlace = function(){
				map.clearOverlays();    //清除地图上所有覆盖物
				var myFun = function(){
					var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
					map.centerAndZoom(pp, 18);
					var marker = new BMap.Marker(pp);
					map.addOverlay(marker);    //添加标注
					marker.enableDragging();

					setInputValue(pp.lng, pp.lat);
					marker.addEventListener("dragend", function(e){
						setInputValue(e.point.lng, e.point.lat);
					});
				}
				var local = new BMap.LocalSearch(map, { //智能搜索
					onSearchComplete: myFun
				});
				local.search(searchValue);
			};
			var setInputValue = function(lng, lat){
				$('#mapBDLng_' + op.R).val(lng);
				$('#mapBDLat_' + op.R).val(lat);

				if(lng){
					//转换百度坐标为腾讯坐标
					var latlng = GPS.bd_decrypt(lat, lng);
					$('#mapTXLng_' + op.R).val(latlng.lon);
					$('#mapTXLat_' + op.R).val(latlng.lat);
				}
			};
		}

		function mapUpdateLatlng(_lookupGroup, R){
			var baiduLng = $('#mapBDLng_' + R).val();
	        var baiduLat = $('#mapBDLat_' + R).val();
	        var tencentLng = $('#mapTXLng_' + R).val();
	        var tencentLat = $('#mapTXLat_' + R).val();
	        var argArr = {
	            'bdLng': baiduLng,
	            'bdLat': baiduLat,
	            'txLng': tencentLng,
	            'txLat': tencentLat
	        };
	        Juuz.updateInputValue(argArr, _lookupGroup);
		}
	};

})(jQuery);





