/* Copyright (C) Sourcemap 2011
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.*/

Sourcemap.MagicWords = {};
Sourcemap.MagicWords.sequence = ["description", "youtube:link", "vimeo:link", "soundcloud:id", "twitter:search", "flickr:setid"];

// callbacks for magic attributes

Sourcemap.MagicWords.content = {
    "youtube": {
        "link": function(lnk) {
            if(!lnk || !lnk.match(/((\?v=)|(v\/))(.+)$/))
                return '<p class="error">Invalid YouTube link.</p>';
            var m = lnk.match(/((\?v=)|(v\/))([^\/\&]+)/);
            if(m) {
                var mkup = '<iframe width="338" height="220" class="youtube-link" type="text/html"'+
                    'src="http://www.youtube.com/embed/'+m[4]+'?autoplay=1"'+ 
                    'frameborder="0" allowfullscreen></iframe>';
            } else mkup = '';
            return mkup;
        },
        "mobilelink": function(lnk) {
            if(!lnk || !lnk.match(/((\?v=)|(v\/))(.+)$/))
                return '';
            var m = lnk.match(/((\?v=)|(v\/))([^\/\&]+)/);
            if(m) {
                var mkup = '<iframe width="100%" class="youtube-link" type="text/html"'+
                    'src="http://www.youtube.com/embed/'+m[4]+'"'+ 
                    'frameborder="0" allowfullscreen></iframe>';
            } else mkup = '';
            return mkup;
        }
    },
    "vimeo": {
        "link": function(lnk) {
            var mkup = '<iframe class="vimeo-link" src="http://player.vimeo.com/video/'+
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
                "api_key": Sourcemap.MagicWords.content.flickr.api_key, "photoset_id": setid
            }, $.proxy(function(data) {
                if(data && data.photoset && data.photoset.photo && data.photoset.photo.length) {
    				var newmkup = "";
    				for(var p in data.photoset.photo) {
    					var photo = data.photoset.photo[p];
    					newmkup += '<div class="flickr-slideshow-item"><h4>'+photo.title+'</h4>';
    					newmkup += '<img src="http://farm'+photo.farm+'.static.flickr.com/'+photo.server+'/'+photo.id+'_'+photo.secret+'.jpg" />';
    					newmkup += '</div>';
    				}
                    var mkup = '<object  class="flickr-setid" width="500" height="360"> <param name="flashvars" value="offsite=true&lang=en-us&page_show_url=%2Fphotos%2F'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2Fshow%2F&page_show_back_url=%2Fphotos%2F&text=true'+
                        data.photoset.owner+'%2Fsets%2F'+setid+'%2F&set_id='+setid+'&jump_to="></param> '+
                        '<param name="movie" value="http://www.flickr.com/apps/slideshow/show.swf?v=71649"></param> '+
                        '<param name="allowFullScreen" value="true"></param><embed class="flickr-setid" type="application/x-shockwave-flash"'+
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
            return '<div class="flickr-setid" id="flickr-photoset-'+setid+'"></div>';
        }
    },
    "twitter": {
        "search": function(term) {
            var mkup = '<div id="twtr-widget"></div><script>new TWTR.Widget({'+
			  'version: 2, id: "twtr-widget", type: "search", search: "'+term+'", interval: 30000, title: "Twitter", subject: "'+term+'", width: "auto", height: 240,'+
			  'theme: { shell: { background: "#C8C8C8", color: "#ffffff" }, tweets: { background: "#C8C8C8", color: "#444444", } },'+
			  'features: { scrollbar: true, loop: true, live: false, hashtags: true, timestamp: true, avatars: true, toptweets: true, behavior: "default" } }).render().start();</script>';
            return mkup;
        }
    },
    "soundcloud": {
        "id": function(id) {
			id = "25545600";
            var mkup = '<object class="soundcloud-id" height="81" width="100%"> <param name="movie" value="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'+id+'"></param> <param name="allowscriptaccess" value="always"></param> <embed allowscriptaccess="always" class="soundcloud-id" height="81" src="http://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'+id+'" type="application/x-shockwave-flash" width="100%"></embed> </object>';
            return mkup;
        }
    }
}    


