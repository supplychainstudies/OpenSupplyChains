/* Live search (modified)
 * Copyright (c) 2008 Andreas Lagerkvist 
 * <http://andreaslagerkvist.com/jquery/live-search/> */

jQuery.fn.liveSearch = function (conf) {
    var config = jQuery.extend({
        url:            '/services/search/', 
        id:             'search-results', 
        duration:       0, 
        typeDelay:      0,
        loadingClass:   'loading', 
        onSlideUp:      function () {}, 
        updatePosition: false
    }, conf);

    var liveSearch  = jQuery('#' + config.id);

    // Create live-search if it doesn't exist
    if (!liveSearch.length) {
        liveSearch = jQuery('<div id="' + config.id + '"></div>')
                        .appendTo(document.body)
                        .hide()
                        .slideUp(0);

    }
    
    // Close live-search when clicking outside it
    jQuery(document.body).click(function(event) {
        var clicked = jQuery(event.target);

        if (!(clicked.is('#' + config.id) || clicked.parents('#' + config.id).length || clicked.is('input'))) {
            liveSearch.slideUp(config.duration, function () {
                config.onSlideUp();
            });
        }
    });

    return this.each(function () {
        var input                           = jQuery(this).attr('autocomplete', 'off');
        var liveSearchPaddingBorderHoriz    = parseInt(liveSearch.css('paddingLeft'), 10) + parseInt(liveSearch.css('paddingRight'), 10) + parseInt(liveSearch.css('borderLeftWidth'), 10) + parseInt(liveSearch.css('borderRightWidth'), 10);

        // Re calculates live search's position
        var repositionLiveSearch = function () {
            var tmpOffset   = input.offset();
            var inputDim    = {
                left:       tmpOffset.left - 1, 
                top:        tmpOffset.top + 1, 
                width:      input.outerWidth(), 
                height:     input.outerHeight()
            };

            inputDim.topPos     = inputDim.top + inputDim.height;
            inputDim.totalWidth = inputDim.width - liveSearchPaddingBorderHoriz;

            liveSearch.css({
                position:   'absolute', 
                left:       inputDim.left + 'px', 
                top:        inputDim.topPos + 'px',
                width:      (inputDim.totalWidth <= 310) ? '310px' : inputDim.totalwidth + 'px'
            });
        };

        // Shows live-search for this input
        var showLiveSearch = function () {
            // Always reposition the live-search every time it is shown
            // in case user has resized browser-window or zoomed in or whatever
            repositionLiveSearch();

            // We need to bind a resize-event every time live search is shown
            // so it resizes based on the correct input element
            $(window).unbind('resize', repositionLiveSearch);
            $(window).bind('resize', repositionLiveSearch);

            liveSearch.slideDown(config.duration);
        };

        // Hides live-search for this input
        var hideLiveSearch = function () {
            liveSearch.slideUp(config.duration, function () {
                config.onSlideUp();
            });
        };

        input
            // On focus, if the live-search is empty, perform an new search
            // If not, just slide it down. Only do this if there's something in the input
            .focus(function () {
                if (this.value !== '') {
                    // Perform a new search if there are no search results
                    if (liveSearch.html() == '') {
                        this.lastValue = '';
                        input.keyup();
                    }
                    // If there are search results show live search
                    else {
                        // HACK: In case search field changes width onfocus
                        setTimeout(showLiveSearch, 1);
                    }
                }
            })
            // Auto update live-search onkeyup
            .keyup(function () {
                // Don't update live-search if it's got the same value as last time
                if (this.value != this.lastValue) {
                    input.addClass(config.loadingClass);

                    var q = this.value;

                    // Stop previous ajax-request
                    if (this.timer) {
                        clearTimeout(this.timer);
                    }

                    // Start a new ajax-request in X ms
                    this.timer = setTimeout(function () {
                        jQuery.getJSON(config.url+'?q='+q, function (json) {
                            input.removeClass(config.loadingClass);

                            // Show live-search if results and search-term aren't empty
                            if (json.results.length && q.length) {
                                
                                // Build output
                                var html = "<ul id=\"live-search-results\">";
                                for (var i = 0; i < json.results.length; i++) { 

                                    // TODO:  turn this into a JSTPL template
                                    html += "<li class=\"search-result\">";
                                    html += "<a class=\"search-link\" href=\"view/"+ json.results[i].id + "\">";
                                    html += "</a>";
                                    html += "<div class=\"search-details\">";
                                    html += "<span class=\"search-title\">";
                                    html += json.results[i].attributes.title || "An Unnamed Sourcemap";
                                    html += "</span>";
                                    html += "<span class=\"search-author\">";
                                    html += " ";
                                    html += json.results[i].owner.name;
                                    html += "</span> ";
                                    html += "<span class=\"search-date\" href=\"\">";
                                    var d = new Date(json.results[i].created*1000);
                                    html += _S.ttrunc(_S.fmt_date(d),14);
                                    html += "</span>";
                                    html += "</div>";
                                    html += "<div class=\"clear\"></div>";
                                    html += "</li>";
                                    
                                    if (i == 2){
                                        html += $('<div></div>').append($('<li></li>').addClass("more").append(
                                                $('<a>More Results...</a>').attr("href", "search?q="+escape(q))
                                            ).append('<div class="clear"></div>')
                                        ).html();
                                        break;
                                    }
 
                                
                                }
                                html += "</ul>";
                                liveSearch.html(html);
                                showLiveSearch();
                            }
                            else {
                                hideLiveSearch();
                            }
                        });
                    }, config.typeDelay);

                    this.lastValue = this.value;
                }
            });
    });
};

// Load the search functionality 
jQuery('#search').liveSearch({url: '/services/search/'});
