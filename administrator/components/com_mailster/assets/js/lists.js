function initPairList(preCode){
				preCode = preCode + '_';
				var moveRightId = preCode + 'MoveRight';
				var moveLeftId = preCode + 'MoveLeft';
				var moveAllRightId = preCode + 'MoveAllRight';
				var moveAllLeftId = preCode + 'MoveAllLeft';
				var selectLeftId = preCode + 'selectLeft';
				var selectRightId = preCode + 'selectRight';
				var submitButtonId = preCode + 'selListPairSubmitButton';
				var selectAllRightId = preCode + 'rightSelectAll';
				var selectAllLeftId = preCode + 'leftSelectAll';
				var selectNoneRightId = preCode + 'rightSelectNone';
				var selectNoneLeftId = preCode + 'leftSelectNone';
				var selectInvRightId = preCode + 'rightSelectInv';
				var selectInvLeftId = preCode + 'leftSelectInv';
				
				var singleSelectButtons = '#' + moveRightId+',#' + moveLeftId;
				var multiSelectButtons = '#' + moveAllRightId+',#' + moveAllLeftId;
				var selectAllButtons = '#' + selectAllRightId+',#' + selectAllLeftId;
				var selectNoneButtons = '#' + selectNoneRightId+',#' + selectNoneLeftId;
				var selectInvButtons = '#' + selectInvRightId+',#' + selectInvLeftId;
				
				$j(singleSelectButtons).click(function(event) {
			
					var id = $j(event.target).attr("id");
					var selectFrom = id == moveRightId ? ("#"+selectLeftId) : ("#"+selectRightId);
					var moveTo = id == moveRightId ? ("#"+selectRightId) : ("#"+selectLeftId);
				
					var selectedItems = $j(selectFrom + " :selected").toArray();
					$j(moveTo).append(selectedItems);
					selectedItems.remove;
				});
				$j(multiSelectButtons).click(function(event) {
					var id = $j(event.target).attr("id");
					var selectFrom = id == moveAllRightId ? ("#"+selectLeftId) : ("#"+selectRightId);
					var moveTo = id == moveAllRightId ? ("#"+selectRightId) : ("#"+selectLeftId);
					var maxTargetContent = document.getElementById(selectFrom);
					$j(selectFrom + " option").attr("selected","selected");				
					var selectedItems = $j(selectFrom + " :selected").toArray();
					$j(moveTo).append(selectedItems);
					selectedItems.remove;
				});
				$j(selectAllButtons).click(function(event) {
					var id = $j(event.target).attr("id");
					var selectAllTarget = id == selectAllRightId ? ("#"+selectRightId) : ("#"+selectLeftId);
					$j(selectAllTarget + " option").attr("selected","selected");				
				});
				$j(selectNoneButtons).click(function(event) {
					var id = $j(event.target).attr("id");
					var selectNoneTarget = id == selectNoneRightId ? ("#"+selectRightId) : ("#"+selectLeftId);
					$j(selectNoneTarget + " option").removeAttr("selected");				
				});
				$j(selectInvButtons).click(function(event) {
					var id = $j(event.target).attr("id");
					var selectInvTarget = id == selectInvRightId ? ("#"+selectRightId) : ("#"+selectLeftId);
					$j(selectInvTarget + " option").each(function(){	
						var isSelected = $j(this).attr("selected");
						if(isSelected != false)
						{
							$j(this).removeAttr("selected"); 
						}else{
							$j(this).attr("selected", "selected");
						}
					});					
				});
				$j('#' + submitButtonId).click(function(event)
				{					
					$j('#' + selectRightId).each(function(){ $j('#' + selectRightId + " option").attr("selected","selected"); }  );
				});
}