/**
 * 树的搜索函数 Vic.z 2012-6-5
 * @param treeId 树的id
 * @param inputObj 搜索input框对象
 * @param searchBtn 搜索按钮
 * @param clearBtn 清空按钮
 */
function bindTreeSearchFun(treeId, inputObj, searchBtn, clearBtn){
	var orgTreeObj = $.fn.zTree.getZTreeObj(treeId);
	var oldInfo = "";
	var nodeList = [];
	var hightLightList = [];
	if(searchBtn){
		searchBtn.click(function(){ //添加“搜索”事件
			var searchInfo = inputObj.val();
			if(searchInfo!=oldInfo || hightLightList.length==0){
				clearHightligth();
				
				nodeList = orgTreeObj.getNodesByParamFuzzy("name", $.trim(searchInfo));
				if(searchInfo!=""){ // && keyc!=40 && keyc!=38 && keyc!=37 && keyc!=39 && keyc!=13
					if(nodeList!=null && nodeList.length>0){
						orgTreeObj.expandAll(false);
						setTimeout(function(){
							//orgTreeObj.expandNode(orgTreeObj.getNodes[0], true, false, true);
							updateNodes(true);
						}, 300);
					}else{
						alert("未找到相关数据！");
					}
				}
				oldInfo = searchInfo;
			}
			inputObj.focus();
		});
		//添加回车事件
		inputObj.unbind().keyup(function(e){
			var event = e || window.event; 
    	    if(event.keyCode == 13){
    	    	searchBtn.click();
    	    }
		});
	}
	if(clearBtn){
		clearBtn.click(function(){ //添加“清空”事件
			inputObj.val("");
			clearHightligth();
			inputObj.focus();
		});
	}
	/**
	 * 清空高亮显示
	 */
	function clearHightligth(){
		if(hightLightList.length>0){
			nodeList = hightLightList;
			updateNodes(false);
		}
		//nodeList = orgTreeObj.getNodesByParamFuzzy("color", "#A60000");
	}
	/**
	 * 更新节点显示及展开效果
	 * @param highlight true：高亮显示，false：取消高亮显示
	 */
	function updateNodes(highlight) {
		hightLightList = [];
		for( var i=0; i<nodeList.length; i++) {
			nodeList[i].highlight = highlight;
			orgTreeObj.updateNode(nodeList[i]);
			
			if(highlight){
				hightLightList.push(nodeList[i]);
				var pNode = nodeList[i].getParentNode();
				if(!pNode){
					orgTreeObj.expandNode(nodeList[i], true, false, true);
				}else if(!pNode.open){
					orgTreeObj.expandNode(pNode, true, false, true);
				}
			}
		}
	}
}


/**
 * 默认展开选择节点下的所有节点，并选择第一个叶子节点 lh :20151214
 */
var treeSelectInitCheck = 0;
var salaryInitSelNode = null;
function getInitFirstTreeNode(treeId){
	treeSelectInitCheck = 0;
	salaryInitSelNode = null;
	var treeObj = $.fn.zTree.getZTreeObj(treeId);
	var nodeList = $.fn.zTree.getZTreeObj(treeId).getNodes();
	
	if(nodeList && nodeList.length > 0){
		if(nodeList[0].children){
			var childs = nodeList[0].children;
			if (childs && childs.length>0) {
				treeObj.expandNode(childs[0], true, true, true);
				for(var v=0;v<childs.length;v++){
					if(childs[v].open){
						getFirstCheckedRecordsNode(childs[v],treeObj);
						break;
					}else{
						salaryInitSelNode = childs[v];
						treeObj.selectNode(childs[v]);
						break;
					}
					if(treeSelectInitCheck == 1 && salaryInitSelNode != null ){
						break;
					}
				}
			}else{
				salaryInitSelNode = nodeList[0];
			}
		}else{
			salaryInitSelNode = nodeList[0];
		}
	}
	return salaryInitSelNode;
}
/**
 * 获取默认选择节点
 * @param tNode
 * @returns
 */
function getFirstCheckedRecordsNode(tNode,treeObj){
	var childrens = tNode.children;
	if(childrens && childrens.length>0){
		for(var v=0;v<childrens.length;v++){
			if(treeSelectInitCheck == 1){
				break;
			} 
			if(childrens[v].isParent){
				getFirstCheckedRecordsNode(childrens[v],treeObj);
			}else{
				treeSelectInitCheck = 1;
				salaryInitSelNode = childrens[v];
				treeObj.selectNode(childrens[v]);
				break;
			}
		}
	}
}


