function prepareTips(){
	$j('.hTip').each(function(){
		addTipIcon(this);
	});
	$j("[class^=hTipIA]").each(function(){
		var classInfo = $j(this).attr('class');
		var classInfoArr = classInfo.split('.');
		var insertAfterObj = $j('#'+classInfoArr[1]);
		addTipIcon(this, insertAfterObj);
	});
	$j("[class^=hTipIB]").each(function(){
		var classInfo = $j(this).attr('class');
		var classInfoArr = classInfo.split('.');
		var insertBeforeObj = $j('#'+classInfoArr[1]);
		addTipIcon(this, false, insertBeforeObj);
	});
}

function addTipIcon(targetObj, insertAfterObj, insertBeforeObj){
	insertAfterObj = insertAfterObj || false;
	insertBeforeObj = insertBeforeObj || false;
	
	var targetId = $j(targetObj).attr("id");
	if($j(targetObj).attr("type") == "radio" && !insertAfterObj && !insertBeforeObj){
		var radioBtnId = $j(targetObj).attr("id");
		var idLength=radioBtnId.length;
		var yesNoId = radioBtnId.charAt(idLength-1);
		if(yesNoId == 0){
			return false;
		}
	}
	var newIcon = $j("#infoIconZero").clone();
	newIcon.attr("id", targetId + "_tip");
	newIcon.attr("title", $j(targetObj).attr("title"));
	newIcon.tipTip({edgeOffset: 5, fadeOut: 400});
	if(insertAfterObj){
		insertAfterObj.after(newIcon);
	}else{
		if(insertBeforeObj){
			insertBeforeObj.before(newIcon);
		}else{
			$j(targetObj).parent().append(newIcon);
		}
	}
}