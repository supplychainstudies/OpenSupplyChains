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
 * program. If not, see <http://www.gnu.org/licenses/>.
 */


$(document).ready(function(){

    /* truncation */
    Sourcemap.truncate_string(".truncate");

    /* collapse/expand */
    $('a.expander').click(function(){
        $(this).parent().next('.collapsed').slideToggle();
    });
    
    /* profiel pictures*/
    $('a#change_profile_pic,a#change_banner').click(function(){
        //modal a profile upload window
        var popID = 'popup';
        var element = document.createElement('div');
        var banner_html = document.createElement('div');
        var edit_id = $(this).attr("id");
        var element_class,element_caption,form_id,upload_bucket;
        switch(edit_id){
            case "change_profile_pic":
                element_class = "upload_profile_pic";
                element_caption = "Profile picture";
                form_id = "profile_pic_upload";
                upload_bucket = "accountpics";
            break;
            case "change_banner":
                element_class = "upload_banner";
                element_caption = "Banner";
                form_id = "banner_upload";
                upload_bucket = "bannerpics";
            break;
        }

        $(element).html(
            '<div id="upload_content">'+
            '<div class="'+element_class+'">'+
            '<h3>'+element_caption+'</h3>'+
            '<form id="'+form_id+'" method="post" action="/services/uploads" enctype="multipart/form-data">'+
            '<input type="hidden" name="bucket" value="'+upload_bucket+'"/>'+
            '<input type="hidden" name="filename" value="'+username+'"/>'+
            '<input type="file" name="file" />'+
            '<input type="submit" value="Upload" />'+
            '</form></div></div>'
        );
        /*
        $(element).html(
            '<div id="upload_content">'+
            '<div class="upload_profile_pic">'+
            '<h3>Profile picture</h3>'+
            '<form id="profile_pic_upload" method="post" action="/services/uploads" enctype="multipart/form-data">'+
            '<input type="hidden" name="bucket" value="accountpics"/>'+
            '<input type="hidden" name="filename" value="'+username+'"/>'+
            '<input type="file" name="file" />'+
            '<input type="submit" value="Upload" />'+
            '</form></div><hr/></div>'
        );
        $(banner_html).html(
            '<h3>Banner</h3>'+
            '<form id="banner_upload" method="post" action="/services/uploads" enctype="multipart/form-data">'+
            '<input type="hidden" name="bucket" value="bannerpics"/>'+
            '<input type="hidden" name="filename" value="'+username+'"/>'+
            '<input type="file" name="file" />'+
            '<input type="submit" value="Upload" />'+
            '</form>'
        );
        $(banner_html).attr('class','upload_banner');
        is_channel ? $(element).find("#upload_content").append(banner_html) : 0 ;
        */

        $(element).attr('id',popID);
        $(element).addClass("popup_block");
        $(element).prepend('<a href="#" class="close"></a>');
        $('body').append($(element));

        //is_channel ? $('#' + popID).height(200) : $('#' + popID).height(100);
        $('#' + popID).height(100);
        $('#' + popID).width(500);
        var popMargTop = ($('#' + popID).height() + 80) / 2;
        var popMargLeft = ($('#' + popID).width() + 80) / 2;

        $('#' + popID).css({
            'margin-top' : -popMargTop,
            'margin-left' : -popMargLeft,
            'overflow' : 'hidden'
        });
        $('#' + popID).fadeIn();

        $('body').append('<div id="fade"></div>'); //Add the fade layer to botto
        $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the
        $('a.close, #fade').live('click', function() { //When clicking on the cl
            $('#fade , .popup_block').fadeOut(function() {
                $('#fade, a.close').remove();
                $('#'+popID).remove();
            }); //fade them both out
            return false;
        });
        $('form#profile_pic_upload,form#banner_upload').submit(function(evt){
            var fileInput = $(this).children('input:file');
            if(fileInput[0].files[0]==undefined){
                evt.preventDefault();
                console.log("No file input.")
                return;    
            }
            var Uploadfilesize = fileInput[0].files[0].fileSize;
            var upload_id = $(this).attr("id");
            var sizelimit;
            if(upload_id=="profile_pic_upload")
                sizelimit = 500000;
            if(upload_id=="banner_upload")
                sizelimit = 1000000;

            // if filesize > 500kb
            if(Uploadfilesize > sizelimit){
                evt.preventDefault(); //prevent upload action
                console.log("file size "+Uploadfilesize+" exceed limit "+sizelimit);
            }
        });
});

/* Channel edit */
$("input:checkbox").each(function(index){
var ischecked = $(this).is(":checked");
$(this).parent().children("a").attr("style","color:"+{color:ischecked?"#777A7E":"#A7AAAE"}.color);

$(this).change(function(){
            var ischecked = $(this).is(":checked");
            $(this).parent().children("a").attr("style","color:"+{color:ischecked?"#777A7E":"#A7AAAE"}.color);
        });
    });

    $('.map-controls').each(function(index){

        var publish_precheck = false;
        var featured_precheck = false;
        var passcode_precheck = false;
        
        // default view
        var map_passcode = $(this).children('.map-controls-passcode');
        var ischecked = $(map_passcode).children('#map-passcode-checkbox').is(":checked");
        $(map_passcode).stop().animate({width:ischecked?249:120},500);     
        //$(parent_control).children("#map-passcode-link").attr("style","color:"+{color:ischecked?"#777A7E":"#A7AAAE"}.color);
        ischecked ? $(map_passcode).children("#map-passcode-input").show():$(map_passcode).children("#map-passcode-input").hide();

        $(this).children('.map-controls-publish').children('#map-publish-checkbox').change(function(){
            publish_precheck = true;
        });

        $(this).children('.map-controls-featured').children('#map-featured-checkbox').change(function(){
            featured_precheck = true;
        });

        $(this).children('.map-controls-passcode').children("#map-passcode-checkbox").change(function(){
            passcode_precheck = true;
            //passcode_precheck = $(this).is(":checked");
            // passcode_check : featured_check ? cancel featured : don't send data            
            // !passcode_check :
            /*
            if(!passcode_precheck){
                // Delete #map-passcode-input value
                if($(this).parent().children("#map-passcode-input").val()=="")
                    passcode_precheck = true;
                else
                    $(this).parent().children("#map-passcode-input").val("");
            }
            */
            // animation
            var ischecked = $(this).is(":checked");
            var parent_control = $(this).parent(".map-controls-passcode");
            $(parent_control).stop().animate({width:ischecked?249:120},500);            
            //$(parent_control).children("#map-passcode-link").attr("style","color:"+{color:ischecked?"#777A7E":"#A7AAAE"}.color);
            ischecked ? $(parent_control).children("#map-passcode-input").show():$(parent_control).children("#map-passcode-input").hide();
        });


        
        $(this).change(function(){

            // publish
            var publish_selector = $(this).children('.map-controls-publish"');
            var publish_check = publish_selector.children("input:checkbox").is(":checked");

            // featured
            var featured_selector = $(this).children('.map-controls-featured');
            var featured_check = featured_selector.children("input:checkbox").is(":checked");
            
            // passcode 
            var passcode_selector = $(this).children('.map-controls-passcode');
            var passcode_check = passcode_selector.children("input:checkbox").is(":checked");
            var passcode_val = passcode_selector.children("#map-passcode-input").val();
            //console.log($(passcode_selector).children("input:checkbox").is(":checked"));
            //console.log(passcode_selector.children("#map-passcode-input").val());

            // precheck
            // publish_check : featured_check ? cancel featured : (click featured before publish); 
            if(publish_precheck){            
                publish_precheck = false; // set to default

                if(!publish_check){                    
                    featured_selector.children("input:checkbox").removeAttr("checked");
                    featured_check = false;                        
                    featured_selector.children("a").attr("style","color:#A7AAAE");
                }
            }
            
            if(featured_precheck){                
                featured_precheck = false; // set to default;    

                if(!publish_check){                    
                    featured_selector.children("input:checkbox").removeAttr("checked");
                    featured_check = false;                        
                    featured_selector.children("a").attr("style","color:#A7AAAE");
                    // cause recursive
                    // featured_selector.children("input:checkbox").trigger("change");
                    return false;
                }
                if(passcode_check){
                    // uncheck passcode and reset                    
                    passcode_selector.children("input:checkbox").removeAttr("checked");
                    passcode_check = false;
                    passcode_selector.children("#map-passcode-input").val("");
                    passcode_val = "";                        
                    passcode_selector.children("input:checkbox").trigger("change");
                }
            }

            // passcode_check : featured_check ? cancel featured : don't send data
            if(passcode_precheck){
                passcode_precheck = false;   // set to default 

                if(passcode_check){
                    if(featured_check){
                        featured_selector.children("input:checkbox").removeAttr("checked"); 
                        featured_check = false;                        
                        featured_selector.children("a").attr("style","color:#A7AAAE");
                    }else{
                        return false;
                    }
                }else {
                    if(passcode_val=="")
                        return false;
                    else{
                        // reset passcode
                        passcode_selector.children("#map-passcode-input").val("");
                        passcode_val = "";                        
                        // uncheck featured
                    }
                }
            }
            

            // supplychain id
            var supplychain_id = $(this).attr('value');
            //http://192.168.1.18/edit/general/5?publish=yes&featured=yes&passcode=aaa 
            $.ajax({
                url:'edit/general/'+supplychain_id,
                data:{  
                    publish: publish_check?"yes":"no",
                    featured: featured_check?"yes":"no",
                    passcode: passcode_val               
                },
                success : function(data) {
                    //console.log("Success : "+data);
                },
                error : function(data){
                    //console.log("Error : "+data);
                }
            });

        });
    });

    /* user edit */
    $('a.edit-button').click(function(evt){
        $('a.edit-button enabled').each(function(){
            $(this).toggleClass('enabled');
            removeForm($(this),$(this).attr('title'));
        });
        evt.preventDefault();
        if (!$(this).hasClass('enabled')){
            // Add form element to field
            addForm($(this), $(this).attr('title'));
        }
        else{
            // Remove form element from field without saving
            removeForm($(this), $(this).attr('title'));
        }
    });
    
    var addForm = function(context, field){
        context.toggleClass('enabled');
        var input = 'input';
        if ( field === 'description' )
            input = 'textarea';  // description field gets a textarea
        var oldData =  $('#' + field).html();
        $('#' + field).html(''
            + '<form action="/home/update" method="post" class="edit-field">'
            + '<' + input + ' type="text" name="' + field + '" placeholder="Enter description here" value="'+oldData+'"></' + input + '>'
            + '<div class="submit-status" hidden></div>'
            + '<input type="submit"></input>'
            + '</form>'
            + '<div class="hidden" hidden>'  // put existing content into a hidden div
            + oldData
            + '</div>'
            + '<div class="clear"></div>'
        );
        $('form.edit-field').submit(function(evt){
            evt.preventDefault();
            postData = $('#' + field).find(input + "[name='" + field + "']").val();
            $('#' + field).find('.submit-status').show().removeClass('failed');
            $.post('/home/update', $('#' + field).find('form').serialize(), function(data){
                if (data === 'success'){
                     $('#' + field).find('div.hidden').html(''
                        + postData
                     );
                     removeForm(context, field);
                } else {
                    $('#' + field).find('.submit-status').addClass('failed');
                }
            }); 
        });
    }

    var removeForm = function(context, field){
        context.toggleClass('enabled');
        oldData = $('#' + field).find('div.hidden').html(); // pull the data out of the hidden div and put it back
        $('#' + field).html(''
            + oldData
        );
    }

    /* unsupported browser detection */
    if ($.browser.msie  && parseInt($.browser.version) <= 8 && $('.browser').length == 0){
        
        //document.getElementById("status-message").className = "status-message browser";
        if (!$('.messsages').length){
            $('.container:eq(0)').append($('<div class="messages"></div>'));
        }

        $(".messages").append(
            "<div class=\"status-wrap\">"
            +"<div class=\"status-message\" style=\"line-height: 1.8em\">"
            +"<div class=\"browser\" "
            +" style=\"font-size:20px;color:#999;padding:20px;border: 1px solid #ddd; width: 903px; \"></div></div></div>"        
        );
        $(".browser").append(
            " You're using Internet Explorer " +  parseInt($.browser.version) + ", which is not supported by Sourcemap."
          + " While we won't stop you from experimenting, we highly recommend using"
          + " the latest version of "
          + " <a href=\"http://www.google.com/chrome\">Chrome</a>,"
          + " <a href=\"http://www.apple.com/safari/\">Safari</a>, or "
          + " <a href=\"http://www.mozilla.org/en-US/firefox/new/\">Firefox</a>."
        );
    }

    /* status message fade */
    if ($('.status-message').length > 0) {
        $('.status-messages').click(function(){
            $(this).fadeOut(0);
        });
        $('.status-messages').fadeIn(400);
    }

    /* modal window fxn courtesy http://www.sohtanaka.com/web-design/examples/modal-window/ */
    $('a.modal').click(function(e) {
        e.preventDefault();
        var popURL = $(this).attr('href'); 

        // parse URL options
        var popDestURL = popURL.split('?')[0];
        var popDest = popURL.split('#')[0] + " #" + popURL.split('#')[1]; 
        var query = popURL.split('?');
        var dim = query[1].split('&');
        var popWidth = dim[0].split('=')[1]; //Gets the first query string value
        
        var popID = 'popup';

        // if an actual URL is specified, get it via a jQuery load()
        if (popDest){
            var element = document.createElement('div'); 
            $(element).load(popDest, function(){
                this.id = popID;

                $(this).addClass("popup_block");
                
                $(this).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

                $('body').append($(element));
               
                $('#' + popID).height(370);
                var popMargTop = ($('#' + popID).height() + 80) / 2;
                var popMargLeft = ($('#' + popID).width() + 80) / 2;
                
                $('#' + popID).css({ 
                    'margin-top' : -popMargTop,
                    'margin-left' : -popMargLeft
                });

                $('#' + popID).width(popWidth); 
                $('#' + popID).fadeIn();
            });
        }

        // otherwise, grab the element whose id === rel
        else {
            var popID = $(this).attr('rel'); 
            $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');
        }
            

        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer 
        
        return false;
    });
    
    $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
        $('#fade , .popup_block').fadeOut(function() {
            $('#fade, a.close').remove();  
    }); //fade them both out
        return false;
    });
    
    /* carousel launch */
    $('.carousel').each(function(){
        $(this).jcarousel({
            scroll: 4,
        });
    });


}); // end of document ready


function DisableOption(input)
{
    // Reest disable function
    $("OPTION").removeAttr("disabled");

    // Set selected value not available to others
    if(input!="0")
        $("OPTION[value="+input+"]").attr('disabled', "disabled");
}

function beforeSubmit()
{
    $("OPTION").removeAttr("disabled");    
    return true;
}


/*!
 * jCarousel - Riding carousels with jQuery
 *   http://sorgalla.com/jcarousel/
 *
 * Copyright (c) 2006 Jan Sorgalla (http://sorgalla.com)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Built on top of the jQuery library
 *   http://jquery.com
 *
 * Inspired by the "Carousel Component" by Bill Scott
 *   http://billwscott.com/carousel/
 */

/*global window, jQuery */
(function($) {
    // Default configuration properties.
    var defaults = {
        vertical: false,
        rtl: false,
        start: 1,
        offset: 1,
        size: null,
        scroll: 3,
        visible: null,
        animation: 'normal',
        easing: 'swing',
        auto: 0,
        wrap: null,
        initCallback: null,
        setupCallback: null,
        reloadCallback: null,
        itemLoadCallback: null,
        itemFirstInCallback: null,
        itemFirstOutCallback: null,
        itemLastInCallback: null,
        itemLastOutCallback: null,
        itemVisibleInCallback: null,
        itemVisibleOutCallback: null,
        animationStepCallback: null,
        buttonNextHTML: '<div></div>',
        buttonPrevHTML: '<div></div>',
        buttonNextEvent: 'click',
        buttonPrevEvent: 'click',
        buttonNextCallback: null,
        buttonPrevCallback: null,
        itemFallbackDimension: null
    }, windowLoaded = false;

    $(window).bind('load.jcarousel', function() { windowLoaded = true; });

    /**
     * The jCarousel object.
     *
     * @constructor
     * @class jcarousel
     * @param e {HTMLElement} The element to create the carousel for.
     * @param o {Object} A set of key/value pairs to set as configuration properties.
     * @cat Plugins/jCarousel
     */
    $.jcarousel = function(e, o) {
        this.options    = $.extend({}, defaults, o || {});

        this.locked          = false;
        this.autoStopped     = false;

        this.container       = null;
        this.clip            = null;
        this.list            = null;
        this.buttonNext      = null;
        this.buttonPrev      = null;
        this.buttonNextState = null;
        this.buttonPrevState = null;

        // Only set if not explicitly passed as option
        if (!o || o.rtl === undefined) {
            this.options.rtl = ($(e).attr('dir') || $('html').attr('dir') || '').toLowerCase() == 'rtl';
        }

        this.wh = !this.options.vertical ? 'width' : 'height';
        this.lt = !this.options.vertical ? (this.options.rtl ? 'right' : 'left') : 'top';

        // Extract skin class
        var skin = '', split = e.className.split(' ');

        for (var i = 0; i < split.length; i++) {
            if (split[i].indexOf('jcarousel-skin') != -1) {
                $(e).removeClass(split[i]);
                skin = split[i];
                break;
            }
        }

        if (e.nodeName.toUpperCase() == 'UL' || e.nodeName.toUpperCase() == 'OL') {
            this.list      = $(e);
            this.clip      = this.list.parents('.jcarousel-clip');
            this.container = this.list.parents('.jcarousel-container');
        } else {
            this.container = $(e);
            this.list      = this.container.find('ul,ol').eq(0);
            this.clip      = this.container.find('.jcarousel-clip');
        }

        if (this.clip.size() === 0) {
            this.clip = this.list.wrap('<div></div>').parent();
        }

        if (this.container.size() === 0) {
            this.container = this.clip.wrap('<div></div>').parent();
        }

        if (skin !== '' && this.container.parent()[0].className.indexOf('jcarousel-skin') == -1) {
            this.container.wrap('<div class=" '+ skin + '"></div>');
        }

        this.buttonPrev = $('.jcarousel-prev', this.container);

        if (this.buttonPrev.size() === 0 && this.options.buttonPrevHTML !== null) {
            this.buttonPrev = $(this.options.buttonPrevHTML).appendTo(this.container);
        }

        this.buttonPrev.addClass(this.className('jcarousel-prev'));

        this.buttonNext = $('.jcarousel-next', this.container);

        if (this.buttonNext.size() === 0 && this.options.buttonNextHTML !== null) {
            this.buttonNext = $(this.options.buttonNextHTML).appendTo(this.container);
        }

        this.buttonNext.addClass(this.className('jcarousel-next'));

        this.clip.addClass(this.className('jcarousel-clip')).css({
            position: 'relative'
        });

        this.list.addClass(this.className('jcarousel-list')).css({
            overflow: 'hidden',
            position: 'relative',
            top: 0,
            margin: 0,
            padding: 0
        }).css((this.options.rtl ? 'right' : 'left'), 0);

        this.container.addClass(this.className('jcarousel-container')).css({
            position: 'relative'
        });

        if (!this.options.vertical && this.options.rtl) {
            this.container.addClass('jcarousel-direction-rtl').attr('dir', 'rtl');
        }

        var di = this.options.visible !== null ? Math.ceil(this.clipping() / this.options.visible) : null;
        var li = this.list.children('li');

        var self = this;

        if (li.size() > 0) {
            var wh = 0, j = this.options.offset;
            li.each(function() {
                self.format(this, j++);
                wh += self.dimension(this, di);
            });

            this.list.css(this.wh, (wh + 100) + 'px');

            // Only set if not explicitly passed as option
            if (!o || o.size === undefined) {
                this.options.size = li.size();
            }
        }

        // For whatever reason, .show() does not work in Safari...
        this.container.css('display', 'block');
        this.buttonNext.css('display', 'block');
        this.buttonPrev.css('display', 'block');

        this.funcNext   = function() { self.next(); };
        this.funcPrev   = function() { self.prev(); };
        this.funcResize = function() { 
            if (self.resizeTimer) {
                clearTimeout(self.resizeTimer);
            }

            self.resizeTimer = setTimeout(function() {
                self.reload();
            }, 100);
        };

        if (this.options.initCallback !== null) {
            this.options.initCallback(this, 'init');
        }

        if (!windowLoaded && $.browser.safari) {
            this.buttons(false, false);
            $(window).bind('load.jcarousel', function() { self.setup(); });
        } else {
            this.setup();
        }
    };

    // Create shortcut for internal use
    var $jc = $.jcarousel;

    $jc.fn = $jc.prototype = {
        jcarousel: '0.2.8'
    };

    $jc.fn.extend = $jc.extend = $.extend;

    $jc.fn.extend({
        /**
         * Setups the carousel.
         *
         * @method setup
         * @return undefined
         */
        setup: function() {
            this.first       = null;
            this.last        = null;
            this.prevFirst   = null;
            this.prevLast    = null;
            this.animating   = false;
            this.timer       = null;
            this.resizeTimer = null;
            this.tail        = null;
            this.inTail      = false;

            if (this.locked) {
                return;
            }

            this.list.css(this.lt, this.pos(this.options.offset) + 'px');
            var p = this.pos(this.options.start, true);
            this.prevFirst = this.prevLast = null;
            this.animate(p, false);

            $(window).unbind('resize.jcarousel', this.funcResize).bind('resize.jcarousel', this.funcResize);

            if (this.options.setupCallback !== null) {
                this.options.setupCallback(this);
            }
        },

        /**
         * Clears the list and resets the carousel.
         *
         * @method reset
         * @return undefined
         */
        reset: function() {
            this.list.empty();

            this.list.css(this.lt, '0px');
            this.list.css(this.wh, '10px');

            if (this.options.initCallback !== null) {
                this.options.initCallback(this, 'reset');
            }

            this.setup();
        },

        /**
         * Reloads the carousel and adjusts positions.
         *
         * @method reload
         * @return undefined
         */
        reload: function() {
            if (this.tail !== null && this.inTail) {
                this.list.css(this.lt, $jc.intval(this.list.css(this.lt)) + this.tail);
            }

            this.tail   = null;
            this.inTail = false;

            if (this.options.reloadCallback !== null) {
                this.options.reloadCallback(this);
            }

            if (this.options.visible !== null) {
                var self = this;
                var di = Math.ceil(this.clipping() / this.options.visible), wh = 0, lt = 0;
                this.list.children('li').each(function(i) {
                    wh += self.dimension(this, di);
                    if (i + 1 < self.first) {
                        lt = wh;
                    }
                });

                this.list.css(this.wh, wh + 'px');
                this.list.css(this.lt, -lt + 'px');
            }

            this.scroll(this.first, false);
        },

        /**
         * Locks the carousel.
         *
         * @method lock
         * @return undefined
         */
        lock: function() {
            this.locked = true;
            this.buttons();
        },

        /**
         * Unlocks the carousel.
         *
         * @method unlock
         * @return undefined
         */
        unlock: function() {
            this.locked = false;
            this.buttons();
        },

        /**
         * Sets the size of the carousel.
         *
         * @method size
         * @return undefined
         * @param s {Number} The size of the carousel.
         */
        size: function(s) {
            if (s !== undefined) {
                this.options.size = s;
                if (!this.locked) {
                    this.buttons();
                }
            }

            return this.options.size;
        },

        /**
         * Checks whether a list element exists for the given index (or index range).
         *
         * @method get
         * @return bool
         * @param i {Number} The index of the (first) element.
         * @param i2 {Number} The index of the last element.
         */
        has: function(i, i2) {
            if (i2 === undefined || !i2) {
                i2 = i;
            }

            if (this.options.size !== null && i2 > this.options.size) {
                i2 = this.options.size;
            }

            for (var j = i; j <= i2; j++) {
                var e = this.get(j);
                if (!e.length || e.hasClass('jcarousel-item-placeholder')) {
                    return false;
                }
            }

            return true;
        },

        /**
         * Returns a jQuery object with list element for the given index.
         *
         * @method get
         * @return jQuery
         * @param i {Number} The index of the element.
         */
        get: function(i) {
            return $('>.jcarousel-item-' + i, this.list);
        },

        /**
         * Adds an element for the given index to the list.
         * If the element already exists, it updates the inner html.
         * Returns the created element as jQuery object.
         *
         * @method add
         * @return jQuery
         * @param i {Number} The index of the element.
         * @param s {String} The innerHTML of the element.
         */
        add: function(i, s) {
            var e = this.get(i), old = 0, n = $(s);

            if (e.length === 0) {
                var c, j = $jc.intval(i);
                e = this.create(i);
                while (true) {
                    c = this.get(--j);
                    if (j <= 0 || c.length) {
                        if (j <= 0) {
                            this.list.prepend(e);
                        } else {
                            c.after(e);
                        }
                        break;
                    }
                }
            } else {
                old = this.dimension(e);
            }

            if (n.get(0).nodeName.toUpperCase() == 'LI') {
                e.replaceWith(n);
                e = n;
            } else {
                e.empty().append(s);
            }

            this.format(e.removeClass(this.className('jcarousel-item-placeholder')), i);

            var di = this.options.visible !== null ? Math.ceil(this.clipping() / this.options.visible) : null;
            var wh = this.dimension(e, di) - old;

            if (i > 0 && i < this.first) {
                this.list.css(this.lt, $jc.intval(this.list.css(this.lt)) - wh + 'px');
            }

            this.list.css(this.wh, $jc.intval(this.list.css(this.wh)) + wh + 'px');

            return e;
        },

        /**
         * Removes an element for the given index from the list.
         *
         * @method remove
         * @return undefined
         * @param i {Number} The index of the element.
         */
        remove: function(i) {
            var e = this.get(i);

            // Check if item exists and is not currently visible
            if (!e.length || (i >= this.first && i <= this.last)) {
                return;
            }

            var d = this.dimension(e);

            if (i < this.first) {
                this.list.css(this.lt, $jc.intval(this.list.css(this.lt)) + d + 'px');
            }

            e.remove();

            this.list.css(this.wh, $jc.intval(this.list.css(this.wh)) - d + 'px');
        },

        /**
         * Moves the carousel forwards.
         *
         * @method next
         * @return undefined
         */
        next: function() {
            if (this.tail !== null && !this.inTail) {
                this.scrollTail(false);
            } else {
                this.scroll(((this.options.wrap == 'both' || this.options.wrap == 'last') && this.options.size !== null && this.last == this.options.size) ? 1 : this.first + this.options.scroll);
            }
        },

        /**
         * Moves the carousel backwards.
         *
         * @method prev
         * @return undefined
         */
        prev: function() {
            if (this.tail !== null && this.inTail) {
                this.scrollTail(true);
            } else {
                this.scroll(((this.options.wrap == 'both' || this.options.wrap == 'first') && this.options.size !== null && this.first == 1) ? this.options.size : this.first - this.options.scroll);
            }
        },

        /**
         * Scrolls the tail of the carousel.
         *
         * @method scrollTail
         * @return undefined
         * @param b {Boolean} Whether scroll the tail back or forward.
         */
        scrollTail: function(b) {
            if (this.locked || this.animating || !this.tail) {
                return;
            }

            this.pauseAuto();

            var pos  = $jc.intval(this.list.css(this.lt));

            pos = !b ? pos - this.tail : pos + this.tail;
            this.inTail = !b;

            // Save for callbacks
            this.prevFirst = this.first;
            this.prevLast  = this.last;

            this.animate(pos);
        },

        /**
         * Scrolls the carousel to a certain position.
         *
         * @method scroll
         * @return undefined
         * @param i {Number} The index of the element to scoll to.
         * @param a {Boolean} Flag indicating whether to perform animation.
         */
        scroll: function(i, a) {
            if (this.locked || this.animating) {
                return;
            }

            this.pauseAuto();
            this.animate(this.pos(i), a);
        },

        /**
         * Prepares the carousel and return the position for a certian index.
         *
         * @method pos
         * @return {Number}
         * @param i {Number} The index of the element to scoll to.
         * @param fv {Boolean} Whether to force last item to be visible.
         */
        pos: function(i, fv) {
            var pos  = $jc.intval(this.list.css(this.lt));

            if (this.locked || this.animating) {
                return pos;
            }

            if (this.options.wrap != 'circular') {
                i = i < 1 ? 1 : (this.options.size && i > this.options.size ? this.options.size : i);
            }

            var back = this.first > i;

            // Create placeholders, new list width/height
            // and new list position
            var f = this.options.wrap != 'circular' && this.first <= 1 ? 1 : this.first;
            var c = back ? this.get(f) : this.get(this.last);
            var j = back ? f : f - 1;
            var e = null, l = 0, p = false, d = 0, g;

            while (back ? --j >= i : ++j < i) {
                e = this.get(j);
                p = !e.length;
                if (e.length === 0) {
                    e = this.create(j).addClass(this.className('jcarousel-item-placeholder'));
                    c[back ? 'before' : 'after' ](e);

                    if (this.first !== null && this.options.wrap == 'circular' && this.options.size !== null && (j <= 0 || j > this.options.size)) {
                        g = this.get(this.index(j));
                        if (g.length) {
                            e = this.add(j, g.clone(true));
                        }
                    }
                }

                c = e;
                d = this.dimension(e);

                if (p) {
                    l += d;
                }

                if (this.first !== null && (this.options.wrap == 'circular' || (j >= 1 && (this.options.size === null || j <= this.options.size)))) {
                    pos = back ? pos + d : pos - d;
                }
            }

            // Calculate visible items
            var clipping = this.clipping(), cache = [], visible = 0, v = 0;
            c = this.get(i - 1);
            j = i;

            while (++visible) {
                e = this.get(j);
                p = !e.length;
                if (e.length === 0) {
                    e = this.create(j).addClass(this.className('jcarousel-item-placeholder'));
                    // This should only happen on a next scroll
                    if (c.length === 0) {
                        this.list.prepend(e);
                    } else {
                        c[back ? 'before' : 'after' ](e);
                    }

                    if (this.first !== null && this.options.wrap == 'circular' && this.options.size !== null && (j <= 0 || j > this.options.size)) {
                        g = this.get(this.index(j));
                        if (g.length) {
                            e = this.add(j, g.clone(true));
                        }
                    }
                }

                c = e;
                d = this.dimension(e);
                if (d === 0) {
                    throw new Error('jCarousel: No width/height set for items. This will cause an infinite loop. Aborting...');
                }

                if (this.options.wrap != 'circular' && this.options.size !== null && j > this.options.size) {
                    cache.push(e);
                } else if (p) {
                    l += d;
                }

                v += d;

                if (v >= clipping) {
                    break;
                }

                j++;
            }

             // Remove out-of-range placeholders
            for (var x = 0; x < cache.length; x++) {
                cache[x].remove();
            }

            // Resize list
            if (l > 0) {
                this.list.css(this.wh, this.dimension(this.list) + l + 'px');

                if (back) {
                    pos -= l;
                    this.list.css(this.lt, $jc.intval(this.list.css(this.lt)) - l + 'px');
                }
            }

            // Calculate first and last item
            var last = i + visible - 1;
            if (this.options.wrap != 'circular' && this.options.size && last > this.options.size) {
                last = this.options.size;
            }

            if (j > last) {
                visible = 0;
                j = last;
                v = 0;
                while (++visible) {
                    e = this.get(j--);
                    if (!e.length) {
                        break;
                    }
                    v += this.dimension(e);
                    if (v >= clipping) {
                        break;
                    }
                }
            }

            var first = last - visible + 1;
            if (this.options.wrap != 'circular' && first < 1) {
                first = 1;
            }

            if (this.inTail && back) {
                pos += this.tail;
                this.inTail = false;
            }

            this.tail = null;
            if (this.options.wrap != 'circular' && last == this.options.size && (last - visible + 1) >= 1) {
                var m = $jc.intval(this.get(last).css(!this.options.vertical ? 'marginRight' : 'marginBottom'));
                if ((v - m) > clipping) {
                    this.tail = v - clipping - m;
                }
            }

            if (fv && i === this.options.size && this.tail) {
                pos -= this.tail;
                this.inTail = true;
            }

            // Adjust position
            while (i-- > first) {
                pos += this.dimension(this.get(i));
            }

            // Save visible item range
            this.prevFirst = this.first;
            this.prevLast  = this.last;
            this.first     = first;
            this.last      = last;

            return pos;
        },

        /**
         * Animates the carousel to a certain position.
         *
         * @method animate
         * @return undefined
         * @param p {Number} Position to scroll to.
         * @param a {Boolean} Flag indicating whether to perform animation.
         */
        animate: function(p, a) {
            if (this.locked || this.animating) {
                return;
            }

            this.animating = true;

            var self = this;
            var scrolled = function() {
                self.animating = false;

                if (p === 0) {
                    self.list.css(self.lt,  0);
                }

                if (!self.autoStopped && (self.options.wrap == 'circular' || self.options.wrap == 'both' || self.options.wrap == 'last' || self.options.size === null || self.last < self.options.size || (self.last == self.options.size && self.tail !== null && !self.inTail))) {
                    self.startAuto();
                }

                self.buttons();
                self.notify('onAfterAnimation');

                // This function removes items which are appended automatically for circulation.
                // This prevents the list from growing infinitely.
                if (self.options.wrap == 'circular' && self.options.size !== null) {
                    for (var i = self.prevFirst; i <= self.prevLast; i++) {
                        if (i !== null && !(i >= self.first && i <= self.last) && (i < 1 || i > self.options.size)) {
                            self.remove(i);
                        }
                    }
                }
            };

            this.notify('onBeforeAnimation');

            // Animate
            if (!this.options.animation || a === false) {
                this.list.css(this.lt, p + 'px');
                scrolled();
            } else {
                var o = !this.options.vertical ? (this.options.rtl ? {'right': p} : {'left': p}) : {'top': p};
                // Define animation settings.
                var settings = {
                    duration: this.options.animation,
                    easing:   this.options.easing,
                    complete: scrolled
                };
                // If we have a step callback, specify it as well.
                if ($.isFunction(this.options.animationStepCallback)) {
                    settings.step = this.options.animationStepCallback;
                }
                // Start the animation.
                this.list.animate(o, settings);
            }
        },

        /**
         * Starts autoscrolling.
         *
         * @method auto
         * @return undefined
         * @param s {Number} Seconds to periodically autoscroll the content.
         */
        startAuto: function(s) {
            if (s !== undefined) {
                this.options.auto = s;
            }

            if (this.options.auto === 0) {
                return this.stopAuto();
            }

            if (this.timer !== null) {
                return;
            }

            this.autoStopped = false;

            var self = this;
            this.timer = window.setTimeout(function() { self.next(); }, this.options.auto * 1000);
        },

        /**
         * Stops autoscrolling.
         *
         * @method stopAuto
         * @return undefined
         */
        stopAuto: function() {
            this.pauseAuto();
            this.autoStopped = true;
        },

        /**
         * Pauses autoscrolling.
         *
         * @method pauseAuto
         * @return undefined
         */
        pauseAuto: function() {
            if (this.timer === null) {
                return;
            }

            window.clearTimeout(this.timer);
            this.timer = null;
        },

        /**
         * Sets the states of the prev/next buttons.
         *
         * @method buttons
         * @return undefined
         */
        buttons: function(n, p) {
            if (n == null) {
                n = !this.locked && this.options.size !== 0 && ((this.options.wrap && this.options.wrap != 'first') || this.options.size === null || this.last < this.options.size);
                if (!this.locked && (!this.options.wrap || this.options.wrap == 'first') && this.options.size !== null && this.last >= this.options.size) {
                    n = this.tail !== null && !this.inTail;
                }
            }

            if (p == null) {
                p = !this.locked && this.options.size !== 0 && ((this.options.wrap && this.options.wrap != 'last') || this.first > 1);
                if (!this.locked && (!this.options.wrap || this.options.wrap == 'last') && this.options.size !== null && this.first == 1) {
                    p = this.tail !== null && this.inTail;
                }
            }

            var self = this;

            if (this.buttonNext.size() > 0) {
                this.buttonNext.unbind(this.options.buttonNextEvent + '.jcarousel', this.funcNext);

                if (n) {
                    this.buttonNext.bind(this.options.buttonNextEvent + '.jcarousel', this.funcNext);
                }

                this.buttonNext[n ? 'removeClass' : 'addClass'](this.className('jcarousel-next-disabled')).attr('disabled', n ? false : true);

                if (this.options.buttonNextCallback !== null && this.buttonNext.data('jcarouselstate') != n) {
                    this.buttonNext.each(function() { self.options.buttonNextCallback(self, this, n); }).data('jcarouselstate', n);
                }
            } else {
                if (this.options.buttonNextCallback !== null && this.buttonNextState != n) {
                    this.options.buttonNextCallback(self, null, n);
                }
            }

            if (this.buttonPrev.size() > 0) {
                this.buttonPrev.unbind(this.options.buttonPrevEvent + '.jcarousel', this.funcPrev);

                if (p) {
                    this.buttonPrev.bind(this.options.buttonPrevEvent + '.jcarousel', this.funcPrev);
                }

                this.buttonPrev[p ? 'removeClass' : 'addClass'](this.className('jcarousel-prev-disabled')).attr('disabled', p ? false : true);

                if (this.options.buttonPrevCallback !== null && this.buttonPrev.data('jcarouselstate') != p) {
                    this.buttonPrev.each(function() { self.options.buttonPrevCallback(self, this, p); }).data('jcarouselstate', p);
                }
            } else {
                if (this.options.buttonPrevCallback !== null && this.buttonPrevState != p) {
                    this.options.buttonPrevCallback(self, null, p);
                }
            }

            this.buttonNextState = n;
            this.buttonPrevState = p;
        },

        /**
         * Notify callback of a specified event.
         *
         * @method notify
         * @return undefined
         * @param evt {String} The event name
         */
        notify: function(evt) {
            var state = this.prevFirst === null ? 'init' : (this.prevFirst < this.first ? 'next' : 'prev');

            // Load items
            this.callback('itemLoadCallback', evt, state);

            if (this.prevFirst !== this.first) {
                this.callback('itemFirstInCallback', evt, state, this.first);
                this.callback('itemFirstOutCallback', evt, state, this.prevFirst);
            }

            if (this.prevLast !== this.last) {
                this.callback('itemLastInCallback', evt, state, this.last);
                this.callback('itemLastOutCallback', evt, state, this.prevLast);
            }

            this.callback('itemVisibleInCallback', evt, state, this.first, this.last, this.prevFirst, this.prevLast);
            this.callback('itemVisibleOutCallback', evt, state, this.prevFirst, this.prevLast, this.first, this.last);
        },

        callback: function(cb, evt, state, i1, i2, i3, i4) {
            if (this.options[cb] == null || (typeof this.options[cb] != 'object' && evt != 'onAfterAnimation')) {
                return;
            }

            var callback = typeof this.options[cb] == 'object' ? this.options[cb][evt] : this.options[cb];

            if (!$.isFunction(callback)) {
                return;
            }

            var self = this;

            if (i1 === undefined) {
                callback(self, state, evt);
            } else if (i2 === undefined) {
                this.get(i1).each(function() { callback(self, this, i1, state, evt); });
            } else {
                var call = function(i) {
                    self.get(i).each(function() { callback(self, this, i, state, evt); });
                };
                for (var i = i1; i <= i2; i++) {
                    if (i !== null && !(i >= i3 && i <= i4)) {
                        call(i);
                    }
                }
            }
        },

        create: function(i) {
            return this.format('<li></li>', i);
        },

        format: function(e, i) {
            e = $(e);
            var split = e.get(0).className.split(' ');
            for (var j = 0; j < split.length; j++) {
                if (split[j].indexOf('jcarousel-') != -1) {
                    e.removeClass(split[j]);
                }
            }
            e.addClass(this.className('jcarousel-item')).addClass(this.className('jcarousel-item-' + i)).css({
                'float': (this.options.rtl ? 'right' : 'left'),
                'list-style': 'none'
            }).attr('jcarouselindex', i);
            return e;
        },

        className: function(c) {
            return c + ' ' + c + (!this.options.vertical ? '-horizontal' : '-vertical');
        },

        dimension: function(e, d) {
            var el = $(e);

            if (d == null) {
                return !this.options.vertical ?
                       (el.outerWidth(true) || $jc.intval(this.options.itemFallbackDimension)) :
                       (el.outerHeight(true) || $jc.intval(this.options.itemFallbackDimension));
            } else {
                var w = !this.options.vertical ?
                    d - $jc.intval(el.css('marginLeft')) - $jc.intval(el.css('marginRight')) :
                    d - $jc.intval(el.css('marginTop')) - $jc.intval(el.css('marginBottom'));

                $(el).css(this.wh, w + 'px');

                return this.dimension(el);
            }
        },

        clipping: function() {
            return !this.options.vertical ?
                this.clip[0].offsetWidth - $jc.intval(this.clip.css('borderLeftWidth')) - $jc.intval(this.clip.css('borderRightWidth')) :
                this.clip[0].offsetHeight - $jc.intval(this.clip.css('borderTopWidth')) - $jc.intval(this.clip.css('borderBottomWidth'));
        },

        index: function(i, s) {
            if (s == null) {
                s = this.options.size;
            }

            return Math.round((((i-1) / s) - Math.floor((i-1) / s)) * s) + 1;
        }
    });

    $jc.extend({
        /**
         * Gets/Sets the global default configuration properties.
         *
         * @method defaults
         * @return {Object}
         * @param d {Object} A set of key/value pairs to set as configuration properties.
         */
        defaults: function(d) {
            return $.extend(defaults, d || {});
        },

        intval: function(v) {
            v = parseInt(v, 10);
            return isNaN(v) ? 0 : v;
        },

        windowLoaded: function() {
            windowLoaded = true;
        }
    });

    /**
     * Creates a carousel for all matched elements.
     *
     * @example $("#mycarousel").jcarousel();
     * @before <ul id="mycarousel" class="jcarousel-skin-name"><li>First item</li><li>Second item</li></ul>
     * @result
     *
     * <div class="jcarousel-skin-name">
     *   <div class="jcarousel-container">
     *     <div class="jcarousel-clip">
     *       <ul class="jcarousel-list">
     *         <li class="jcarousel-item-1">First item</li>
     *         <li class="jcarousel-item-2">Second item</li>
     *       </ul>
     *     </div>
     *     <div disabled="disabled" class="jcarousel-prev jcarousel-prev-disabled"></div>
     *     <div class="jcarousel-next"></div>
     *   </div>
     * </div>
     *
     * @method jcarousel
     * @return jQuery
     * @param o {Hash|String} A set of key/value pairs to set as configuration properties or a method name to call on a formerly created instance.
     */
    $.fn.jcarousel = function(o) {
        if (typeof o == 'string') {
            var instance = $(this).data('jcarousel'), args = Array.prototype.slice.call(arguments, 1);
            return instance[o].apply(instance, args);
        } else {
            return this.each(function() {
                var instance = $(this).data('jcarousel');
                if (instance) {
                    if (o) {
                        $.extend(instance.options, o);
                    }
                    instance.reload();
                } else {
                    $(this).data('jcarousel', new $jc(this, o));
                }
            });
        }
    };

})(jQuery);
