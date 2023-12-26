jQuery(document).ready(function(){
  var slider = jQuery('.bxslider').bxSlider({
	  pager: true,
	  control: false,
	  auto: true,
	  autoHover: true,
	  pause: 5000,
	  controls: false,
    touchEnabled: false,
    keyboardEnabled: true,
    onSliderLoad: function(currentIndex) {
      jQuery('.bxslider>li').eq(1).addClass('active-slide')
      jQuery('.bxslider>li').attr('tabindex','-1');
      jQuery('.bxslider>li a').attr('tabindex','-1');
      jQuery('.bx-viewport').attr('tabindex','0');
    },
    onSlideBefore: function(slideElement, oldIndex, newIndex){
      jQuery('.bxslider>li').eq(oldIndex+1).removeAttr('class').attr('tabindex','-1');
      jQuery('.bxslider>li').eq(oldIndex+1).find('a').attr('tabindex','-1');
      jQuery('.bxslider>li').eq(newIndex+1).addClass('active-slide').attr('tabindex','0');
      jQuery('.bxslider>li').eq(newIndex+1).find('a').attr('tabindex','0');
    }
  });
  jQuery(document).on('focus','.bx-viewport', function(){
        jQuery('.slidersubwrpr').addClass('focused');
  });
  jQuery(document).on('blur','.bx-viewport', function(){
        jQuery('.slidersubwrpr').removeClass('focused');
  });
  jQuery(document).on('focus','.bx-viewport, .bxslider li a',function(){
    jQuery(this).closest(".bx-viewport").trigger("mouseenter");
    slider.stopAuto();
  });
  jQuery(document).on('focusout','.bx-viewport, .bxslider li a',function(){
    jQuery(this).closest(".bx-viewport").trigger("mouseleave");
    slider.startAuto();
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


    jQuery('#statedropdown,#statedropdown2,#statedropdown3').change( function () {
	var value = jQuery(this).val();
	var id = jQuery(this).attr('id');
	var post_ids;
	var tab = "";
	var postids = "";
	var anchor = "";
	if (id=="statedropdown2") {
	  anchor = "#p12";
	  tab = "p12";
	  post_ids= jQuery(this).attr('data-post-ids');
	} else if (id=="statedropdown3"){
	  anchor = "#higheradulted";
    tab = "higheradulted";
	  post_ids= jQuery(this).attr('data-post-ids');
	} else {
	  tab = "all";
	}
	if (post_ids!=="undefined") {
	  postids = '<input type="text" name="post_ids" value="' + post_ids + '" />';
	}
        
	if (id!=="statedropdown") {
	  var form = jQuery('<form action="' + value + '" method="post">' +
	  '<input type="text" name="active_tab" value="' + tab + '" />' +
	  postids +
	  '</form>');
	  jQuery('body').append(form);
	  form.submit();
	} else {
	  var form = jQuery('<form action="' + value + '" method="post">' +
	  '<input type="text" name="active_tab" value="' + tab + '" />' +
	  '</form>');
	  jQuery('body').append(form);
	  form.submit();
	}
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
      
      var active = jQuery('#active_level').val();
      // If the location.hash matches one of the links, use that as the active tab.
      // If no match is found, use the first link as the initial active tab.      
      $active = jQuery($links.filter('[href="#'+active+'"]')[0] || $links.filter('.active')[0] || $links[0]);
      $active.parent().addClass('active');
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
	$active.parent().removeClass('active');
	$content.hide();
    
	// Update the variables with the new link and content
	$active = jQuery(this);
	$content = jQuery(this.hash);
    
	// Make the tab active.
	$active.addClass('active');
	$active.parent().addClass('active');
	$content.show();
    
	// Prevent the anchor's default click action
	e.preventDefault();
      });
  });
  
  var hst = jQuery('#stry-video-overlay').attr('hst');
  jQuery(document).on('click','a.stry-video-link', function(e){
    e.preventDefault ? e.preventDefault() : e.returnValue = false;
    togglemodal(hst,1);
  })
  jQuery(document).on('click','#stry-video-overlay', function(e){
    e.preventDefault ? e.preventDefault() : e.returnValue = false;
    togglemodal(hst,0);
  })
  /* Close Modal */
  jQuery(document).on('click','.stry-video-close', function(e){
    e.preventDefault ? e.preventDefault() : e.returnValue = false;
    togglemodal(hst,0);
  })
  /* Close Modal on escape, enter and space key press when focus is on modal close button */
  jQuery(document).on('keydown','.stry-video-close', function(e){
    if (e.key == "Escape" || e.key == "Esc" || e.key == "Enter" || e.keyCode == 13 || e.keyCode == 32 ) { 
      jQuery('.stry-video-close').trigger("click");
    }
  })
  jQuery(document).on("keydown", function(e) {
   if (e.key == "Escape" || e.key == "Esc") { 
     // escape key maps to keycode `27`
     togglemodal(hst,0);
    }
  });
  //window.setInterval(checkFocus, 1000); 
});

function togglemodal(hst, bol){
  if(bol){ //show and play
    if(hst == 1){
      player.playVideo();
    }else{
      vimplay.play();
    }
    jQuery('#stry-video-overlay').modal('show');
  }else{ //pause and hide
    if(hst == 1){
      player.pauseVideo();
    }else{
      vimplay.pause();
    }
    jQuery('#stry-video-overlay').modal('hide');
  }
}

/** Check if youtube iFrame has stolen the focus **/
function checkFocus() {
    if(document.activeElement.tagName == "IFRAME") {
        document.getElementById("stry-video-overlay").focus(); //return focus to overlay
        document.body.focus();
    }
}

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