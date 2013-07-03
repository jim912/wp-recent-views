jQuery(document).ready(function($){
    $.post(RecentViews.endpoint, {
        action: RecentViews.action,
		limit: RecentViews.limit
    }, function(response){
		$('#'+RecentViews.id).append(response);
    });
});