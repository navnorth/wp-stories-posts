jQuery(document).ready(function($){
    $('.btn-load-more').click(function(){
        var page_num = parseInt($(this).attr('data-page-number'));
        var data = {
            action: 'load_more',
            post_var: page_num
        };
        
        $.post(the_ajax_script.ajaxurl, data).done(function(response) {
            var btn_load = $('.btn-load-more');
            var next_page = page_num + 1;
            var base_url = btn_load.attr('data-base-url');
            var max_page = btn_load.attr('data-max-page');
            
            history.pushState({}, '', base_url + $('.btn-load-more').attr("href"));
            btn_load.parent().before(response);
            if (next_page<=max_page) {
                 btn_load
                    .attr('data-page-number',next_page)
                    .attr('href', '&paged='  + next_page.toString());
            }else {
                btn_load.addClass('btn-hidden');
            }
        });
        return false;
    });
});