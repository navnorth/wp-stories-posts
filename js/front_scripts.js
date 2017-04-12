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
    jQuery('.sortoption').text(jQuery('.sort-options').find('li.cs-selected').text());
    
    jQuery('.sort-story').click(function(){
      jQuery('.sort-options').fadeToggle('fast');
    });
    
    jQuery('.sort-options ul li a').click(function(){
      jQuery('.sort-options ul li').removeClass('cs-selected');
      jQuery(this).parent().addClass('cs-selected');
      jQuery('.sortoption').text(jQuery(this).text());
      jQuery('.sort-selectbox').val(jQuery(this).parent().attr('data-value'));
      jQuery('.sort-options').fadeToggle('fast');
      jQuery('.sort-selectbox').trigger("change");
    });
    moveStoryLabelOnMobile();
    
    jQuery('ul.tabs').each(function(){
      // For each set of tabs, we want to keep track of
      // which tab is active and its associated content
      var $active, $content, $links = jQuery(this).find('a');
      
      // If the location.hash matches one of the links, use that as the active tab.
      // If no match is found, use the first link as the initial active tab.      
      $active = jQuery($links.filter('.active')[0] || $links[0]);
      $active.addClass('active');

      $content = jQuery($active[0].hash);

      // Hide the remaining content
      $links.not($active).each(function () {
	jQuery(this.hash).hide();
      });

      // Bind the click event handler
      jQuery(this).on('click', 'a', function(e){
	// Make the old tab inactive.
	$active.removeClass('active');
	$content.hide();
    
	// Update the variables with the new link and content
	$active = jQuery(this);
	$content = jQuery(this.hash);
    
	// Make the tab active.
	$active.addClass('active');
	$content.show();
    
	// Prevent the anchor's default click action
	e.preventDefault();
      });
  });
});

/** Movelabel in between excerpt and topic **/
function moveStoryLabelOnMobile() {
  if (jQuery(window).width()<=600) {
    jQuery('span.bgblue,span.bgorange').each(function(){
      if (jQuery('span.bgblue,span.bgorange').length>0) {
	var blueLabel = jQuery(this);
	blueLabel.css({"margin-left":"0"});
	blueLabel.parent().parent().find('.story-topics').before(blueLabel);
      }
    });
  }
}
function formsubmit(ref)
{
	jQuery(ref).parent("form").submit();
}