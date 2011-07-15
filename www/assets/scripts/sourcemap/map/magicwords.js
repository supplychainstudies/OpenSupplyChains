Sourcemap.MagicWords = {};
Sourcemap.MagicWords.sequence = ["description", "youtube:link", "vimeo:link", "flickr:setid"];

// callbacks for magic attributes

Sourcemap.MagicWords.popup_content = {
    "youtube": {
        "link": function(lnk) {
            if(!lnk || !lnk.match(/((\?v=)|(v\/))(.+)$/))
                return '<p class="error">Invalid YouTube link.</p>';
            var m = lnk.match(/((\?v=)|(v\/))([^\/\&]+)/);
            if(m) {
                var mkup = '<iframe width="500" height="360" class="youtube-player" type="text/html"'+
                    'src="http://www.youtube.com/embed/'+m[4]+'?autoplay=1"'+ 
                    'frameborder="0" allowfullscreen></iframe>';
            } else mkup = '';
            return mkup;
        }
    },
    "vimeo": {
        "link": function(lnk) {
            var mkup = '<iframe class="vimeo-player" src="http://player.vimeo.com/video/'+
                (lnk.match(/\/(\d+)$/))[1]+'?title=0&amp;byline=0&amp;portrait=0&autoplay=1" '+
                'frameborder="0"></iframe>';
            return mkup;
        }
    },
    "flickr": {
        "api_key": "06ea60fff75fc5721cfd11d823634ab8",
        "setid": function(setid, elid) {
            var url = "http://www.flickr.com/services/rest/?jsoncallback=?";
            $.getJSON(url, {
                "method": "flickr.photosets.getPhotos", "format": "json",
                "api_key": Sourcemap.MagicWords.popup_content.flickr.api_key, "photoset_id": setid
            }, $.proxy(function(data) {
                if(data && data.photoset && data.photoset.photo && data.photoset.photo.length) {
                    var mkup = '<object width="500" height="360"> <param name="flashvars" value="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></param> '+
                        '<param name="movie" value="http://www.flickr.com/apps/slideshow/show.swf?v=71649"></param> '+
                        '<param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash"'+
                        'src="http://www.flickr.com/apps/slideshow/show.swf?v=71649" allowFullScreen="true" '+
                        'flashvars="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+data.photoset.owner+
                        '%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F'+data.photoset.owner+
                        '%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></embed></object>';
                } else {
                    var mkup = 'Photo set not found.';
                }
                $('#flickr-photoset-'+setid).replaceWith(mkup);
                $(window).resize();                
                
                return;
            }, this));
            return '<div class="flickr-slideshow-wrapper" id="flickr-photoset-'+setid+'"></div>';
        }
    }
};

