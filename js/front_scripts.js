jQuery(document).ready(function(){
  jQuery('.bxslider').bxSlider({
	  pager: true,
	  control: false,
	  auto: true,
	  autoHover: true,
	  pause: 5000,
	  controls: false
  });
  jQuery(".cstmaccordiandv").click(function(){
	 if(jQuery(this).children('i').hasClass("fa-caret-right"))
	 {
		jQuery(this).children('i').removeClass("fa-caret-right");
		jQuery(this).children('i').addClass("fa-caret-down");
        jQuery(this).children('a').attr("title",jQuery(this).children('a').attr("title").replace("Expand","Collapse"));;
	 }
	 else if(jQuery(this).children('i').hasClass("fa-caret-down"))
	 {
		jQuery(this).children('i').removeClass("fa-caret-down");
		jQuery(this).children('i').addClass("fa-caret-right");
        jQuery(this).children('a').attr("title",jQuery(this).children('a').attr("title").replace("Collapse","Expand"));;
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


    jQuery('#statedropdown').change( function () {
		var value = jQuery(this).val();
        window.location.href = value;
    });

    jQuery('#showalltopic').change( function () {
        var value = jQuery(this).val();
        window.location.href = value;
    });
    
    /** Remove empty p tags **/
    jQuery('p').filter(function () { return jQuery.trim(this.innerHTML) == "" }).remove();
    
    /** Sort Script **/
    jQuery('.sort-story').click(function(){
      jQuery('.sort-options').fadeToggle('fast');
    });
    
    jQuery('.sort-options ul li a').click(function(){
      jQuery('.sort-options ul li').removeClass('cs-selected');
      jQuery(this).parent().addClass('cs-selected');
      jQuery('.sortoption').text(jQuery(this).text());
      jQuery('.sort-selectbox').val(jQuery(this).parent().attr('data-value'));
    });
});
function formsubmit(ref)
{
	jQuery(ref).parent("form").submit();
}