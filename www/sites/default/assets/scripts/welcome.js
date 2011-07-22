$(function () {
    $('#featured-slider').anythingSlider({
        buildArrows: false,
        delay: 5000,
        easing: 'easeInOutExpo',
        hashTags: false,
        width: '1040px',
        height: '322px',
        onInitialized : function(){      // Random styling of buttons 
            var minSize = 18;
            var maxSize = 27;

            function randOrd() {
                return (Math.round(Math.random())-0.5); 
            }

            // Code that adds a floating description box
            $('.anythingWindow').append('<div id="featured-slider-description"><h1>Yeah</h1></div>');
            $('#featured-slider-description').empty();
            $('#featured-description-0').contents().clone().appendTo($('#featured-slider-description'));
        },
        onSlideComplete : function(slider) {
            $('#featured-slider-description').empty();
            $('#featured-description-' + (slider.currentPage - 1)).contents().clone().appendTo($('#featured-slider-description'));
        }
    });
});
