<!-- Demo Code for Organic Valley site. -->
<!-- Cobbled together from code at organicvalley.coop and sourcemap stuff. -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="en" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb=
"http://www.facebook.com/2008/fbml" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta name="generator" content=
  "HTML Tidy for Linux/x86 (vers 11 February 2007), see www.w3.org" />
  <link rel="shortcut icon" href=
  "http://www.organicvalley.coop/fileadmin/marquee/images/favicon.ico" type=
  "image/x-ico; charset=binary" />
  <link rel="icon" href=
  "http://www.organicvalley.coop/fileadmin/marquee/images/favicon.ico" type=
  "image/x-ico; charset=binary" />

  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script type="text/javascript" src="/sites/organicvalley/assets/scripts/jquery.maphilight.min.js"></script>
  

  <title>Organic Valley - Meet the Farmers</title>
  <meta name="generator" content="TYPO3 4.4 CMS" />
  <meta name="viewport" content="width=960" />
  <meta http-equiv="X-UA-Compatible" content="IE=IE8" />
  <link rel="stylesheet" type="text/css" href=
  "http://ovfm.ov.coop/fileadmin/marquee/stylesheets/bc8.z.min.css" />
  <link rel="stylesheet" type="text/css" href=
  "/typo3conf/ext/user_farmer/pi1/static/farmer-layout.css" media="screen" />
  <link rel="image_src" href="/typo3conf/ext/user_farmer/pi1/images/farmer-fbicon.jpg" />
</head>

<body>
  <div id="pagecolumn" class="container_24">
  <div id="header">
    <h2><a href="/"><span>Organic Valley, a farmer owned organic dairy
    cooperative.</span></a></h2>

    <h6>1617 Owners Strong</h6>
  </div>

  <div>
    <div id="nav">
      <ul>
        <li id="navwyf" class="active"><a href="http://www.organicvalley.coop/who-is-your-farmer/index/" class=
        "active"><span>Who's Your Farmer</span></a></li>

        <li id="navproducts"><a href="http://www.organicvalley.coop/products/"><span>Products</span></a></li>

        <li id="navrecipes"><a href="http://www.organicvalley.coop/products/recipes/search-recipes/"><span>Recipes</span></a></li>

        <li id="navwhy"><a href="http://www.organicvalley.coop/products/why-organic/overview/"><span>Why Organic?</span></a></li>

        <li id="navcommunity"><a href="http://www.organicvalley.coop/products/community/overview/"><span>Community</span></a></li>

        <li id="navnewsroom"><a href="http://www.organicvalley.coop/newsroom/overview/"><span>Newsroom</span></a></li>

        <li id="navstory"><a href="http://www.organicvalley.coop/about-us/overview/our-history/"><span>About
        Us</span></a></li>
      </ul>
    </div>
  </div>

  <div>
    <div class="breadcrumb">
      <ul>
        <li class="home"><a href="/" title="Return to homepage"><span>Home</span></a></li>

        <li>&raquo;&nbsp;<a href="/" target="_self">Where Does it Come From?</a></li>

        <li>&raquo;&nbsp;Overview</li>
      </ul>
    </div>
  </div>

  <div class="clear"></div>

  <div id="body" class="grid_20 push_4">
    <!--TYPO3SEARCH_begin-->

    <div id="c9904" class="csc-default">
      <div class="user-farmer-pi1">
          <div id="map">
            <?= $content ?>
          </div>
      </div>
    </div><!--TYPO3SEARCH_end-->
  </div>

  <div id="sectionnav" class="grid_4 pull_20">
    <div id="submenu">
      <ul>
        <li class="selected"><a href="">Home</a></li>

        <li class="close"><a href="">Milk</a></li>

        <li class="close"><a href="">Eggs</a></li>

        <li class="close"><a href="">Cheese</a></li>

      </ul>

      <div class="fancycap"></div>
    </div>

    <div id="aftersubmenu">
      <!--TYPO3SEARCH_begin--><!--TYPO3SEARCH_end-->
    </div>
  </div>

  <div id="features" class="grid_6"></div>

  <div class="clear"></div>

  <div id="controls">
    <h6>Share</h6>

    <div id="facebook">
      <a title="Share on Facebook" href=
      "javascript:openCenteredWindow('http://www.facebook.com/sharer.php?u=http://www.organicvalley.coop/who-is-your-farmer/index/farmer/1248/&amp;t=Meet+the+Farmers','Share%20on%20Facebook','',600,480)">
      <span>Share on Facebook</span></a>
    </div>

    <div id="facebook-dialog"></div>

    <div id="twitter">
      <a href="#" id="twittershare" title="Share on twitter" name=
      "twittershare"><span>Tweet This</span></a>
    </div>

    <div id="rss"></div>

    <div id="libwheretobuy">
      <form action="store-locator/stores-results/" method="get" name="wheretobuy" id=
      "wheretobuy">
        <fieldset>
          <legend>Where to Buy Organic Valley Products</legend><label for="wtb_zip">Your
          zip code</label><span class="head-input-container"><input id="wtb_zip" type=
          "text" name="stores_zip" value="" /></span><span id=
          "wtbtarget"></span><input class="submit" type="submit" name="submit" value=
          "Submit" /><input type="hidden" class="hidden" value="1" name=
          "main_search" /><input type="hidden" class="hidden" value="-1" name=
          "stores_category" /><input type="hidden" class="hidden" value="10" name=
          "stores_distance" />
        </fieldset>
      </form>
    </div>

    <div id="libsearch">
      <form action="/search/" method="get" name="searchform" id="searchform">
        <fieldset>
          <legend>Search the Website</legend><label for=
          "tx_mnogosearch_pi1_q">Keywords</label><span class=
          "head-input-container"><input id="tx_mnogosearch_pi1_q" name=
          "tx_googlecse_pi1[q]" type="text" size="18" value="" /></span><span id=
          "searchtarget"></span><input class="submit" name="tx_googlecse_pi1[submit]"
          type="submit" value="Submit" />
        </fieldset>
      </form>
    </div>
  </div>

  <div id="c8622" class="csc-default">
    <div id="promos">
      <a class="prevPage browse left"></a>

      <div class="scrollable">
        <div class="items">
          <div class="scroll-item">
            <div class="scroll-grouped-item">
              <div id="c9540" class="csc-default">
                <a title="Print Organic Valley coupons" href="coupons/"><img width="215"
                height="144" alt="get coupons" src=
                "http://ovfm.ov.coop/sm/coupons-chunky.png" /></a>
              </div>
            </div>

            <div class="scroll-grouped-item">
              <div id="c8623" class="csc-default">
                <div class="sm sm-wtb">
                  <form action="store-locator/stores-results/" method="get" onsubmit=
                  "if(jQuery('#sm-wtb-zip').attr('value') == 'Enter your zip'){jQuery('#sm-wtb-zip').attr('value','');}"
                  name="sm-wtbf" id="sm-wtbf">
                    <fieldset>
                      <legend>Where to Buy Organic Valley Products</legend> <label for=
                      "sm-wtb-zip">Your zip code</label> <input id="sm-wtb-zip" value=
                      "Enter your zip" onclick=
                      "if(this.value == 'Enter your zip'){this.value='';}" type="text"
                      name="stores_zip" /> <span id="sm-wtb-target" onclick=
                      "jQuery('#sm-wtbf .submit').click();"></span> <input class="submit"
                      type="submit" name="submit" value="Submit" /> <input type="hidden"
                      class="hidden" value="1" name="main_search" /> <input type="hidden"
                      class="hidden" value="-1" name="stores_category" /> <input type=
                      "hidden" class="hidden" value="10" name="stores_distance" />
                    </fieldset>
                  </form>
                </div>
              </div>
            </div>

            <div class="scroll-grouped-item">
              <div id="c9494" class="csc-default">
                <img class="sm-item" name="smchunky" src=
                "http://ovfm.ov.coop/sm/sm-chunky.png" width="215" height="144" border=
                "0" id="smchunky" usemap="#m_smchunky" alt=
                "Organic Valley on Facebook and Twitter" /><map name="m_smchunky" id=
                "m_smchunky">
                  <area shape="poly" coords="6,140,209,5,210,141,6,140" href=
                  "http://twitter.com/organicvalley" title="Follow us on Twitter" alt=
                  "Follow us on Twitter" />
                  <area shape="poly" coords="4,3,4,138,205,4,4,3" href=
                  "http://www.facebook.com/OrganicValley" title="Friend us on Facebook"
                  alt="Friend us on Facebook" />
                </map>
              </div>
            </div>

            <div class="scroll-grouped-item">
              <div id="c8617" class="csc-default">
                <a title="Food, Farms and Health" href=
                "community/beyond-the-plate/efas/page1/"><img width="215" height="144"
                alt="Beyond the Plate" src=
                "http://ovfm.ov.coop/sm/btp-chunky-8b-1.png" /></a>
              </div>
            </div>
          </div>

          <div class="scroll-item">
            <div class="scroll-grouped-item">
              <div class="delayLoad" data-content=
              "%3Cdiv%20id%3D%22c8620%22%20class%3D%22csc-default%22%20%3E%3Ca%20title%3D%22Calculate%20your%20impact%22%20href%3D%22%2Forganiccounts%2F%22%3E%3Cimg%20width%3D%22215%22%20height%3D%22144%22%20alt%3D%2290%2C770%2C797lbs%20of%20toxic%20chemicals%20prevented%20since%201988.%22%20src%3D%22http%3A%2F%2Fovfm.ov.coop%2Fsm%2Foc-chunky_05.png%22%3E%3C%2Fa%3E%3C%2Fdiv%3E">
              </div>
            </div>

            <div class="scroll-grouped-item">
              <div class="delayLoad" data-content=
              "%3Cdiv%20id%3D%22c8615%22%20class%3D%22csc-default%22%20%3E%3Ca%20title%3D%22See%20what%20we%27ve%20won%22%20href%3D%22products%2Fawards%2F%22%3E%0D%0A%3Cimg%20width%3D%22215%22%20height%3D%22144%22%20alt%3D%22Our%20Awards%22%20src%3D%22http%3A%2F%2Fovfm.ov.coop%2Fsm%2Fawards-chunky_02.png%22%3E%0D%0A%3C%2Fa%3E%3C%2Fdiv%3E">
              </div>
            </div>

            <div class="scroll-grouped-item">
              <div class="delayLoad" data-content=
              "%3Cdiv%20id%3D%22c8612%22%20class%3D%22csc-default%22%20%3E%3Ca%20title%3D%22Learn%20about%20Animal%20Care%22%20href%3D%22why-organic%2Fhumane-treatment%2F%22%3E%3Cimg%20width%3D%22215%22%20height%3D%22144%22%20alt%3D%22Animal%20Care%22%20src%3D%22http%3A%2F%2Fovfm.ov.coop%2Fsm%2Fac-chunky-8b_01.png%22%3E%3C%2Fa%3E%3C%2Fdiv%3E">
              </div>
            </div>
          </div>
        </div>
      </div><a class="nextPage browse right"></a>
    </div>
  </div><img style="display: none;" src=
  "http://ovfm2.ov.coop/fileadmin/marquee/images/ov-chunky.png" width="215" height="144"
  alt="Organic Valley" /></div>

  <div id="footershelf">
    <div id="footer">
      <div id="footernav">
        <ul>
          <li><a href="faq/">Frequently Asked Questions</a></li>

          <li><a href="contact-us/">Contact Us</a></li>

          <li><a href="about-us/employment/">Work with Us</a></li>

          <li><a href="farmer-support/">Farmer Support</a></li>

          <li><a href="trade/overview/">Trade</a></li>
        </ul>
      </div>

      <div id="footerbody">
        <div class="breadcrumb">
          <ul>
            <li class="home"><a href="/" title=
            "Return to homepage"><span>Home</span></a></li>

            <li>&raquo;&nbsp;<a href="/" target="_self">Who's
            Your Farmer</a></li>

            <li>&raquo;&nbsp;Overview</li>
          </ul>
        </div>
        <hr />

        <ul id="sitemap">
          <li class="grid_1 active">
            <h6><a href="who-is-your-farmer/index/">Who's Your Farmer</a></h6>

            <ul>
              <li class="first active">Overview</li>

              <li><a href="who-is-your-farmer/new-york-fresh/">New York Fresh</a></li>

              <li><a href="who-is-your-farmer/northeast/">Northeast</a></li>

              <li><a href="who-is-your-farmer/heartland/">Heartland</a></li>

              <li><a href="who-is-your-farmer/northwest/">Northwest</a></li>

              <li><a href="who-is-your-farmer/rocky-mountain/">Rocky Mountain</a></li>

              <li><a href="who-is-your-farmer/southwest/">Southwest</a></li>

              <li><a href="who-is-your-farmer/california/">California</a></li>

              <li class="last"><a href="who-is-your-farmer/southeast/">Southeast</a></li>
            </ul>
          </li>

          <li class="grid_1 alpha">
            <h6><a href="products/">Products</a></h6>

            <ul>
              <li class="first"><a href="products/why-choose-organic-valley/">Why Choose
              OV?</a></li>

              <li><a href="products/yogurt/">Yogurt</a></li>

              <li><a href="products/soy/">Soy</a></li>

              <li><a href="products/milk/">Milk</a></li>

              <li><a href="products/cream/">Cream</a></li>

              <li><a href="products/creamers/">Creamers</a></li>

              <li><a href="products/butter/">Butter</a></li>

              <li><a href="products/cottage-cheese/">Cottage Cheese</a></li>

              <li><a href="products/sour-cream/">Sour Cream</a></li>

              <li><a href="products/cream-cheese/">Cream Cheese</a></li>

              <li><a href="products/cheese/">Cheese</a></li>

              <li><a href="products/juice/">Juice</a></li>

              <li><a href="products/eggs/">Eggs</a></li>

              <li><a href="products/meat/">Meat</a></li>

              <li><a href="products/healthy-snacks/">Healthy Snacks</a></li>

              <li><a href="products/produce/">Produce</a></li>

              <li><a href="products/all/">All Products</a></li>

              <li><a href="products/gluten-free-products/">Gluten Free Products</a></li>

              <li><a href="products/awards/">Awards</a></li>

              <li class="last"><a href="coupons/">Coupons</a></li>
            </ul>
          </li>

          <li class="grid_1">
            <h6><a href="recipes/search-recipes/">Recipes</a></h6>

            <ul>
              <li class="first"><a href="recipes/search-recipes/">Search for
              Recipes</a></li>

              <li><a href="recipes/dish-types/">Browse Dish Types</a></li>

              <li class="last"><a href="recipes/kitchen-pantry/">Kitchen Pantry</a></li>
            </ul>
          </li>

          <li class="grid_1">
            <h6><a href="why-organic/overview/">Why Organic?</a></h6>

            <ul>
              <li class="first"><a href="why-organic/overview/">Six Reasons</a></li>

              <li><a href="why-organic/nutrition-and-health/">Nutrition and
              Health</a></li>

              <li><a href="why-organic/pesticides/">Pesticides</a></li>

              <li><a href="why-organic/synthetic-fertilizers/">Synthetic
              Fertilizers</a></li>

              <li><a href="why-organic/synthetic-hormones/">Synthetic Hormones</a></li>

              <li><a href="why-organic/antibiotics/">Antibiotics</a></li>

              <li><a href="why-organic/gmos/">GMOs</a></li>

              <li><a href="why-organic/humane-treatment/">Animal Care</a></li>

              <li><a href="why-organic/pasture/">Pasture</a></li>

              <li><a href="why-organic/environment/">Environment</a></li>

              <li><a href="why-organic/community/">Community</a></li>

              <li><a href="why-organic/organic-defined/">Organic Defined</a></li>

              <li><a href="why-organic/organic-defined/the-natural-hoax/">The "Natural"
              Hoax</a></li>

              <li class="last"><a href="why-organic/research-library/overview/">Research
              Library</a></li>
            </ul>
          </li>

          <li class="grid_1">
            <h6><a href="community/overview/">Community</a></h6>

            <ul>
              <li class="first"><a href="community/overview/">Overview</a></li>

              <li><a href="community/farm-friends/">Farm Friends</a></li>

              <li><a href="community/organicsense/">Organic Sense</a></li>

              <li><a href="community/beyond-the-plate/food-safety/page1/">Beyond the
              Plate</a></li>

              <li><a href="community/down-natures-trail/latest/">Down Nature's
              Trail</a></li>

              <li><a href="community/making-hay/">Making Hay</a></li>

              <li><a href="community/ovies/">Ovie's Underground</a></li>

              <li><a href="community/subscribe/">Subscribe</a></li>

              <li class="last"><a href="community/ov-general-store/">OV General
              Store</a></li>
            </ul>
          </li>

          <li class="grid_1">
            <h6><a href="newsroom/overview/">Newsroom</a></h6>

            <ul>
              <li class="first"><a href="newsroom/overview/">Overview</a></li>

              <li><a href="newsroom/press-releases/">Press Releases</a></li>

              <li><a href="newsroom/organic-valley-in-the-news/">Organic Valley in the
              News</a></li>

              <li><a href="newsroom/organics-in-the-news/">Organics in the News</a></li>

              <li><a href="newsroom/photos/">Graphic Resources</a></li>

              <li class="last"><a href="newsroom/about-organic-valley/">About Organic
              Valley</a></li>
            </ul>
          </li>

          <li class="grid_1">
            <h6><a href="about-us/overview/our-history/">About Us</a></h6>

            <ul>
              <li class="first"><a href="about-us/overview/our-history/">Our
              Story</a></li>

              <li><a href="about-us/our-cooperative/our-mission/">Our Mission</a></li>

              <li><a href="about-us/our-cooperative/our-co-op/">The Cooperative</a></li>

              <li><a href="about-us/generation-organic/gen-o-tour/">Generation
              Organic</a></li>

              <li><a href="about-us/transparency/">Transparency</a></li>

              <li><a href="about-us/sustainability/page1/">Sustainability</a></li>

              <li><a href="about-us/our-partners/">Partners</a></li>

              <li><a href="about-us/grow-with-our-co-op/">Farm with Us</a></li>

              <li><a href="about-us/organic-trader/">Organic Trader</a></li>

              <li><a href="about-us/employment/">Work with Us</a></li>

              <li><a href="about-us/invest/stock-prospectus/">Invest with Us</a></li>

              <li class="last"><a href="about-us/donations/">Cooperative Giving</a></li>
            </ul>
          </li>

          <li class="grid_1 omega">
            <h6><a href="trade/overview/">Trade</a></h6>

            <ul>
              <li class="first"><a href="trade/overview/">Overview</a></li>

              <li><a href="trade/retail/">Retail</a></li>

              <li><a href="trade/product-info/">Product Info</a></li>

              <li><a href="trade/certificates/">Certificates</a></li>

              <li><a href="trade/foodservice/">Foodservice</a></li>

              <li><a href="trade/bulk-ingredients/">Commercial Ingredients</a></li>

              <li><a href="trade/produce/">Produce</a></li>

              <li><a href="newsroom/photos/">Graphics Center</a></li>

              <li><a href="trade/contact-sales-support/">Contact Sales Support</a></li>

              <li class="last"><a href="trade/retail/">Sales Team Contacts</a></li>
            </ul>
          </li>
        </ul>
        <hr />

        <div id="footer_copyright">
          <p>Organic Valley Family of Farms &copy;2011</p>

          <div id="regionalInfo"></div>
        </div>

        <div class="usda">
          <a href="/why-organic/organic-defined/government-regulation/"><span>USDA
          Certified Organic</span></a>
        </div>
      </div>

      <div id="footercap"></div>
    </div>
  </div>

  <ul class="productmenu" style="display:none;">
    <li>
      <a href="/products/milk/" class="p-milk">Milk</a><a href="/products/cream/" class=
      "p-cream">Cream</a><a href="/products/yogurt/" class="p-yogurt">Yogurt</a><a href=
      "/products/cream-cheese/" class="p-cc">Cream Cheese</a><a href=
      "/products/sour-cream/" class="p-sour">Sour Cream</a><a href="/products/butter/"
      class="p-butter">Butter</a><a href="/products/cheese/" class=
      "p-cheese">Cheese</a><a href="/products/soy/" class="p-soy">Soy</a><a href=
      "/products/juice/" class="p-juice">Juice</a><a href="/products/cottage-cheese/"
      class="p-cottage">Cottage Cheese</a><a href="/products/eggs/" class=
      "p-eggs">Eggs</a><a href="/products/produce/" class="p-produce">Produce</a><a href=
      "/products/meat/" class="p-meat">Meat</a><a href="/products/" class=
      "p-all"><span>All Products</span></a>

      <div class="clear"></div>
    </li>
  </ul>

  <div id="fb-root"></div><script type="text/javascript">
//<![CDATA[
  window.fbAsyncInit=function(){FB.init({appId:"369741567538",status:true,cookie:true,xfbml:true})};(function(){var a=document.createElement("script");a.async=true;a.src=document.location.protocol+"//connect.facebook.net/en_US/all.js";document.getElementById("fb-root").appendChild(a)})();
  //]]>
  </script>
  <!--[if lte IE 7]> <link rel="stylesheet" type="text/css" href="http://ovfm.ov.coop/fileadmin/marquee/stylesheets/ie7ltez.css" /> <![endif]-->
</body>
</html>

