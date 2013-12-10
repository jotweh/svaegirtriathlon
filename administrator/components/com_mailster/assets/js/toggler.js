function addToggler(showNow, togglerSelector, contentSelector, showText, hideText, toggleTime){
	
	if(showNow == true){
		$j(contentSelector).show();
		$j(togglerSelector).text(hideText);
	}else{
		$j(contentSelector).hide();
		$j(togglerSelector).text(showText);
	}
	
	$j(togglerSelector).click(function() { // add toggler
		
		if ($j(togglerSelector).text() == showText) { // change the link text
			// show now
			$j(togglerSelector).text(hideText);
		    $j(contentSelector).fadeIn(toggleTime);
		}else {
			// hide now
			$j(togglerSelector).text(showText);
		    $j(contentSelector).fadeOut(toggleTime);
		}
		
		return false; // do not follow link destination
	});
}