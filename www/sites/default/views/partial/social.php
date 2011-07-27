        <div class="container social">
            <!-- Facebook like -->
            <div class="social-icon">
                <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?= URL::site(NULL, TRUE) ?>view/<?= $supplychain_id ?>" send="false" layout="button_count" width="450" show_faces="true" font=""></fb:like>
            </div>
            
            <!-- Google +1 -->
            <div class="social-icon">
                <g:plusone size="medium"></g:plusone>
                <script type="text/javascript">
                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
            </div>

            <!-- Twitter tweeter -->
            <div class="social-icon">
                <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>            
            </div>

            <div class="clear"></div>
        </div>

