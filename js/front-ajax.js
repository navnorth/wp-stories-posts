jQuery(document).ready(function($){
    $('.btn-load-more').click(function(){
        var page_num = parseInt($(this).attr('data-page-number'));
        var post_ids = $(this).attr('data-posts');
        
        var data = {
            action: 'load_more',
            post_var: page_num,
            post_ids:  post_ids
        };
        
        $.post(the_ajax_script.ajaxurl, data).done(function(response) {
            var btn_load = $('.btn-load-more');
            var next_page = page_num + 1;
            var base_url = btn_load.attr('data-base-url');
            var max_page = btn_load.attr('data-max-page');
            
            history.pushState({}, '', base_url + $('.btn-load-more').attr("href"));
            btn_load.parent().before(response);
            if (next_page<=max_page) {
                if (post_ids) {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('href', '?page='  + next_page.toString());
                } else {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('href', '&page='  + next_page.toString());
                }
            }else {
                btn_load.addClass('btn-hidden');
            }
        });
        return false;
    });
    
    /** Sorting of List of Stories Widget **/
    $('.sort-selectbox').change(function(){
        var data = {
            action: 'sort_stories',
            sort: $(this).val()
        };
        
        $.post(the_ajax_script.ajaxurl, data).done(function(response) {
            $('#content-stories').html();
            $('#content-stories').html(response);
            
            var btn_load = $('.btn-load-more');
            var next_page = page_num + 1;
            var base_url = btn_load.attr('data-base-url');
            var max_page = btn_load.attr('data-max-page');
            
            history.pushState({}, '', base_url + $('.btn-load-more').attr("href"));
            btn_load.parent().before(response);
            if (next_page<=max_page) {
                if (post_ids) {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('href', '?page='  + next_page.toString());
                } else {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('href', '&page='  + next_page.toString());
                }
            }else {
                btn_load.addClass('btn-hidden');
            }
        });
    });
});