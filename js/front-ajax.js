jQuery(document).ready(function($){
    $('.btn-load-more').click(function(){
        var page_num = parseInt($(this).attr('data-page-number'));
        var post_ids = $(this).attr('data-posts');
        var sorting = $(this).attr('data-sort');
        var page = $(this).attr('data-page');
        
        if ($(this).attr('data-sort')) {
            sorting = $(this).attr('data-sort');
        }
        
        var data = {
            action: 'load_more',
            post_var: page_num,
            post_ids:  post_ids,
            sort: sorting,
            page: page
        };
        
        /*$.post(the_ajax_script.ajaxurl, data).done(function(response) {*/
        $.post(sajaxurl, data).done(function(response) {
            var btn_load = $('.btn-load-more');
            var next_page = page_num + 1;
            var base_url = btn_load.attr('data-base-url');
            var max_page = btn_load.attr('data-max-page');
            
            history.pushState({}, '', base_url + $('.btn-load-more').attr("href"));
            $('#content-stories').append(response);
            if (next_page<=max_page) {
                if (post_ids) {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('data-sort', sorting)
                       .attr('href', '?page='  + next_page.toString());
                } else {
                    btn_load
                       .attr('data-page-number',next_page)
                       .attr('data-sort', sorting)
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
        var sort = $(this).val();
        var page_num = parseInt($('.btn-load-more').attr('data-page-number'));
        var post_ids = $('.btn-load-more').attr('data-posts');
        var term = $('.topics-search-box select[name=term]').val();
        var taxonomy = $('.topics-search-box input[name=story_taxonomy]').val();
        
        var data = {
            action: 'sort_stories',
            sort: sort,
            post_var: page_num-1,
            post_ids: post_ids,
            term: term,
            taxonomy: taxonomy
        };
        
        /*$.post(the_ajax_script.ajaxurl, data).done(function(response) {*/
        $.post(sajaxurl, data).done(function(response) {
            $('#content-stories').html('');
            $('#content-stories').html(response);
            
            if ($('.btn-load-more').is(':visible')) {
                var btn_load = $('.btn-load-more');
                
                btn_load.attr('data-sort', sort)
                
            } 
        });
    });
});