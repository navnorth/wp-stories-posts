jQuery(document).ready(function(){
  jQuery('.bxslider').bxSlider({'auto': true});

  jQuery(".cstmaccordiandv").click(function(){
	 if(jQuery(this).children('i').hasClass("fa-caret-right"))
	 {
		jQuery(this).children('i').removeClass("fa-caret-right");
		jQuery(this).children('i').addClass("fa-caret-down");
	 }
	 else if(jQuery(this).children('i').hasClass("fa-caret-down"))
	 {
		 jQuery(this).children('i').removeClass("fa-caret-down");
		 jQuery(this).children('i').addClass("fa-caret-right");
	 }
	 jQuery(this).next(".tglelemnt").slideToggle();
  });

  // accessible for keyboard navigation
  jQuery(".cstmaccordiandv").keydown(function(e) {
                    var code = e.which;
                    if ((code === 13) || (code === 32)) {
                        jQuery(this).click();
                    }
  });

});