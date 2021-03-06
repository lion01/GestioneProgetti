window.addEvent('load', function() {
	checkColumns();
	$$('select.columns').addEvent('change', function() {
		checkColumns();
		setColumnParam();
	});
});

function setColumnParam() {
	var widths = new Array();
	$$('div.column').each(function(column){
		widths.push(column.getProperty('id').substring(7)+':'+column.getElement('select').getProperty('value'));
	});

	$('jform[params][columns]').setProperty('value', widths.join(';'));
}

function checkColumns() {
	var widths = new Number(0);
	$$('select.columns').each(function(column){
		widths += parseInt(column.getProperty('value'));
	});
	$('columns_used').set('text', widths);
	if (widths !== 12)
	{
		$('column_info').setStyle('color', 'red');
		$('columns_warning').setStyle('display', 'inline');
	}
	else
	{
		$('column_info').setStyle('color', 'inherit');
		$('columns_warning').setStyle('display', 'none');
	}
	$$('div.column').each(function(column){
		var columWidth = column.getElement('select').getProperty('value')/12*82;
		if (columWidth == 0) columWidth = 6;
		column.setStyle('width', columWidth+'%');
	});
}

function swapColumns(col, dir) {
	var cols = $$('div.column');
	var index = 0;
	var selected = 'column_'+col;
	if (dir == 'right')
	{
		cols.each(function(el) {
			if (el.getProperty('id') == selected)
			{
				swapindex = index + 1;
			}
			index++;
		});
		$(selected).injectAfter(cols[swapindex]);
	}
	else
	{
		cols.each(function(el) {			
			if (el.getProperty('id') == selected)
			{
				swapindex = index - 1;
			}
			index++;
		});
		$(selected).injectBefore(cols[swapindex]);
	}
	checkColumns();
	setColumnParam();
}