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

$(function () {
    $('#featured-slider').anythingSlider({
        buildArrows: false,
        delay: 5000,
        easing: 'easeInOutExpo',
        hashTags: false,
        width: '500px',
        height: '250px',
        onInitialized : function(){      // Random styling of buttons 
            var minSize = 18;
            var maxSize = 27;

            function randOrd() {
                return (Math.round(Math.random())-0.5); 
            }

            // Code that adds a floating description box
            $('.anythingWindow').append('<div id="featured-slider-description"><h1>...</h1></div>');
            $('.channel-featured-maps').append('<div id="featured-slider-text"><p>...</p></div>');
            $('#featured-slider-description').empty();
            $('#featured-slider-text').empty();
            $('#featured-description-0').contents().clone().appendTo($('#featured-slider-description'));
        },
        onSlideComplete : function(slider) {
            $('#featured-slider-description').empty();
            $('#featured-description-' + (slider.currentPage - 1)).contents().clone().appendTo($('#featured-slider-description'));
        }
    });
});
