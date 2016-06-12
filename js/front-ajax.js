jQuery(document).ready(function($){
    $('btn-load-more').click(function(){
        var data = {};
        
        $.post(the_ajax_script.ajaxurl, data, function(response) {
            alert(response);
        });
        return false;
    });
});