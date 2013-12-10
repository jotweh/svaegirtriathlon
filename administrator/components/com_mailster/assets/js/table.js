function addRow(tableId, rows, rowOffset){
	var tblHtml = '';
	var cols;
	var col;
	for (var i = 0; i < rows.length; i++){
		cols = rows[i];
		tblHtml = tblHtml + '<tr id="' + tableId + '_row' + rowOffset + '">';
		for (var j = 0; j < cols.length; j++){
			col = cols[j];
			tblHtml = tblHtml + '<td>' + col + '</td>';
		}
		tblHtml = tblHtml + '</tr>';
	}
	tableId = '#' + tableId + ' > tbody:last';
	$j(tableId).append(tblHtml);
	
	
}