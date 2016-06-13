jQuery(document).ready(function($){
    $('.btn-load-more').click(function(){
        var data = {
            action: 'load_more',
            post_var: 'test'
        };
        
        $.post(the_ajax_script.ajaxurl, data).done(function(response) {
            console.log(response);
        });
        return false;
    });
});