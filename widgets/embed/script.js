(function() {
// Localize jQuery variable
var jQuery;

//Keep track of embed scripts if multiple
var foundScripts = [];

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
    var foundScripts = [];
    /*** Get Script path ***/
    var scripts = document.getElementsByTagName( 'script' );
    var scriptPath = '';
    
    if(scripts && scripts.length>0) {
        for(var i in scripts) {
            if(scripts[i].src && scripts[i].src.match(/\/script\.js$/)) {
                scriptPath = scripts[i].src.replace(/(.*)\/script\.js$/, '$1');
                foundScripts.push(scripts[i]);
                break;
            }
        }
    }
    
    jQuery(document).ready(function($) {
        /******* Load CSS *******/
        var css_link = $("<link>", { 
            rel: "stylesheet", 
            type: "text/css", 
            href: scriptPath + "/../../css/story.embed.css" 
        });
        css_link.appendTo('head');
        
        /******* Load HTML *******/
        var jsonp_url = scriptPath + "/embed.php?callback=?";
        
        if (typeof window.foundScripts==="undefined") {
            
            /** Loop through the oet-embed-story class **/
            $('.oet-embed-story').each(function(){
                
                var story_id = $(this).attr('data-story-id');
                var story_width = $(this).attr('data-story-width');
                var story_height = $(this).attr('data-story-height');
                
                /** Get Story display **/
                $.getJSON(jsonp_url, { id: story_id }, function(data) {
                  $('.oet-embed-story[data-story-id="' + data.id + '"]').html(data.html);
                });
                
            });
            
            window.foundScripts = foundScripts;
        }
    });
}
})();