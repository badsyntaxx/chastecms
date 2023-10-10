var Search = new Object();

/**
 * Search Initializer
 * 
 * This is the primary funciton for this object. It will set 
 * the properties and call all other functions.
 */
Search.init = function() {
    Search.preventEnterKey();
    Search.liveSearch();
}

/**
 * Prevent Enter Key
 * 
 * Prevent enter key presses while focused in the search input.
 * Returns false when user hits Enter.
 */
Search.preventEnterKey = function() {
    $('.search').keypress(function(key) {
        if (key.which == 13) { //Enter key pressed
            return false;
        }
    });
}

/**
 * Live Search
 * 
 * This function is the meat and potatoes of the live search.
 * On keyup this function will do an AJAX call to the search 
 * controller and the result will be displayed in a div below 
 * the search box.
 */
Search.liveSearch = function() {
    $('.search').on('keyup', '#search-string', function() {

        if ($('#search-string').val()) {
            $('.search-results').css({'display':'block'});
        } else {
            $('.search-results').css({'display':'none'});
        }

        if ($('#search-string').val().length > 1) {
            
            $.ajax({
                url: '/admin/search/liveSearch',
                type: 'POST',
                data: $('#search-string').serialize(),
                beforeSend: function() {
                    $('.search-results').append(Common.showLoader());
                },
                success: function(response, status, xhr) {
                    if ($.trim(response)) {
                        $('.loading').remove();
                        $('.search-results li').remove('');
                        var data = JSON.parse(response);
                        var users = data.users;
                        var posts = data.posts;
                        var pages = data.pages;

                        if (users.length) {
                            $('.search-results-users em').text('Users:');
                            $.each(users, function(index, item) {
                                $('.search-results-users').append('<li><a href="/admin/users/' + item.username + '">' + item.username + '</a></li>');
                            });
                        } else {
                            $('.search-results-users li').remove('');
                            $('.search-results-users em').text('');
                        }

                        if (posts.length) {
                            $('.search-results-blog em').text('Blog:');
                            $.each(posts, function(index, item) {
                                $('.search-results-blog').append('<li><a href="/admin/blog/' + item.id + '/edit">' + item.title + '</a></li>');
                            });
                        } else {
                            $('.search-results-blog li').remove('');
                            $('.search-results-blog em').text('');
                        }

                        if (pages.length) {
                            $('.search-results-pages em').text('Pages:');
                            $.each(pages, function(index, item) {
                                $('.search-results-pages').append('<li><a href="/admin/pages/' + item.name + '/edit">' + item.name + '</a></li>');
                            });
                        } else {
                            $('.search-results-pages li').remove('');
                            $('.search-results-pages em').text('');
                        }
                    }
                }
            });
        }
    });
}