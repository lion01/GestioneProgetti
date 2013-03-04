// Sticky top nav

window.onscroll = function()
{
	if( window.XMLHttpRequest ) {
		if (document.documentElement.scrollTop > 132 || self.pageYOffset > 132) {
			$('alt-toolbar').style.position = 'fixed';
			$('alt-toolbar').style.top = '0';
			$('alt-toolbar').style.width = '95%';
			$('alt-toolbar').className = 'sticky-tools';
		} else if (document.documentElement.scrollTop < 132 || self.pageYOffset < 132) {
			$('alt-toolbar').style.position = 'static';
			$('alt-toolbar').style.top = '0';
			$('alt-toolbar').className = '';
		}
	}
}