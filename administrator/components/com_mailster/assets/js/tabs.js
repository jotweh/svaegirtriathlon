function prepareTabs(){
	
	var tabContainers = $j('div.tabs > div.tabDiv');
	tabContainers.hide().filter(':first').show();
	
	$j('div.tabs ul.tabNavigation a').click(function () {
		tabContainers.hide();
		tabContainers.filter(this.hash).show();
		$j('div.tabs ul.tabNavigation a').removeClass('selected');
		$j(this).addClass('selected');
		$j('#tab').val(this.hash);
		return false;
	}).filter(':first').click();
	var query = location.href.split('#');
	var anchor = query[1];
	forceTab(anchor);
	
}

function forceTab(anchor)
{
	var tabContainers = $j('div.tabs > div.tabDiv');
	if(anchor != null && anchor != '')
	{
		var anchorMod = '\"#' + anchor + '\"';
		tabContainers.hide();
		tabContainers.filter(anchorMod).show();
		$j('#tab').val('#' + anchor);
		$j('div.tabs ul.tabNavigation a').removeClass('selected');
		$j( '#' + anchor ).addClass('selected');
	}
}