<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Values &amp; Actions Overview | WholeFoodsMarket.com</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="We now number over 50,000 team members and are glad to report that our idealism and commitment to our core values are as strong as ever" /> 
		<link rel="canonical" href="index.html" />

		<link rel="stylesheet" href="http://www.wholefoodsmarket.com/css/wfm-default.css" />
		<link rel="stylesheet" href="http://www.wholefoodsmarket.com/css/tabber.css" />
		<link rel="shortcut icon" href="../images/favicon.ico" />
		<link rel="apple-touch-icon" href="http://www.wholefoodsmarket.com/images/apple-touch-icon.png" />

		<script type="text/javascript" src="http://www.wholefoodsmarket.com/scripts/showhide.js"></script>
		<script type="text/javascript" src="http://www.wholefoodsmarket.com/scripts/tabber.js"></script>
		<script type="text/javascript">AC_FL_RunContent = 0;</script>
		<script type="text/javascript" src="http://www.wholefoodsmarket.com/scripts/AC_RunActiveContent.js"></script>
</head>
<body class="values" id="values-index">

<div class="container"><a name="top"></a>

	<div class="header">
<script type="text/javascript">var _gat=null;</script>
<div class="wrap" id="headerwrap">
			<ul id="skiplinks">
				<li><a href="index.html#maincontent"><img src="../images/pxclear.gif" height="1" width="1" alt="skip to main content" /></a></li>
				<li><a href="index.html#navlinks"><img src="../images/pxclear.gif" height="1" width="1" alt="skip to primary navigation" /></a></li>
				<li><a href="index.html#subnavlinks"><img src="../images/pxclear.gif" height="1" width="1" alt="skip to section navigation" /></a></li>
			</ul>

		<h1 role="banner"><a href="http://www.wholefoodsmarket.com/"><img id="logo" src="../images/wfm-logo-horiz-cv1.gif" alt="Whole Foods Market" width="200" height="85" /></a></h1>


		<div class="storefinder">
	<img id="storethumb" src="../images/store-thumb-sample.jpg" alt="" width="85" height="65" />
		<p><strong>Find your store:</strong></p>

		<form method="get" action="http://wholefoodsmarket.com/stores/store-list/">
			<ul>
				<li><label for="zipcode">ZIP Code:</label>
				<input id="zipcode" name="zipcode" size="10" maxlength="8" class="text" type="text" />
				<input type="hidden" id="source" name="source" value="header" />
				<input id="storesearchbutton" type="image" src="../images/search.gif" alt="Store search button" /></li>
			</ul>
		</form>
	<p class="storefinder_links"><a class="first" href="http://www.wholefoodsmarket.com/stores/all/">All stores</a> | <a href="http://www.wholefoodsmarket.com/stores/canada/">Canada stores</a> | <a href="http://www.wholefoodsmarket.com/stores/uk/">UK stores</a><br /><a class="first" href="http://www.wholefoodsmarket.com/careers/">Jobs at Whole Foods Market</a></p>	</div><!-- /storefinder -->

	<!-- Login Section -->
	<div class="account">
		<div id="headerDiv">
		<span class="welcome">Welcome, Guest</span><br />
<div id="login_container">
 <div id="topnav">
	<a href="http://www.wholefoodsmarket.com/login" class="signin"><span>Login / Register</span></a>
	 <p class="otherlinks"><a href="http://www.wholefoodsmarket.com/company/service.php" title="Contact Us">Customer Service</a></p>
	 <p class="otherlinks"><a href="http://www.wholefoodsmarket.com/newsletters/" title="Subscribe to our Email Newsletters" class="newsletter-header">Email Newsletters</a></p>
 </div><!--/topnav-->

 <fieldset id="signin_menu">
  <form method="post" id="signin" action="https://www.wholefoodsmarket.com/users/mlogin.php">
	<label for="email">Email</label>
	<input id="email" name="email" value="" title="Text: enter your Email address" type="text" />
	<p>
	 <label for="password">Password</label>
	 <input id="password" name="password" value="" title="Text: enter your password" type="password" />
	</p>
	<p class="remember">
	 <input id="signin_submit" value="Log In" type="submit" />
	 <a href="http://www.wholefoodsmarket.com/password/" id="forgot_pass_link" title="Request a password reset link be sent to your Email.">Forgot Password</a> | <a href="http://www.wholefoodsmarket.com/register/" id="register_link" title="Create new account for login to the Whole Foods Market site.">Register</a>
	 </p>
	 <p class="forgot-username">
	 	Other Logins: <a id="careers_link" title="Click here to update or check the status of your job application." href="http://www.wholefoodsmarket.com/careers/">Careers</a> | <a href="https://shop.wholefoodsmarket.com/store/myAccountLogin.aspx" id="ecommerce_link"  title="Click here to login to the holiday ordering and eCommerce system.">eCommerce</a>
	 </p>
  </form>
 </fieldset>
</div><!--/login_container-->

<script type="text/javascript">
if (! window.jQuery) {
	document.write('<'+'script src="/includes/libs/jquery-1.4.2.min.js" type="text/javascript"><'+'/script>');
}
</script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".signin").click(function(e) {
			e.preventDefault();
			 $("fieldset#signin_menu").toggle();
			 $(".signin").toggleClass("menu-open");
		});
		
		$("fieldset#signin_menu").mouseup(function() {
			return false
		});

		$(document).mouseup(function(e) {
			if($(e.target).parent("a.signin").length==0) {
			 $(".signin").removeClass("menu-open");
			 $("fieldset#signin_menu").hide();
			}
		});
	});
</script>
<script src="http://www.wholefoodsmarket.com/scripts/jquery.tipsy.js" type="text/javascript"></script>
<script type='text/javascript'>
	jQuery(function($) {
	 $('#forgot_pass_link').tipsy({gravity: 'e'});
	 $('#register_link').tipsy({gravity: 'w'});
	 $('#careers_link').tipsy({gravity: 'e'});
	 $('#newsletter_link').tipsy({gravity: 'w'});
	 $('#ecommerce_link').tipsy({gravity: 'w'});
	});
</script>
	</div><!--/headerDiv-->

	<div id="headerloginbox">
	</div><!--/headerloginbox-->
		
	<div id="loginStatus"></div>
	</div><!--/loginStatus-->
<!-- Login Section (End) -->

<div id="somed">
<h3 role="navigation">Talk to us:</h3>
<ul>
	<li><a href="http://www.wholefoodsmarket.com/twitter/">Twitter</a></li>
	<li><a href="http://www.wholefoodsmarket.com/facebook/">Facebook</a></li>
	<li><a href="http://www.flickr.com/photos/whole_foods/" rel="nofollow">Flickr</a></li>
	<li><a href="http://blog.wholefoodsmarket.com/">Our blog</a></li>
</ul>
</div><!--/somed-->

<div id="cpn-promo">
<!--	
	<h3>Gift Box</h3>
	<a class="imglink" href="/giftbox/"><img src="/images/giftbox-for-header.gif" alt="Gift boxes (US only)" width="60" height="65"></a>
-->

	<h3>Coupons</h3>
	<a class="imglink" href="http://www.wholefoodsmarket.com/coupons/"><img src="../images/coupons-for-header.gif" alt="Printable coupons (US only)" width="52" height="66" /></a>

</div><!--/cpn-promo-->

</div>
	</div><!-- /header -->
	
	<div class="navbar"><a id="navlinks"></a>
<div class="wrap" id="navwrap">
		<ul role="navigation">
			<li id="nav-home"><a href="http://www.wholefoodsmarket.com/">Home</a></li>
			<li id="nav-stores"><a href="http://www.wholefoodsmarket.com/stores/">Stores</a></li>
			<li id="nav-products"><a href="http://www.wholefoodsmarket.com/products/">Products</a></li>
			<li id="nav-recipes"><a href="http://www.wholefoodsmarket.com/recipes/">Recipes</a></li>
			<li id="nav-nutrition"><a href="http://www.wholefoodsmarket.com/healthstartshere/">Health Starts Here</a></li> 
			<li id="nav-values"><a href="index.html">Values</a></li>
			<li id="nav-company"><a href="http://www.wholefoodsmarket.com/company/">Company</a></li>
			<li id="nav-shop"><a href="http://www.wholefoodsmarket.com/shop/">Shop</a></li>
		</ul>
<form action="http://www.wholefoodsmarket.com/search/m/search.php" method="get" id="cse-search-box">
 <div role="search"><label for="sitesearch">Search Whole Foods Market Site</label>
  <input type="text" name="query" id="sitesearch" class="text" size="12" value="" />
  <input type="hidden" name="search" value="1" />
  <input type="image" name="sa" value="Search" src="../images/search.gif" />
 </div>
</form>

</div><!-- navwrap -->
	</div><!-- /navbar -->


<!-- ======================================= BEGIN PAGE CONTENT AREA ======================================= -->

	<div class="wrap">
	
<!-- ======================================= END PAGE CONTENT AREA ======================================= -->

	<div class="subnav"><a id="subnavlinks"></a>
	 
<h4>VALUES &amp; ACTIONS</h4>

<ul role="navigation">
<li id="currentpage">
<a href="http://www.wholefoodsmarket.com/values/index.php">Overview</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/corevalues.php">Core Values</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/whole-planet-foundation.php">Whole Planet Foundation</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/organic.php">Organic Food</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/green-mission.php">Green Mission</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/local-producer-loan-program.php">Local Producer Loan Program</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/seafood.php">Seafood Sustainability</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/genetically-engineered.php">Genetically Engineered Foods</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/sustainability.php">Sustainability &amp; Our Future</a></li>

<li>
<a href="http://www.wholefoodsmarket.com/values/giving.php">Community Giving</a></li>
		
<li>
<a href="http://www.wholefoodsmarket.com/values/parents-kids.php">Parents and Kids</a></li>
		
</ul>	</div><!-- /subnav -->

	</div><!-- /wrap -->
<div class="footernav">
	<div class="wrap" id="fnwrap">

	<ul class="footernav-links" role="navigation">
		<li id="footerstores"><h5>Stores</h5>
			<ul>
				<li><a href="http://www.wholefoodsmarket.com/stores/index.php">Find a Store</a></li>
				<li><a href="http://www.wholefoodsmarket.com/users/lists.php">Shopping Lists</a></li>
				<li><a href="http://www.wholefoodsmarket.com/stores/cooking-classes/">Cooking Classes</a></li>
				<li><a href="http://www.wholefoodsmarket.com/stores/catering/">Catering</a></li>
				<li><a href="http://www.wholefoodsmarket.com/giftcards/">Gift Cards</a></li>
			</ul>
		</li>
		<li id="footercareers"><h5>Careers</h5>
			<ul>
				<li><a href="http://www.wholefoodsmarket.com/careers/index.php">Search for Jobs</a></li>
				<li><a href="http://www.wholefoodsmarket.com/careers/workhere.php">Why Work Here?</a></li>
				<li><a href="http://www.wholefoodsmarket.com/careers/benefits.php">Benefits</a></li>
				<li><a href="http://www.wholefoodsmarket.com/careers/faq.php">Careers FAQ</a></li>
			</ul>
		</li>
		<li id="footerproducts"><h5>Products</h5>
			<ul>
				<li><a href="http://www.wholefoodsmarket.com/products/quality-standards.php">Quality Standards</a></li>
				<li><a href="http://www.wholefoodsmarket.com/products/wholedeal/">The Whole Deal</a></li>
				<li><a href="http://www.wholefoodsmarket.com/products/whole-trade.php">Whole Trade</a></li>
				<li><a href="http://www.wholefoodsmarket.com/products/food-safety.php">Food Safety</a></li>
				<li><a href="http://www.wholefoodsmarket.com/products/locally-grown/">Locally Grown</a></li>
			</ul>
		</li>
		<li id="footerblogs"><h5>Blogs &amp; Community</h5>
			<ul>
				<li><a href="http://blog.wholefoodsmarket.com">Whole Story Blog</a></li>
				<li><a href="http://www2.wholefoodsmarket.com/blogs/jmackey/">CEO John Mackey's blog</a></li>
				<li><a href="http://www.wholefoodsmarket.com/video/">Videos</a></li>
				<li><a href="http://blog.wholefoodsmarket.com/category/whole-body-podcast/">Whole Body Podcasts</a></li>
				<li><a href="http://www.wholefoodsmarket.com/forums/">Forums</a></li>
</ul>
		</li>
		<li id="footervalues"><h5>Values</h5>
			<ul>
				<li><a href="http://www.wholefoodsmarket.com/values/corevalues.php">Our Core Values</a></li>
				<li><a href="http://www.wholefoodsmarket.com/products/locally-grown/">Locally Grown</a></li>
				<li><a href="http://www.wholefoodsmarket.com/values/green-mission.php">Green Mission</a></li>
				<li><a href="http://www.wholefoodsmarket.com/values/whole-planet-foundation.php">Whole Planet Foundation</a></li>
				<li><a href="http://www.wholefoodsmarket.com/values/local-producer-loan-program.php">Local Producer Loan Program</a></li>
			</ul>
		</li>
		<li id="footercompany"><h5>Company Info</h5>
			<ul>
				<li><a href="http://www.wholefoodsmarket.com/company/history.php">History</a></li>
				<li><a href="http://www.wholefoodsmarket.com/company/leadership_board.php">Board of Directors</a></li>
				<li><a href="http://www.wholefoodsmarket.com/company/investor-relations.php">Investor Relations</a></li>
				<li><a href="http://www.wholefoodsmarket.com/pressroom/">Press Room</a></li>
				<li><a href="http://www.wholefoodsmarket.com/company/service.php">Customer Service</a></li>
			</ul>
		</li>
	</ul>
	
	<div class="clear"></div>
</div>	</div><!-- /footernav -->
	
	<div class="footer">
	<p role="navigation"><a href="http://www.wholefoodsmarket.com/">Home</a> |  <a href="http://blog.wholefoodsmarket.com/">Blog</a> | <a href="http://www.wholefoodsmarket.com/sitemap.php">Site Map</a> | <a href="http://www.wholefoodsmarket.com/users/terms.php">Terms of Use</a> | <a href="http://www.wholefoodsmarket.com/users/privacy.php">Privacy Policy</a> | <a href="http://www.wholefoodsmarket.com/users/subscribe.php">Email Subscriptions</a> | <a href="http://m.wholefoodsmarket.com/">Mobile Site</a>
</p>
<p role="contentinfo">Copyright @<script type="text/javascript">document.write(new Date().getFullYear());</script>. Whole Foods Market IP, L.P.</p>

<script src="http://www.wholefoodsmarket.com/scripts/gatag.js" type="text/javascript"></script>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-190385-1");
pageTracker._trackPageview();
</script>

<script src="http://www.wholefoodsmarket.com/scripts/ga-events.js?v=2" type="text/javascript"></script>
	</div><!-- /footer -->

</div><!-- /container -->

</body>
</html>
