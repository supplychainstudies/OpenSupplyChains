$(function () {
    $('#slider').anythingSlider({
        buildArrows: false,
        delay: 5000,
        easing: 'easeInOutExpo',
        hashtags: false,
        width: '940px',
        height: '356px',
        onInitialized : function(){      // Random styling of buttons 
            var minSize = 18;
            var maxSize = 27;

            function randOrd() {
                return (Math.round(Math.random())-0.5); 
            }

            var classes = ['orange', 'yellow', 'brown'];
            classes.sort( randOrd );
            $(".anythingControls li a").each(function(i){
                var size = Math.floor(Math.random() * (maxSize - minSize + 1) + minSize);
                var opacity = Math.random() * .3 + .7;
                $(this).addClass(classes[i]).css({
                    'height': size,
                    'width': size,
                    'border-radius': (size + 2) / 2,
                    '-moz-border-radius': (size + 2) / 2,
                    '-webkit-border-radius': (size + 2) / 2,
                    'padding': 0,
                    'text-indent': '-999em',
               });
            })
        },
    });
});
