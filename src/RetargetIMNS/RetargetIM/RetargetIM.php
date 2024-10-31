<?php
//header('Access-Control-Allow-Origin: http://dev.doc-internet.net');
//require('/wp-blog-header.php');
//require_once ABSPATH . '/wp-content/plugins/woocommerce/includes/class-wc-customer.php';
//require_once ABSPATH . '/wp-content/plugins/woocommerce/includes/class-wc-cart.php';


/**
* RetargetIMNS_RetargetIM_RetargetIM
*
* @uses
*
* @category RetargetIM
* @package  Package
* @author   Hadar Shpivak<Hadar.Shpivak@gmail.com>
* @license  Shpivak.co.il
* @link     http:// Shpivak.co.il
*/
class RetargetIMNS_RetargetIM_RetargetIM
{
    protected $wp;

    /**
     * __construct
     *
     * @param $facade \C3_Facade_WordPress Allows inserting a different facade object for testing.
     *
     * @access public
     *
     * @return void
     */
	 /*
    public function __construct()
    {
        $this->setFacade();
    }*/

    /**
     * setFacade
     *
     * @param $facade \C3_Facade_WordPress Allows inserting a different facade object for testing.
     *
     * @access public
     *
     * @return void
     */
	 /*
    public function setFacade(C3_Support_Facade_WordPress $facade = null)
    {
        $this->wp = ($facade) ? $facade : new C3_Support_Facade_WordPress();
    }
*/

    /**
     * initialize should take care of registering all hooks and actions. These
     * calls should be made through the WordPress Facade and not directly to
     * WordPress.
     *
     * @access public
     *
     * @return void
     */

	 
	 
	public function addFB()
	{
		
		echo '<div> Facebook Messenger!</div>';
		
	//	 echo '<div id="fb-root"></div>';
		 /*echo '<div class="fb-messengermessageus" 
  messenger_app_id="290168538044939" 
  page_id="154120788342419"
  color="blue"
  size="standard" >
</div> ';*/
		
	}
	
    public function rim_initialize()
    {
/*
		echo '<script src="http://cdn.jsdelivr.net/alertifyjs/1.8.0/alertify.min.js"></script>
<!-- CSS -->
<link rel="stylesheet" href="http://cdn.jsdelivr.net/alertifyjs/1.8.0/css/alertify.min.css"/>
<!-- Default theme -->
<link rel="stylesheet" href="http://cdn.jsdelivr.net/alertifyjs/1.8.0/css/themes/default.min.css"/>
<!-- Semantic UI theme -->
<link rel="stylesheet" href="http://cdn.jsdelivr.net/alertifyjs/1.8.0/css/themes/semantic.min.css"/>
<!-- Bootstrap theme -->
<link rel="stylesheet" href="http://cdn.jsdelivr.net/alertifyjs/1.8.0/css/themes/bootstrap.min.css"/>
<!--endOfSrc-->';
*/
		
		
       // $this->wp->add_action('wp_enqueue_scripts', array($this, 'enqueuePublicScripts'));
	   //echo "hello";
	   
	 //  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
	   {
    // Put your plugin code here
	
		function rim_wptuts_scripts_basic()
		{
			// Register the script like this for a plugin:
			//											src/NS/Name/
			wp_register_script( 'l-script', plugins_url( '/RetargetIM.js', __FILE__ ) );
		 
			// For either a plugin or a theme, you can then enqueue the script:
			wp_enqueue_script( 'l-script' );
		}
		add_action( 'wp_enqueue_scripts', 'rim_wptuts_scripts_basic' );
		add_action('wc_addFb', array($this, 'addFB'));
		
		
		//admin
	//	add_action(‘admin_menu’, ‘test_plugin_setup_menu’);
	//	add_menu_page( ‘Test Plugin Page’, ‘Test Plugin’, ‘manage_options’, ‘test-plugin’, ‘test_init’ );


	//	if ( is_user_logged_in() ) 
		
			// Current user is logged in,
			// so let's get current user info
		//	$current_user = wp_get_current_user();
			// User ID
		//	$user_id = $current_user->ID;
			//echo json_encode(42)
		//	echo $user_id;
		//$user = new WP_User(get_current_user_id());


		//FB
		
//		 echo '<div id="fb-root"></div>';

/*		echo 
'<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : \'290168538044939\',
             version    : \'v2.8\',
	   status: false,
		cookie: true,
		xfbml: true
    });
   // FB.AppEvents.logPageView();
  //};
   
FB.getLoginStatus(function(response) {
  if (response.status === \'connected\') {
     alert(\'connected\');
    var uid = response.authResponse.userID;
    var accessToken = response.authResponse.accessToken;
	alert (uid);
  } else if (response.status === \'not_authorized\') {
     alert(\'not_authorized\');
  } else {
    alert(\'not logged\');
  }
 });	
	   }; 
  
    (function(d){
        var js, id = \'facebook-jssdk\', ref = d.getElementsByTagName(\'script\')[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(\'script\'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        ref.parentNode.insertBefore(js, ref);
        }(document));

//onload =   FB.getLoginStatus(function(response) { alert(response);});

 </script>';
*/
	//		global $woocommerce;
	//	$cust = $woocommerce->customer;
	//	$cart = new WC_Cart();
	//	$cart = $woocommerce->cartt;
		//$cart.get_cart_from_session('fde65056d238aeb509c3416bd491be0b');
		//echo '<div id="tempCart">';
	//	echo "<script type='text/javascript'>alert('$cart');</script>";
		//echo $cart;
		//echo '</div';
	
	
	/*
			//$wp_user = wp_get_current_user();
		$user_id = get_current_user_id();
		$user = new WP_User(get_current_user_id());
		//$useri = get_currentuserinfo();
		//$wcuser =new WC_Cutomer();
		global $woocommerce;
		$cust = $woocommerce->customer;
		
		//$city = $cust->get_city();
		echo '<div id="UserDiv" >';
		echo $user_id;
//		echo var_dump($cust);
//		echo $cust->ID;
//		echo $cust->user_email;
		//echo $city;
		echo '</div>';
			*/
			
			/*
if ( ! class_exists( 'wcIntegClass' ) ) :
class wcIntegClass {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
	
	//* Initialize the plugin.
	
	public function init() {
		// Checks if WooCommerce is installed.
		if ( class_exists( 'wcIntegClass' ) ) {
			// Include our integration class.
			include_once 'class-wcInteg.php';
			// Register the integration.
			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		} else {
			// throw an admin error if you like
		}
	}
	
	 // Add a new integration to WooCommerce.
	 
	public function add_integration( $integrations ) {
		$integrations[] = 'wcInteg';
		return $integrations;
	}
}
$wcIntegClass = new wcIntegClass( __FILE__ );
endif;
	   */
    }
		
	}
	}
	 

    /**
     * enqueuePublicScripts
     *
     * @access public
     *
     * @return mixed Value.
     */
	 /*
    public function enqueuePublicScripts()
    {
        if (! $this->wp->wp_script_is('jquery', 'enqueue')) {
            wp_enqueue_script(
                'jquery',
                "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js",
                false,
                '1.8.3',
                true
            );
        }
    }
	*/
	
	
	?>
