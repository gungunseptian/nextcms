$(function(){

	//Hide SubLevel Menus
	$('#nav ul li ul').hide();

	//OnHover Show SubLevel Menus
	$('#nav ul li').hover(
		//OnHover
		function(){
			//Hide Other Menus
			$('#nav ul li').not($('ul', this)).stop();

			//Add the Arrow
			$('ul li:first-child', this).before(
				'<li class="arrow">arrow</li>'
			);

			//Remove the Border
			$('ul li.arrow', this).css('border-bottom', '0');

			// Show Hoved Menu
			$('ul', this).slideDown();
		},
		//OnOut
		function(){
			// Hide Other Menus
			$('ul', this).slideUp();

			//Remove the Arrow
			$('ul li.arrow', this).remove();
		}
	);

});