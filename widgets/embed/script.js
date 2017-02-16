(function() {
// Localize jQuery variable
var jQuery;
/******** Load jQuery if not present *********/
if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.4.2') {
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type","text/javascript");
    script_tag.setAttribute("src",
        "//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
    if (script_tag.readyState) {
      script_tag.onreadystatechange = function () { // For old versions of IE
          if (this.readyState == 'complete' || this.readyState == 'loaded') {
              scriptLoadHandler();
          }
      };
    } else { // Other browsers
      script_tag.onload = scriptLoadHandler;
    }
    // Try to find the head, otherwise default to the documentElement
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
} else {
    // The jQuery version on the window is the one we want to use
    jQuery = window.jQuery;
    main();
}
/******** Called once jQuery has loaded ******/
function scriptLoadHandler() {
    // Restore $ and window.jQuery to their previous values and store the
    // new jQuery in our local jQuery variable
    jQuery = window.jQuery.noConflict(true);
    // Call our main function
    main(); 
}
/******** Our main function ********/
function main() {
    /*** Get Script path ***/
    var scriptSrcs = document.getElementsByTagName( 'script' );
    var thisScriptEl = scriptSrcs[scriptSrcs.length - 1];
    var scriptPath = thisScriptEl.src;
    var scriptFolder = scriptPath.substr(0, scriptPath.lastIndexOf( '/' )+1 );
    
    jQuery(document).ready(function($) {
        /******* Load CSS *******/
        var css_link = $("<link>", { 
            rel: "stylesheet", 
            type: "text/css", 
            href: scriptFolder + "../../css/story.embed.css" 
        });
        css_link.appendTo('head');
        
        /******* Load HTML *******/
        var jsonp_url = scriptFolder + "embed.php?callback=?";
        var story_id = $('.oet-embed-story').attr('data-story-id');
        var story_width = $('.oet-embed-story').attr('data-story-width');
        var story_height = $('.oet-embed-story').attr('data-story-height');
        
        $.getJSON(jsonp_url, { id: story_id }, function(data) {
          $('.oet-embed-story').html(data.html);
        });
    });
}
})();