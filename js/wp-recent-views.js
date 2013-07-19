function RegisterRecentViewsCookie()
{
    var ids = '';

    var cookieName = 'recent-views=';
    var allcookies = document.cookie;

    var position = allcookies.indexOf( cookieName );
    if( position != -1 )
    {
        var startIndex = position + cookieName.length;

        var endIndex = allcookies.indexOf( ';', startIndex );
        if( endIndex == -1 )
        {
            endIndex = allcookies.length;
        }
        ids = decodeURIComponent( allcookies.substring( startIndex, endIndex ) );
    }

	if ( ids.length > 0 ) {
		ids = ids.split(/\s*,\s*/);
	} else {
		ids = new Array();
	}

	for (var i = 0; i < ids.length; i++) {
		if ( ids[i] == viewPost.id ) {
			ids.splice( i,1 );
		}
	}

	ids.unshift(viewPost.id);
	ids = ids.slice(0,viewPost.generations);
	ids = ids.join(',');
	var setData = 'recent-views=' + ids + '; path=' + viewPost.path + '; max-age=' + viewPost.maxAge;
	document.cookie = setData;
	
}
RegisterRecentViewsCookie();