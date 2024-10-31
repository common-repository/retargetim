<?php
/*
Plugin Name: RetargetIM – Automatic Personalized Notifications
Plugin URI: http://docs.RetargetIM.com
Description: A WooCommerce plugin that enables you to automatically serve your clients with personalized browser notifications, even when they're not on your site!
Version: 1.2.19
Author: Hadar Shpivak <hadar@RetargetIM.com>
Author URI: http://RetargetIM.com
License: MIT
*/
add_action('admin_menu', 'rim_plugin_setup_menu');
 
 //DB:
register_activation_hook( __FILE__, 'rim_Start' );
 
function rim_plugin_setup_menu(){
	
		rim_Start();
	
		$pageBeforeOrAfterInstall = 'rim_RetargetIM_Setup';
	//	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	//		$pageBeforeOrAfterInstall = 'rim_how_to_set_up';
        add_menu_page( 'RetargetIM', 'RetargetIM', 'manage_options', 'rim_RetargetIM_Setup', $pageBeforeOrAfterInstall );
		add_submenu_page('rim_RetargetIM_Setup', 'Dashboard', 'Dashboard', 'manage_options', 'rim_RetargetIM_DashBoard','rim_RetargetIM_DashBoard' );
		add_submenu_page('rim_RetargetIM_Setup', 'Site Setting', 'Site Setting', 'manage_options', 'rim_RetargetIM_Site_Settings','rim_RetargetIM_Site_Settings' );
		add_submenu_page('rim_RetargetIM_Setup', 'Opt-in message', 'Opt-in message', 'manage_options', 'rim_RetargetIM_Opt_In','rim_RetargetIM_Opt_In' );
		add_submenu_page('rim_RetargetIM_Setup', 'Push Notification', 'Push Notification', 'manage_options','rim_RetargetIM_Configuration','rim_RetargetIM_Configuration' );
		add_submenu_page('rim_RetargetIM_Setup', 'Send Push', 'Send Push', 'manage_options', 'rim_RetargetIM_CustomMessage','rim_RetargetIM_CustomMessage' );
		add_submenu_page('rim_RetargetIM_Setup', 'Plans', 'Plans', 'manage_options', 'rim_RetargetIM_Packages','rim_RetargetIM_Packages' );
		add_submenu_page('rim_RetargetIM_Setup', 'Support', 'Support', 'manage_options', 'rim_RetargetIM_Support','rim_RetargetIM_Support' );
		add_submenu_page('rim_RetargetIM_Setup', 'Info', 'Info', 'manage_options', 'rim_RetargetIM_Info','rim_RetargetIM_Info' );
		if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
			add_submenu_page('rim_RetargetIM_Setup', 'How To Set-Up', 'How To Set-Up', 'manage_options', 'rim_how_to_set_up','rim_how_to_set_up' );
		add_submenu_page('rim_RetargetIM_Setup', 'How To Congifure', 'How To Congifure', 'manage_options', 'rim_how_to_configure','rim_how_to_configure' );


}
function rim_dashboard_widget_function( $post, $callback_args ) {
	rim_getStats(true);
}

// function rim_used in the action hook
function rim_add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', 'RetargetIM Statistics', 'rim_dashboard_widget_function');
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'rim_add_dashboard_widgets' );
register_uninstall_hook(__FILE__, 'rim_uninstall_delete_db');

error_reporting(E_ERROR | E_PARSE);


$RIMServer = null;
$RIMWHMCSServer = 'https://whmcs.retargetim.com/whmcs/whmcs/';
$ServerAuth = null;
$secret = null;
$rowClient = null;
$currentPackageMessage = null;
$UsersPackageJson = null;
$defColor = null;
//$JSONobj = null;



function rim_Start()
{
	$GLOBALS['rowClient'] = rim_getDataFromDB();
	
		
	$localUrl = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/clientConf.json';
	
	ini_set("allow_url_fopen", 1);
	$json = file_get_contents($localUrl);
	$JSONobj = json_decode($json);
	$GLOBALS['RIMServer'] = $JSONobj->server;
	//$GLOBALS['RIMWHMCSServer'] = $JSONobj->whmcs;
	
	add_action( 'customize_register', 'rim_customize_register' );

	
	
	//case of update
	if(isset($GLOBALS['rowClient']) && !empty($GLOBALS['rowClient']) && isset($GLOBALS['rowClient']['code']) && isset($GLOBALS['rowClient']['enabled']))
	{
	//$localUrl ='/RetargetIM_wpp/src/RetargetIMNS/RetargetIM/clientConf.json';
		
		//get vals
		$GLOBALS['ServerAuth'] = $GLOBALS['rowClient']['code'];//$JSONobj->serverAdd;
		$GLOBALS['secret'] = $GLOBALS['rowClient']['secret'];//rim_getSecret($GLOBALS['ServerAuth']);
		/////////ADD CLIENT DETAILS
		
		//save to clientConf

		$newJSONObj = (json_decode($json));
			
		if(isset($GLOBALS['ServerAuth']))
			$newJSONObj->serverAdd = $GLOBALS['ServerAuth'];
		if(isset($GLOBALS['rowClient']['enabled']))
			$newJSONObj->settings[0]->Value/*Enabled*/  = $GLOBALS['rowClient']['enabled'];
		if(isset($GLOBALS['rowClient']['allSite']))
			$newJSONObj->settings[1]->Value/*Enabled*/  = $GLOBALS['rowClient']['enabled'];
		if(isset($GLOBALS['rowClient']['invitationTextHeader']))
			$newJSONObj->settings[2]->Value/*invitationTextHeader*/  = $GLOBALS['rowClient']['invitationTextHeader'];
		if(isset($GLOBALS['rowClient']['invitationTextBody']))
			$newJSONObj->settings[3]->Value/*invitationTextBody*/  = $GLOBALS['rowClient']['invitationTextBody'];
		if(isset($GLOBALS['rowClient']['invitationTextBodyMobile']))
			$newJSONObj->settings[4]->Value/*invitationTextBodyMobile*/  = $GLOBALS['rowClient']['invitationTextBodyMobile'];
		if(isset($GLOBALS['rowClient']['invitationTextFooter']))
			$newJSONObj->settings[5]->Value/*invitationTextFooter*/  = $GLOBALS['rowClient']['invitationTextFooter'];
		if(isset($GLOBALS['rowClient']['invitationColor']))
			$newJSONObj->settings[6]->Value/*invitationColor*/  = $GLOBALS['rowClient']['invitationColor'];
		if(isset($GLOBALS['rowClient']['invitationTextColor']))
			$newJSONObj->settings[7]->Value/*invitationTextColor*/  = $GLOBALS['rowClient']['invitationTextColor'];
		if(isset($GLOBALS['rowClient']['invitationBorderColor']))
			$newJSONObj->settings[8]->Value/*invitationBorderColor*/  = $GLOBALS['rowClient']['invitationBorderColor'];
		if(isset($GLOBALS['rowClient']['invitationHeadColor']))
			$newJSONObj->settings[9]->Value/*invitationHeadColor*/  = $GLOBALS['rowClient']['invitationHeadColor'];
	
		file_put_contents($localUrl, json_encode($newJSONObj));
	}

	
		function complete_install() {
			?>
			<div class="update-nag notice">
				<p><?php
				$completeUrl = admin_url().'admin.php?page=rim_RetargetIM_Setup';
				_e(' RetargetIM is activated, but needs one more click to be installed.</br>To complete installation <a href="'.$completeUrl.'">click Here </a>', 'retargetim' ); ?></p>
			</div>
			<?php
		}

		if(empty($GLOBALS['ServerAuth']) &&
		$_GET['page']!=('rim_RetargetIM_Setup'))
		{
			add_action( 'admin_notices', 'complete_install' );
		}
		
		ValidatePackage();
}	

function getCurrentPackageMessage()
{
	ValidatePackage();
	return $GLOBALS['currentPackageMessage'];
	
}
	function ValidatePackage()
	{					
		$localUrl = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/clientConf.json';
		$localJson = file_get_contents($localUrl);
		$JSONobj = json_decode($localJson);
		
		if ( isset($GLOBALS['ServerAuth']) && !empty($GLOBALS['ServerAuth']) )
//			&&(!isset($GLOBALS['currentPackageMessage']) || empty($JSONobj->plans->checkDate) || ((time()-(60*60*24)) > strtotime($JSONobj->plans->checkDate))))
		{			
			$headUrl = $GLOBALS['RIMServer'].'/'.$GLOBALS['ServerAuth'].'/Package';///SETUP/WC?url='.$fullURL;
			ini_set("allow_url_fopen", 1);
			$result = file_get_contents($headUrl);
			$response = json_decode( $result );	
			$GLOBALS['UsersPackageJson'] = $response;
			$JSONobj->plans->isEnabled = $response->isEnabled;
			$JSONobj->plans->checkDate = date("d/m/Y");
			
			file_put_contents($localUrl, json_encode($JSONobj));	
			
			
			
			//package message
			
			switch($response->Package)
			{
				case 0:
					$packageName = "Tasting Spoon (FREE TRIAL - up to 100 users)";
					break;
				case 2:
					$packageName = "Chocholate Scoop";
					break;
				case 22:
					$packageName = "Chocholate-Vanilla Scoops";
					break;
				case 32:
					$packageName = "Chocholate-Vanilla-Caramel Scoops";
					break;
				case 42:
					$packageName = "Chocholate-Vanilla-Caramel Scoops With Toppings";
					break;
					
			}
			$GLOBALS['currentPackageMessage'] = 
			'You are currently on <b>`'.$packageName.'`</b> plan.</br>'.
			'<b>Current Active Users:</b>'.$response->ActiveUsers .'/'.($response->MaxUsers).'</br></br>';
						
			if($response->Package == 0 && $response->ActiveUsers > $response->MaxUsers)//trial
			{
			//	echo date("Y/m/d",strtotime($response->TrialEndDate));
			//	echo date("Y/m/d");
				
				if(date("Y/m/d",strtotime($response->TrialEndDate))>date("Y/m/d"))
				{
				//		$dStart = new DateTime(date("d/m/Y"));
				//	   $dEnd  = new DateTime($response->TrialEndDate);
				//	   $dDiff = $dStart->diff($dEnd);
					   $dDiff = (strtotime($response->TrialEndDate) - strtotime(date("Y/m/d")))/(60*60*24);
					$GLOBALS['currentPackageMessage'] = 
					$GLOBALS['currentPackageMessage'] . 'You have '.$dDiff .' days until the free trial is over.</br>';
				}
				else
				{
					$GLOBALS['currentPackageMessage'] = 
					$GLOBALS['currentPackageMessage'] . 'Your free trial is over. Upgrade RetargetIM so you could reach more customers </br> ' ;
				}
			}
			else
			{
				if(100*intval($response->ActiveUsers)/intval($response->MaxUsers) > 75) //75%
				{
					$GLOBALS['currentPackageMessage'] = 
					$GLOBALS['currentPackageMessage'] . 'Consider upgrading your plan soon, so you could reach more customers</br>';
				}				
			}
			
		}
	}

function rim_RetargetIM_Setup()
{
	echo '<div dir="ltr" style="padding:10px">';

if (! class_exists( 'WooCommerce' ) ) 
{
	echo '<h1 style="color:red">WooCommerce is not installed!</h1><h2 style="color:red"> The RetargetIM platform is based on WooCommerce Plugin. Please install & configure your WooCommerce plugin - and procceed with RetargetIM</h2></font>';
	if (!admin_url().includes('https')) 
		echo '<h1 style="color:red">HTTPS is required!</h1><h2 style="color:red"> The RetargetIM platform requires a secure site. please contact your hosting provider to install an SSL certificate.</h2>';
	
}
else if (stripos(admin_url(),'https')===false) 
{
	echo '<h1 style="color:red">HTTPS is required!</h1><h2 style="color:red"> The RetargetIM platform requires a secure site. please contact your hosting provider to install an SSL certificate.</h2>';
}
else
{
	$siteName = get_bloginfo( 'name' ); 
//	 $siteName = str_replace(' ','__',get_bloginfo( 'name' )); 
	 $encoded = rawurlencode($siteName);
	 echo '</br>';
	if(strlen($siteName)!=strlen($encoded))//means the url encoded will fail (hebrew for example...
		$siteName = $_SERVER['SERVER_NAME'];
		
	$adminMail = get_option( 'admin_email' );
	$timeZone = get_option('gmt_offset');
	$storeCountry = WC_Countries::get_base_country();
	$callbackUrl = $GLOBALS['RIMServer'] .'/SETUP/WC?url=https://'.$_SERVER['SERVER_NAME'].'&siteName='. $siteName .'&adminMail='.$adminMail .'&timeZone='.$timeZone.'&storeCountry='.$storeCountry;
	$callbackUrl = str_replace('&','%26',$callbackUrl);
	$callbackUrl = str_replace(' ','__',$callbackUrl);
	//$newUrl = admin_url().'admin.php?page=rim_RetargetIM_Site_Settings';//.'%26initNew=1';
	$newUrl = admin_url().'admin.php?page=rim_RetargetIM_Info';//.'%26initNew=1';
	$randUserId = rand (1, 100 );

	
	if(!(strpos(explode('.co',$_SERVER['SERVER_NAME'])[0],'.') >0)) //don't have subdomain (www.)
		$fullSiteUrl = 'www.'.$_SERVER['SERVER_NAME'];
	else
		$fullSiteUrl = $_SERVER['SERVER_NAME'];

	
	$authURL = 'https://'.$fullSiteUrl.'/wc-auth/v1/authorize?app_name=RetargetIM&user_id='.$randUserId.'&scope=read&'.
	'return_url='.$newUrl.'%26initNew=1'.
	'&callback_url='.$callbackUrl;
		
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		echo '<h2>Thank you for installing RetargetIM</h2><h3> our on-boarding process is still new,
If you have any issues, open a ticket or mail us to <a href="mailto:support@retargetim.com">support@retargetim.com</a>, and get $14 from us, to use RetargetIM, for the incovinance.</h3>';
	
		echo '<img style="height:100px;margin-left: 10%;" src="'.plugin_dir_url(__FILE__).'/src/RetargetIMNS/RetargetIM/woocommerce_logo.png"/><br>
				<div id="iFrameDiv" style="overflow:hidden">	
			<iframe id="wc-auth-iframe" style="position:relative;top:-170px" width="800" height="800"'.
		//'style="position: relative; top: -280px;right:-100px;"'.
		'src="'.$authURL.'"></iframe></div>';
		
	}
	else
	{
		echo rim_RetargetIM_DashBoard();
	}
	echo '</div>';
	
	 global $wpdb;
	
	?>
	
	<script>

	var counter = 0;
	jQuery('iframe#wc-auth-iframe').load(function() {
		if(counter++ > 0) //only the second load...
		{//this.hide();
			window.location.href =  "<?php echo $newUrl.'&initNew=1' ?>";
		}
	});

	
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-91172121-2', 'auto');
	  ga('send', 'pageview');

	</script>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'ActivationPage');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>
	
	<?php
	
}
}

function rim_RetargetIM_Site_Settings(){
	
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{	
	echo '<div dir="ltr" style="padding:10px">';
	echo '<h1>Set your RetargetIM plugin</h1>';
	
	rim_initPlug();
	echo '</div>';
	
		?>
				
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-91172121-2', 'auto');
		  ga('send', 'pageview');

		
		</script>
		<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>

		
		<?php
	}
}

$customizationSettings = null;
function rim_customize_register( $wp_customize ) {
   //All our sections, settings, and controls will be added here
//$GLOBALS['customizationSettings'] = $wp_customize->get_setting('background_color');
$GLOBALS['customizationSettings'] = $wp_customize;//->get_setting('accent_color');
$GLOBALS['customizationSettings'] = 'hello';
//get_theme_mod
//echo 'testing colors!';

}
//add_action( 'customize_register', 'rim_customize_register' );

function BreakCSS($css)
{

    $results = array();

    preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
    foreach($matches[0] AS $i=>$original)
        foreach(explode(';', $matches[2][$i]) AS $attr)
            if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
            {
                list($name, $value) = explode(':', $attr);
                $results[$matches[1][$i]][trim($name)] = trim($value);
            }
    return $results;
}

function isColor($color)
{
if(strpos($color, 'transparent') !== false) //rgb ->hex
	return false;

if(strpos($color, 'rgb') !== false) //rgb ->hex
{
$charsToRemove = 4; //rgb(
	if(strpos($color, 'rgba') !== false)
{
		$charsToRemove = 5; //rgba(
}
	$color = substr($color,$charsToRemove);
	$tempArr = explode(',',$color);
	
	$newArr = array();
	for($i=0;$i<3;$i=$i+1)
		$newArr[$i] = dechex($tempArr[$i]);
	$color = join('',$newArr);
}
//echo 'the new color:'. $color;

if (strlen($color)==3) // 000 / fff
{
	$newColor = array();
	$newColor[0] = substr($color,0,1);
	$newColor[1] = substr($color,0,1);
	$newColor[2] = substr($color,1,1);
	$newColor[3] = substr($color,1,1);
	$newColor[4] = substr($color,2,1);
	$newColor[5] = substr($color,2,1);
	$color = join('',$newColor);
}
else if (substr($color,0,1)=='#')
	$color = substr($color,1);

$r = substr($color,0,2);
$g = substr($color,2,2);
$b = substr($color,4,2);

if($r == $g and $g==$b) //grey/black/white
	return false;
return true;
 //hex




}
function getDefColor()
{
$Color = '#000000';
	$allCss = (BreakCSS(file_get_contents(get_stylesheet_uri())));
	foreach ($allCss as $elem=>$style)
	{
		//echo 'elem:'. $elem . '======>' . var_dump($style).'</br>';
		foreach($style as $attr=>$val)
		{
			//echo 'attr:'. $attr . '======>' . ($val);
			if($attr == 'background-color')
			{	
				//echo $val . '<br/>';
				//isColor($val) ;
				//echo'<br/>';
				if(isColor($val))
				{	
					$Color = $val;
					break;
				}
			}
		
		}
	}
return $Color;
}

function rim_RetargetIM_Opt_In(){
	
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{
		echo '<div dir="ltr" style="padding:10px">';
		
		echo '<h1>Set your Opt-In Message</h1>';
		echo 'The opt-in message will appear to the user to encourage him to approve the push notification from your site. <br/>
See here an example of <a href="http://docs.retargetim.com/how-does-it-looks/">how it will look</a> and how to <a href="http://docs.retargetim.com/setting-the-opt-in-message/">set the opt-in message </a> to your needs <br/>
To block the opt-in message and invitation to use RetargetIM, you can uncheck the "enable" checkbox.<br/>
The opt-in message will not appear on the homepage, checkout, and purchase pages. <br/>
<i>The opt-in message will not appear in Safari browser (iOS). We will add this functionality soon</i>.<br/><br/>
';
		
/*
		echo '$image:';
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
		echo $image[0];
*/
		rim_createFormLocal();
		echo '</div>';
		?>
				
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-91172121-2', 'auto');
		  ga('send', 'pageview');

		
		</script>
		<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>

		
		<?php
	
	}
	
	
}

function rim_initPlug()
{
	$fullURL = 'https://'.$_SERVER['SERVER_NAME'];
	/*
	if(isset($_GET["initNew"]))	{
		
		echo '<p><h3> RetargetIM is ready! </h3></p>';
		$headUrl = $GLOBALS['RIMServer']. '/SETUP/WC?url='.$fullURL;
		ini_set("allow_url_fopen", 1);
		$result = file_get_contents($headUrl);
		
		$response = json_decode( $result );
	
		//CLIENT CONF
		$localUrl = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/clientConf.json';
		$json = file_get_contents( $localUrl);
		$JSONobj = json_decode($json);
		$JSONobj->serverAdd = $response->CODE;
		$GLOBALS['ServerAuth'] =  $response->CODE;
		$GLOBALS['secret'] = $response->SECRET;
		
		ini_set("allow_url_fopen", 1);
		$confUrl = $GLOBALS['RIMServer'] . '/SETUP/'. $response->CODE;
		$serverJson = file_get_contents($confUrl);
		$ServerJsonObj = json_decode($serverJson);
	//	$JSONobj->fcm = $ServerJsonObj->Bot->Tech->fcm;
		
		file_put_contents( $localUrl, json_encode($JSONobj));
	//	rim_SaveToDB($GLOBALS['ServerAuth'],$GLOBALS['secret']);
		rim_SaveToDB(json_encode($JSONobj),$GLOBALS['ServerAuth'],$GLOBALS['secret']);
		
	//	header("Location: ".
	//	admin_url().'admin.php?page=rim_RetargetIM_Site_Settings'
	//	); 
	//	exit();

	}*/
//	else
//	{
		
		if(isset($_POST["initSubmit"]))	{
		
		echo '<p><b>  Site data update succesfully</b></p>';
		
		$headUrl = $GLOBALS['RIMServer'] . '/SETUP/Head?url='.$fullURL.'&secret='.$GLOBALS['secret'];
		
		$json = json_encode($_POST);
		
		$options = array(
		  'http' => array(
			'method'  => 'POST',
			'content' => $json,
			'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
			)
		);
		ini_set("allow_url_fopen", 1);
		$context  = stream_context_create( $options );
		$result = file_get_contents($headUrl , false, $context );
		$response = json_decode( $result );
		
		}

		
//		if(isset($GLOBALS['ServerAuth']) && !empty($GLOBALS['ServerAuth']))//Create
		{
			
			echo 'The site name you will set will appear in the push notification to the user, </br>
					and the time-zone is the reference so we\'ll know when to send the notifications.';
		echo '<form method="post" >';
		echo '<table><tbody >';
		$url = $GLOBALS['RIMServer'] . '/SETUP/Head?url='.$fullURL.'&secret='.$GLOBALS['secret'];
		
		ini_set("allow_url_fopen", 1);
		$json = file_get_contents($url);
		$JSONobj = json_decode($json,false);
		$GLOBALS['ServerAuth'] = $JSONobj->CODE;
	
				
	//	echo '<tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;" />';	
	//	echo 'Site URL </td>';
		echo '<td> <input type="hidden" id="SITE_URL" name="SITE_URL" value="'.$JSONobj->SITE_URL.'"  style="width:320px"  style="width:320px" /><br/>';
		echo '</td></tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;" />';
		echo 'Site Name  </td><td> <input type="text" id="SITE_NAME" name="SITE_NAME" value="'.$JSONobj->SITE_NAME.'" style="width:320px"  style="width:320px" /><br/>';
		echo '</td></tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;" />';
		echo 'Admin E-Mail  </td><td> <input type="email" id="ADMIN_MAIL" name="ADMIN_MAIL" value="'.$JSONobj->ADMIN_MAIL.'"  style="width:320px"  style="width:320px" /><br/>';
		echo '</td></tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;" />';
		echo 'Store`s Time-Zone  </td><td>';
		
		
		rim_TimeZoneDropDown($JSONobj->TZ_CODE);
		//'<input type="number" id="TIME_ZONE" name="TIME_ZONE" value="'.$JSONobj->TIME_ZONE.'"  style="width:320px"  style="width:320px" /><br/>';
		
		echo '<input type="hidden" id="ADMIN_COUNTRY" name="ADMIN_COUNTRY" style="width:320px"  style="width:320px" />';
			?>
	 <script>
	jQuery.getJSON('https://freegeoip.net/json/', function(result) {
    jQuery('#ADMIN_COUNTRY').val(result.country_name);
	});
	</script>
	 <?php

		

	echo '</td></tr>';
	
	echo '</tbody></table>';
	echo '<input type="submit" class="button button-primary" id="initSubmit" name="initSubmit" value="Update" action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'"/><br/>';
	echo '</form>';
		
	}
}

function rim_getSecret($code)
{
	global $wpdb;
	$secret = $wpdb->get_var('SELECT secret FROM ' . $wpdb->prefix . 'rim_cs WHERE code= "'.$code.'"');
	return $secret;
}

function rim_getDataFromDB()
{
	rim_create_table();
	
	global $wpdb;
	
	$row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix .'rim_cs ', ARRAY_A );
	return $row;
}

//function rim_SaveToDB($code,$secret)
function rim_SaveToDB($json,$code,$secret)
{
	rim_create_table();
	$JSONObj = json_decode($json);

	global $wpdb;
	$exists = $wpdb->get_var( "SELECT COUNT(*) FROM ". $wpdb->prefix . 'rim_cs where code="'.$code.'"' );
	if($exists==0)
	{
		$wpdb->insert(
            $wpdb->prefix . 'rim_cs',
            array(
                'code'    => $code,
                'secret'   => $secret,
				'enabled' => $JSONObj->settings[0]->Value,//Enabled,
				'allSite' => $JSONObj->settings[1]->Value,//AllSite
				'invitationTextHeader' => $JSONObj->settings[2]->Value,//invitationTextHeader,
				'invitationTextBody' => $JSONObj->settings[3]->Value,//invitationTextBody,
				'invitationTextBodyMobile' => $JSONObj->settings[4]->Value,//invitationTextBodyMobile,
				'invitationTextFooter' => $JSONObj->settings[5]->Value,//invitationTextFooter
				'invitationColor' => $JSONObj->settings[6]->Value,//invitationColor,
				'invitationTextColor' => $JSONObj->settings[7]->Value,//invitationTextColor,
				'invitationBorderColor' => $JSONObj->settings[8]->Value,//invitationBorderColor
				'invitationHeadColor' => $JSONObj->settings[9]->Value//invitationHeadColor
            )
        );
	}
	else //exists - need to update
	{
		$wpdb->update( 
				$wpdb->prefix . 'rim_cs',
            array(
                'code'    => $code,
                'secret'   => $secret,
				'enabled' => $JSONObj->settings[0]->Value,//Enabled,
				'allSite' => $JSONObj->settings[1]->Value,//AllSite
				'invitationTextHeader' => $JSONObj->settings[2]->Value,//invitationTextHeader,
				'invitationTextBody' => $JSONObj->settings[3]->Value,//invitationTextBody,
				'invitationTextBodyMobile' => $JSONObj->settings[4]->Value,//invitationTextBodyMobile,
				'invitationTextFooter' => $JSONObj->settings[5]->Value,//invitationTextFooter
				'invitationColor' => $JSONObj->settings[6]->Value,//invitationColor,
				'invitationTextColor' => $JSONObj->settings[7]->Value,//invitationTextColor,
				'invitationBorderColor' => $JSONObj->settings[8]->Value,//invitationBorderColor
				'invitationHeadColor' => $JSONObj->settings[9]->Value//invitationHeadColor

		    ),
				array( 'code' => $code ), 
				null,
				null
			);
	}
}

function rim_create_table() {
        global $wpdb;


		
			if($wpdb->get_var("show tables like rim_cs") != 'rim_cs') {
//	$tblResult = mysql_query("show tables like ".$wpdb->prefix ."rim_cs");
//	$tblExists = (mysql_num_rows($result))?TRUE:FALSE;
//	if(!$tblExists) {
	
        $sql = 'CREATE TABLE ' . $wpdb->prefix . 'rim_cs (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
            code varchar(10) NOT NULL,
            secret varchar(10) NOT NULL,
			enabled tinyint(1) ,
			allSite tinyint(1) ,
			invitationTextHeader varchar(50) CHARACTER SET hebrew COLLATE hebrew_general_ci,
			invitationTextBody varchar(200)  CHARACTER SET hebrew COLLATE hebrew_general_ci,
			invitationTextBodyMobile varchar(200) CHARACTER SET hebrew COLLATE hebrew_general_ci,
			invitationTextFooter varchar(200) CHARACTER SET hebrew COLLATE hebrew_general_ci,
			invitationColor varchar(20),
			invitationTextColor varchar(20),
			invitationBorderColor varchar(20),
			invitationHeadColor varchar(20),
            UNIQUE KEY id (id)
			)
			CHARACTER SET hebrew COLLATE hebrew_general_ci;';

        if ( ! function_exists('dbDelta') ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }

        dbDelta( $sql );
	}
	else
	{
		$colFootResult = mysql_query("SHOW COLUMNS FROM `". $wpdb->prefix . "rim_cs` LIKE 'invitationTextFooter'");
		$colFootExists = (mysql_num_rows($colFootResult))?TRUE:FALSE;
		if(!$colFootExists) 
		{
		    $sql = 'ALTER TABLE ' . $wpdb->prefix . 'rim_cs 
			ADD invitationTextFooter varchar(200) NOT NULL AFTER `invitationTextBodyMobile;`';
		dbDelta( $sql );}
		
		
		$colHeadResult = mysql_query("SHOW COLUMNS FROM `". $wpdb->prefix . "rim_cs` LIKE 'invitationHeadColor'");
		$colHeadExists = (mysql_num_rows($colHeadResult))?TRUE:FALSE;
		if(!$colHeadExists) 
		{    $sql = 'ALTER TABLE ' . $wpdb->prefix . 'rim_cs 
			ADD invitationHeadColor varchar(200) NOT NULL AFTER `invitationBorderColor;`';
		dbDelta( $sql );}		
		
		
		$allSiteResult = mysql_query("SHOW COLUMNS FROM `". $wpdb->prefix . "rim_cs` LIKE 'allSite'");
		$allSiteExists = (mysql_num_rows($allSiteResult))?TRUE:FALSE;
		if(!$allSiteExists) 
		{
		    $sql = 'ALTER TABLE ' . $wpdb->prefix . 'rim_cs 
			ADD allSite tinyint(1) NOT NULL AFTER `enabled`;';
		dbDelta( $sql );}	
		
		
			$sql = $sql  . 'ALTER TABLE `wpet_rim_cs` 
			CHANGE `invitationTextHeader` `invitationTextHeader` VARCHAR(50) CHARACTER SET hebrew COLLATE hebrew_general_ci NULL DEFAULT NULL,
			CHANGE `invitationTextBody` `invitationTextBody` VARCHAR(50) CHARACTER SET hebrew COLLATE hebrew_general_ci NULL DEFAULT NULL,
			CHANGE `invitationTextBodyMobile` `invitationTextBodyMobile` VARCHAR(50) CHARACTER SET hebrew COLLATE hebrew_general_ci NULL DEFAULT NULL,
			CHANGE `invitationTextFooter` `invitationTextFooter` VARCHAR(50) CHARACTER SET hebrew COLLATE hebrew_general_ci NULL DEFAULT NULL 
			CHARACTER SET hebrew COLLATE hebrew_general_ci';
			
        //if ( ! function_exists('dbDelta') ) {
         //   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
        dbDelta( $sql );
		
	}
	//change collation to hebrew
//	$collationResult = mysql_query("show table status like 'wpet_rim_cs'");
//"show table status like `". $wpdb->prefix ."rim_cs`");
	//echo 'collation:'.$collationResult[0];
//	while($row = mysql_fetch_array($collationResult)) {
		//only once...
//        echo $row;
//      }
	
	
	//if($collationResult
//");
		
	
}

function rim_createForm()
{
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{	
	rim_media_selector_print_scripts();
		
	$url = $GLOBALS['RIMServer'].'/SETUP/'.$GLOBALS['ServerAuth'].'?secret='.$GLOBALS['secret'];
	if(isset($_POST['submitConf'])) 
	{
		echo '<p><b> Push Notification Configuration Updated!</b></p>';
		
		$json = json_encode($_POST);
		
		$options = array(
		  'http' => array(
			'method'  => 'POST',
			'content' => $json,
			'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
			)
		);

		$context  = stream_context_create( $options );
		$result = file_get_contents($url, false, $context );
		$response = json_decode( $result );
	}

	
	ini_set("allow_url_fopen", 1);
	$json = file_get_contents($url);
	
	$JSONobj = json_decode($json);
	$confArray = $JSONobj->Bot->Conf;
	
	echo '<h2>Please notice the notifications\' titles are limited to 30 characters and the body to 100 </h2>';
	$displayPreview = true;
//	addPreviewField();
	echo '<table><tr><td>';
	echo '<form id="ConfForm" method="post">';
	foreach($confArray as $confObj)
	{
		
		echo '<table><tr><td style="font-size:large;width:210px;"><div><b><br/>'.$confObj->Display .'   </b></td><td>';

		if( $confObj->Id!= 'Times' && $confObj->Id!= 'UTM')
			echo '<br/><button type="button" class="button button-primary previewButton" id="'.$confObj->Id .'">Preview</button></td>';
		echo '</tr></table>';
		echo '<table>';
		foreach($confObj->Values as $KVobj)
		{
			if(!empty($KVobj->Display))
			{
				echo '<tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;" />';
					if((!isset($KVobj->Value) || empty($KVobj->Value)) && $KVobj->Value!='0')
						echo '<b><font color="red">'.$KVobj->Display .': </font></b>';
					else
						echo $KVobj->Display .': ';
				echo '</td><td>';
				if($KVobj->Type=="DAY")
				{
					rim_WeekDayDropDown(($confObj->Id . '~~' . $KVobj->Id),$KVobj->Value);
				}			
				else{
					echo '<input type="';
						echo $KVobj->Type;
						echo '" id="';
						echo $confObj->Id . '~~' . $KVobj->Id;	
						echo '" name="';
						echo $confObj->Id . '~~' . $KVobj->Id;		
						echo '" value="';
						echo $KVobj->Value;
						echo '"';
						if($KVobj->Type=="checkbox")
						{ 
							if($KVobj->Value==1)
								echo ' checked ';
						}
						else
						{
							echo ' style="width:320px"';
						}
						
						if(stripos($KVobj->Id,"TITLE")!==false) //title (false or number)
						{
							echo ' maxlength="28" ';
						}
						else if(stripos($KVobj->Id,"BODY")!==false) //body (false or number)
						{
								echo ' maxlength="100" ';
						}
						
						echo ' /></br>';
				}
				echo '</td></tr>';
			}
		}
		
		echo '</table>';
	}
	echo '<input type="submit" class="button button-primary" name="submitConf" value="Update RetargetIM settings" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo '" /></form>';
	
	echo '</td><td valign="top" style="padding:20px">';
	addPreviewField(); 
	echo '</td></tr></table>';

	echo '<h4>Shortcode Usages:</h4>
	<b>[SiteName] </b>- Your site\'s name as defined in `Site Settings` Tab. </br>
	<b>[Prod] </b>- Product\'s name (taken from your WooCommerce store)</br>
	<b>[Price] </b>- Specific product\'s price (current <b>Regular</b> price from WooCommerce)</br>
	<b>[Stock] </b>- Current available quantity in stock for specific product (relevant for stock changes alert)
	</br>';
	
	?>
	<script>
	
	// demo notif
		if (document.getElementById("njsScript") == null) {
            var njsPath = "<?php echo 'https://'.$_SERVER['SERVER_NAME'].'/wp-content/plugins/retargetim/src/RetargetIMNS/RetargetIM/notify.min.js' ?> ";
            var serverPath = njsPath;

            var njs_src = document.createElement("script");
            njs_src.id = "njsScript";
            njs_src.src = serverPath;
            document.body.appendChild(njs_src);
        }
        document.getElementById("njsScript").onload = function (f) {
			
			//getProductForPreview();
			addPreviewButtonsListeners();
		}		
	//		popDemoNotif("Reminder from RetargetIM","Click Here !!!","demo.retargetim.com","https://shpivak.co.il/wp-content/uploads/2017/01/suit_b.jpg");
	//   }
	function addPreviewButtonsListeners()
	{
		var buttons = document.getElementsByClassName('previewButton')
		for(var i=0;i<buttons.length;i++)
		{
			buttons[i].addEventListener("click", function (e) {

                popDemoNotif(getPreviewValues(e.target.getAttribute('id')));
            });
		}
	}
	<!--<?php echo $_SERVER['SERVER_NAME']; ?>-->
	<!--<?php echo get_bloginfo( 'name' ); ?>-->
	
	function getPreviewValues(id)
	{
		var json = {site :"<?php echo $_SERVER['SERVER_NAME']; ?>" ,
		pic : "",
		name: "Product",
		price:'100',
		stock:'3',
		siteName:'<?php echo get_bloginfo( 'name' ); ?>'};
		
		json.name = jQuery('#preview_name').val();
		json.price = jQuery('#preview_price').val();
		json.stock = jQuery('#preview_stock').val();
		json.pic = jQuery('#icon').val();
		
		var elems = document.getElementsByTagName('input');
		for(var i=0;i<elems.length;i++)
		{	elem = elems[i];
			if(elem.getAttribute('name').includes(id) && elem.getAttribute('name').includes('TITLE'))
			{	
				json['title'] = elem.value;
				break;
			}
		}
		for(var i=0;i<elems.length;i++)
		{	elem = elems[i];
			if(elem.getAttribute('name').includes(id) && elem.getAttribute('name').includes('BODY'))
			{	
				json['body'] = elem.value;
				break;
			}
		}
		json.title = json.title.replace('[Prod]',json.name);
		json.title = json.title.replace('[Price]',json.price);
		json.title = json.title.replace('[Stock]',json.stock);
		json.title = json.title.replace('[SiteName]',json.siteName);
		
		json.body = json.body.replace('[Prod]',json.name);
		json.body = json.body.replace('[Price]',json.price);
		json.body = json.body.replace('[Stock]',json.stock);
		json.body = json.body.replace('[SiteName]',json.siteName);
		
		return json;
		//alert(JSON.stringify(json));
	//	popDemoNotif(json.title,json.body,json.site,json.pic);
		
	}
			
			function popDemoNotif(json){//title,body,site,pic){
				var title = "Reminder from RetargetIM";
				var body = "Click Here !!!";
				var site ="demo.retargetim.com";
				var pic = "https://shpivak.co.il/wp-content/uploads/2017/01/suit_b.jpg";
				var link = "";
				
				//
				var myHtml;
                        myHtml = "<div " ;
							if(jQuery('#adminmenuwrap').css('float')==='right') {myHtml += " style='right:"+jQuery('#adminmenuwrap').css('width')+"' " }
						myHtml +="><table><tr>"+
						"<td rowspan='3'>"+
							"<img height='80' width='80' src='"+json.pic+"'/>"+
						"</td><td><div style='font-size:large'><b>"+json.title+"</b></div></td>"+
						"</tr><tr><td>"+
							json.body+

							"</td></tr><tr><td>"+
							"<div style='font-size:small;color:grey'>"+json.site+"</div>"+					
							"</td></tr></table>"+
//                            "<span data-notify-text/>" +
                            "</div>";

                        var myCSSstyle =
                            {
                                html: myHtml,
                                classes: {
                                    base: {
										"position":"fixed",
										"height" : "100px",
										"bottom" : "0%",
										"right":   "0%",
										"width":   "400px",
										"padding": "5px",
										"margin": "5px",
										"background-color":"white",
										"box-shadow": "-1px -1px 20px"
                                    }
                                }
                            };

							//DEMO!
                        jQuery.notify.addStyle('RIMStyle', myCSSstyle);
						
			
			
				//	jQuery.notify("another test");
			setTimeout(function () {
				//bottom

		jQuery.notify("test",{ style: "RIMStyle", autoHide:false});
			},2000);
		}
		
		//end of demo notif
		
		
		jQuery("input[type=checkbox]").load(function(){

		if(this.checked)
			document.getElementById('ItemsInCart~~SPECIFIC_HOUR_OF_DAY').setAttribute("disabled","disabled")		
		else
			document.getElementById('ItemsInCart~~SPECIFIC_HOUR_OF_DAY').removeAttribute("disabled");
		});

	
	jQuery("input[type=checkbox]").change(function(){

		if(this.checked)
			document.getElementById('ItemsInCart~~SPECIFIC_HOUR_OF_DAY').setAttribute("disabled","disabled")		
		else
			document.getElementById('ItemsInCart~~SPECIFIC_HOUR_OF_DAY').removeAttribute("disabled");
		});
		
		/*jQuery("input[type=text]").unbind('keyup change input paste').bind('keyup change input paste',function(e){
			if(this.val().length>this.attr('maxlength')){
				this.val(this.val().substring(0,this.attr('maxlength')));	
				this.style.color = 'red';
			}
			else
				this.style.color = 'black';
	}); */
		
	</script>
	<?php 
}
}
function addPreviewField()
{
	echo '<b style="font-size:large">Edit your product example fields for previews: </b></br>
	<br>
	<table>
		<tr>
			<td style="font-weight:600;width:210px;">
				Name:
			</td>
			<td style = "width:320px">
				<input id="preview_name" name="preview_name" value="product name">
			</td>
		</tr>
		<tr>
			<td style="font-weight:600;width:210px;">
				Price:
			</td>
			<td style = "width:320px">
				<input id="preview_price" name="preview_price"  value="100">
			</td>
		</tr>
		<tr>
			<td style="font-weight:600;width:210px;">
				Stock:
			</td>
			<td style="width:320px;">
				<input id="preview_stock" name="preview_stock"  value="3">
			</td>
		</tr>
		<tr>
			<td style="font-weight:600;width:210px;">
				Picture:
			</td>
			<td>';
			
			if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['icon'] ) ) :
		update_option( 'media_selector_attachment_id', absint( $_POST['icon'] ) );
	endif;

	wp_enqueue_media();
		?>
	
	<div class='image-preview-wrapper'>
		 <img id='image-preview' src='
		 <?php if(isset($_GET['imageLink']))
			echo $_GET['imageLink'];
		?> 		 ' width='100' height='100' style='max-height: 100px; width: 100px;'>
	</br>	 
	<input id="upload_image_button" name='upload_image_button' type="button" class="button button-primary" value="<?php _e( 'Upload image' ); ?>" />
	<input type='hidden' name='icon' id='icon' value='
	<?php if(isset($_GET['imageLink']))
			echo $_GET['imageLink'];
	?>		'>
	</div>
	<?php
			echo '
				
			</td>
		</tr>
	</table>';
		
	
}

function rim_createFormLocal()
{	

	$localUrl = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/clientConf.json';
	$json = file_get_contents($localUrl);
	$JSONobj = json_decode($json);


	if(isset($_POST['ClientConf'])) 
	{
		echo '<p><b> Client UI Configuration Updated!</b></p>';

		$JSONobj = rim_getSettingsJson($JSONobj,$_POST);
		rim_SaveToDB(json_encode($JSONobj),$GLOBALS['ServerAuth'],$GLOBALS['secret']);
		
		file_put_contents($localUrl, json_encode($JSONobj));
	}
	
	echo 'You can set the text and the rules for each notification.  <br/>
You can leave the default fields or <a href="http://docs.retargetim.com/category/notification-setting/">click here</a> to read about all the push notification triggers. <br/>
All the push notifications will automatically use the product images from your store.<br/>
<br/>
';
	echo '<form method="post">';
	echo '<table>';

	foreach($JSONobj->settings as $KVobj)
	{
		echo '<tr><td style="width:210px;padding-top:20px;padding-bottom:20px">';
			if((!isset($KVobj->Value) || empty($KVobj->Value)) && $KVobj->Value!='0')
				echo '<b><font color="red">'.$KVobj->Display .' </font></b>';
			else
				echo $KVobj->Display ;
		echo '</td><td>';
		
				
				if($KVobj->Type == "checkbox")
					echo '<input type="hidden" value="0" name="'.$KVobj->Id .'" />';
				echo '<input type="';
				echo $KVobj->Type;
				echo '" id="';
				echo $KVobj->Id;	
				echo '" name="';
				echo $KVobj->Id;
				echo '" value="';
				//html_entity_decode
				//htmlspecialchars
				echo ($KVobj->Value);
				echo '"';
		
				if($KVobj->Type == "checkbox" && $KVobj->Value==1)
					echo ' checked ';
			
				if($KVobj->Type == "text")
					echo 'style="width:640px"';
				echo '/></br>';

		echo '</td></tr>';
	}
	echo '</table>';
	echo '<input type="submit"  class="button button-primary" value="Save opt-in message" name="ClientConf" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo '" />';
	
	echo'</form>';
	
	echo '</br><b>Desktop Preview:</b></br>';
	
	
	$BorderColor = 'black';
	$BackColor = 'white';
	$TextColor = 'black';
	$HeadColor = 'black';
	$InvitationTextHeader = 'InvitationTextHeader';
	$InvitationTextBody = 'InvitationTextBody';
	$footerText = 'Footer';
	
	echo 'Don\'t forget to save your changes!</br></br>';
/*	echo "<div id='preview' style='background-color: ".$BackColor.";border-style:solid;border-color:" . $BorderColor . ";padding:20px;border-radius: 10px;border-width:4px;width:550px'>" .
                                "<div id='previewheader' style='background-color:" . $BorderColor . ";color:" . $BackColor . ";border-radius: 10px;text-align:center;font-weight: bold;font-size:x-large;padding:5px;'>" .
                                $InvitationTextHeader .
                                "</div>" .
                                "<div id ='previewbody' style='font-size:large;padding:5px;;color:".$TextColor."' > ".$InvitationTextBody."</div>" .
                                "<div id='previewfooter' style='font-size:small;color:".$TextColor."'>"
                                . $footerText .
                                "</div></div>";
	*/
	$dir = 'ltr';
	$margindir = 'left';
	if(get_locale() == 'he_IL')
	{
		$dir = 'rtl';
		$margindir = 'right';
	}
	
echo "<div id='preview' dir=".$dir." style=';border-style:solid;border-color:" .$BorderColor .";padding:20px;border-radius: 5px;border-width:6px;width:675px;height:220px'> ".
							"<div id='xColorPreview' style='background-color:".$BackColor .";text-align:center;font-size:x-large;width:25px;margin-$margindir
							:101%;margin-top:-5%;padding:5px;border-style:solid;border-color:" .$BorderColor .";color:".$HeadColor."'><b>X</b></div>".
                            "<div id='previewheader' style='color:" .$HeadColor . ";text-align:center;font-weight: bold;padding:10px;font-size:40px;'>" .
                            $InvitationTextHeader .
                            "</div>" .
                            "<div id='previewbody' style='font-size:xx-large;padding:20px;line-height:110%;color:".$TextColor."' >" . $InvitationTextBody ."</div>".
                            "<hr>".
							"<div id='previewfooter' style='font-size:medium;padding:20px;margin-top: -2%;margin-bottom: -1%;'>"
                            .$InvitationTextFooter.
                            "</div></div>";	
			
	?>
	<script>
	
	jQuery("input[type=checkbox]").change(function(){
		if(this.checked)
		{		this.value = 1;
				this.addAttribute("checked");
				jQuery('#CBToggle').value = 1;
		}
		else
		{
			this.value = 0;
			this.removeAttribute("checked");
			jQuery('#CBToggle').value = 0;
		}
	});
	
//	jQuery(document).load(function(){
	jQuery( document ).ready(function() {
		jQuery('#previewheader').text(jQuery("#invitationTextHeader").val()); 
		jQuery('#previewbody').text(jQuery("#invitationTextBody").val()); 
		jQuery('#previewfooter').text(jQuery("#invitationTextFooter").val());

	jQuery('#preview').css("background-color",jQuery("#invitationColor").val())
						.css("border-color",jQuery("#invitationBorderColor").val());
	jQuery('#previewheader').css('color',jQuery("#invitationHeadColor").val());
	jQuery('#previewbody').css('color',jQuery("#invitationTextColor").val());
	jQuery('#previewfooter').css('color',jQuery("#invitationTextColor").val());
	jQuery('#xColorPreview').css('background-color',jQuery("#invitationColor").val())
							.css('margin-<?php echo $margindir; ?>','100%')
							.css('border-color',jQuery("#invitationBorderColor").val())
							.css('color',jQuery("#invitationHeadColor").val());
			
/* 
		jQuery('#preview').attr("style",
		'background-color: '+jQuery("#invitationColor").val()+
		';border-style:solid;border-color:'+jQuery("#invitationBorderColor").val()+';padding:20px;border-radius: 10px;border-width:4px;width:550px'
		); 
		jQuery('#previewheader').attr("style",
		
		'color:'+jQuery("#invitationHeadColor").val()+';font-size:xx-large;');
		
		jQuery('#previewbody').attr("style",
		'font-size:large;padding:5px;color:'+jQuery("#invitationTextColor").val()
		); 
		jQuery('#previewfooter').attr("style",
		'font-size:small;color:'+jQuery("#invitationTextColor").val()
		);
		
		jQuery('#xColorPreview').attr("style",
		'background-color: '+jQuery("#invitationColor").val()+
		';text-align:center;width:25px;margin-<?php echo $margindir .':100%'; ?>;margin-top:-5%;border-style:solid;border-color:'+jQuery("#invitationBorderColor").val()
		);
*/		
		
	});
	jQuery("#invitationTextHeader").change(function(){
		jQuery('#previewheader').text(jQuery("#invitationTextHeader").val()); 
		
	});
	jQuery("#invitationTextBody").change(function(){
		jQuery('#previewbody').text(jQuery("#invitationTextBody").val()); 
	});
	jQuery("#invitationTextFooter").change(function(){
		jQuery('#previewfooter').text(jQuery("#invitationTextFooter").val()); 
	});
	
	jQuery("#invitationColor").change(function(){
			jQuery('#preview').css('background-color',jQuery("#invitationColor").val())
							.css('border-color',jQuery("#invitationBorderColor").val());

	/*jQuery("#invitationColor").change(function(){
		jQuery('#preview').attr("style",
		'background-color: '+jQuery("#invitationColor").val()+
		';border-style:solid;border-color:'+jQuery("#invitationBorderColor").val()+';padding:20px;border-radius: 10px;border-width:4px;width:550px'
		);
*/ 
		/*
		jQuery('#previewheader').attr("style",
		'color:'+jQuery("#invitationColor").val()+
		';border-radius: 10px;text-align:center;font-weight: bold;font-size:x-large;height:30px;padding:5px;'
		); */
		
	});
	
	jQuery("#invitationBorderColor").change(function(){
		jQuery('#preview').css('background-color',jQuery("#invitationColor").val())
						.css('border-color',jQuery("#invitationBorderColor").val());

	jQuery('#xColorPreview').css('margin-<?php echo $margindir; ?>','100%')
							.css('border-color',jQuery("#invitationBorderColor").val())
							.css('background-color',jQuery("#invitationColor").val());
	

	/*jQuery("#invitationBorderColor").change(function(){
		jQuery('#preview').attr("style",
		'background-color: '+jQuery("#invitationColor").val()+
		';border-style:solid;border-color:'+jQuery("#invitationBorderColor").val()+';padding:20px;border-radius: 10px;border-width:4px;width:550px'
		);
*//*
	jQuery('#xColorPreview').attr("style",'text-align:center;width:25px;margin-<?php echo $margindir . ':100%';?>;margin-top:-5%;border-style:solid;border-color:'+jQuery("#invitationBorderColor").val() + 
		';background-color: '+jQuery("#invitationColor").val());
	*/	
	/*		
		jQuery('#previewheader').attr("style",
		'background-color:'+jQuery("#invitationBorderColor").val()+
		';color:'+jQuery("#invitationColor").val()+
		';border-radius: 10px;text-align:center;font-weight: bold;font-size:x-large;height:30px;padding:5px;'
		);
	*/	
	});
	
	jQuery("#invitationTextColor").change(function(){
		jQuery('#previewbody').css('color',jQuery("#invitationTextColor").val()); 
		jQuery('#previewfooter').css('color',jQuery("#invitationTextColor").val());
		/*
		jQuery('#previewbody').attr("style",
		'font-size:large;padding:5px;color:'+jQuery("#invitationTextColor").val()
		); 
		jQuery('#previewfooter').attr("style",
		'font-size:small;color:'+jQuery("#invitationTextColor").val()
		);*/ 
	});
		jQuery("#invitationHeadColor").change(function(){
		
		/*
		jQuery('#previewheader').attr("style",
		'color:'+jQuery("#invitationHeadColor").val()+';font-size:xx-large'
		); 
		*/		
		jQuery('#previewheader').css('color',jQuery("#invitationHeadColor").val()); 
		jQuery('#xColorPreview').css('color',jQuery("#invitationHeadColor").val());
				
	});
	
	
	</script>
	<?php
	
}

function rim_getSettingsJson($OrigJson,$settingsJson)
{
	foreach($settingsJson as $key => $value)
	{
		foreach($OrigJson->settings as $setObj)
		{
			if($setObj->Id == $key)
				{
					$setObj->Value = htmlspecialchars(clearQuotes($value));
				}
		}
	}
	return $OrigJson;
}

function clearQuotes($text)
{
	return str_replace('\\\'','\'',str_replace('\"','"',$text));
}

function rim_CustomMessages()
{	

	echo '<h2> Send you own push notifications</h2>';

	$url = $GLOBALS['RIMServer'].'/Custom/'.$GLOBALS['ServerAuth'];
	if(isset($_GET['productID']) && isset($_GET['productName']))
		$url = $url . '?prod='.$_GET['productID'] ;//.'&name='. $_GET['productName'] ;
	
	if(isset($_POST['submitCustom'])) 
	{
		
		$json = json_encode($_POST);
		
		$options = array(
		  'http' => array(
			'method'  => 'POST',
			'content' => $json,
			'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
			)
		);

		$context  = stream_context_create( $options );
		$result = file_get_contents($url, false, $context );
		$response = json_decode( $result );
				echo '<p><b>'.$response->count.' Messages Sent!</b></p>';
		
		//echo json_decode($_POST);
		
	}
	
	
//	else
	// Save attachment ID
	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['icon'] ) ) :
		update_option( 'media_selector_attachment_id', absint( $_POST['icon'] ) );
	endif;

	wp_enqueue_media();

	
	ini_set("allow_url_fopen", 1);
	$json = file_get_contents($url);
	
	$JSONobj = json_decode($json);
	$confArray = $JSONobj->Bot->Conf;
	
	//echo '</b>';
	echo '<table>';
	echo '<form id="CustomForm" method="post">';

	if(isset($_GET['productID']))
	{
		echo 'Send push notifications to the users who engaged with '.$_GET['productName'].'</br>';
		
		echo '<input type="hidden" name="to" value="/topics/'.$GLOBALS['ServerAuth'].'_'.$_GET['productID'].'"/></br>';
		echo '<input type="hidden" name="prodName" value="'.$_GET['productName'].'"/></br>';
	}
	else
	{
		echo 'Send push notifications to all active users</br>';
	}
	echo '<tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;">Message Title </td><td> <input type="text" style="width:320px" name="title"';
		if(isset($_GET['productName']))
			echo ' value="'.$_GET['productName'].' @ '.get_bloginfo( 'name' ).'"';
		
	echo '/></td><tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;"> Message Body </td><td> <input type="text" size="100" style="width:320px" name="body"';
		if(isset($_GET['productName']))
			echo ' value="have a another look at the '.$_GET['productName'].'. just click"';
		
	echo '/></td><tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;"> Link on Click </td><td> <input type="text" style="width:320px" name="click_action"';
		if(isset($_GET['productLink']))
			echo ' value="'.$_GET['productLink'].'"';
		
	echo '/></td><tr><tr><td style="width:210px;padding-top:20px;padding-bottom:20px;font-weight:600;"> Image </td><td> ';//<input type="text" name="icon"/></br>';
	?>
	
	<div class='image-preview-wrapper'>
		 <img id='image-preview' src='
		 <?php if(isset($_GET['imageLink']))
			echo $_GET['imageLink'];
		?> 		 ' width='100' height='100' style='max-height: 100px; width: 100px;'>
	</br>	 
	<input id="upload_image_button" type="button" class="button button-primary" value="<?php _e( 'Upload image' ); ?>" />
	<input type='hidden' name='icon' id='icon' value='
	<?php if(isset($_GET['imageLink']))
			echo $_GET['imageLink'];
	?>		'>
	</div>
	<?php
	
	
	echo '</td></tr><tr><td>
	<input type="submit"  value="SEND!" name="submitCustom" class= "button button-primary" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo '" ></input></td></tr></form>';
	
	echo '</table>';
	//echo '</b>';

}

//add_action('wp_head', 'rim_media_selector_print_scripts');
//add_action( 'admin_footer', 'rim_media_selector_print_scripts' );
function rim_media_selector_print_scripts() {
	$my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
	?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
			jQuery('#upload_image_button').on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#icon' ).val((attachment.url).replace("http://","https://"));
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script><?php
}

function rim_RetargetIM_Configuration()
{
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{
		echo '<div dir="ltr" style="padding:10px">';		
		rim_createForm();
		echo '</div>';
		
		?>
				
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-91172121-2', 'auto');
			  ga('send', 'pageview');

			
			</script>
		<?php
	}
}

function rim_RetargetIM_DashBoard()
{
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{
		echo '<div dir="ltr" style="padding:10px">';		

	echo '<h1 style="text-align:center">RetargetIM Statistics</h1>';
	rim_getStats(false);
echo '<hr>';
	//$url = $GLOBALS['RIMServer'].'/DashBoard/demo/Products';

	$url = $GLOBALS['RIMServer'].'/DashBoard/'.$GLOBALS['ServerAuth'].'/Products';
	
	$to = date("Y-m-d",strtotime("+1 day",strtotime(date("Y-m-d"))));
	$from =  date("Y-m-d",strtotime("-1 month",strtotime($to)));
	$refresh = 0;
	if(isset($_GET['refresh']))
		$refresh = $_GET['refresh'];
	if(isset($_GET['from']) && isset($_GET['to']))
	{
		$to = date("Y-m-d",strtotime($_GET['to']));
		$from = date("Y-m-d",strtotime($_GET['from']));
		//$refresh = 1; - happens automatically from the code
	}	
	$url = $url . '?from=' .$from. '&to='.$to .'&refresh='.$refresh;

	ini_set("allow_url_fopen", 1);
	$json = file_get_contents($url);
	$JSONobj = json_decode($json);
		
	echo '<h3 style="text-align:center"> Products Details </h3>';	
	
	echo '<form id="datesFrom" method="GET">';
	echo '<b>Filter By Dates : </b>';
	echo '<input type="hidden" name = "page"  value ="rim_RetargetIM_DashBoard" />';
	echo 'from: <input type="date" name="from" value="'.$from.'"/>';
	echo 'to: <input type="date" name="to" value="'.$to.'"/> ';
	//echo '<input type="hidden" name="refresh" value="1"/>';
	echo '<input type="submit" class="button button-primary" id="dateForm" value="Filter" action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'"/>';
	echo '<input type="submit" class="button button-primary" id="dateForm" value="Refresh All" action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'"/></form>';
	echo 'Notice that filtering by dates only filter the users & notifications, not the products <br>/';
	echo '`Refresh All` takes a bit more time to gathered all your data,which automatically updated hourly.';
	
	echo '<table id="dashboardTable" class="widefat fixed" cellspacing="0">
    <thead>
    <tr>

           <th id="prodId" class="manage-column column-id num" scope="col" width="30">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(0)><u>ID</u></button></th>
           <th id="prodPic" class="manage-column column-pic" scope="col" width="70">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(1)><u>Thumbnail</u></button></th>
			<th id="prodName" class="manage-column column-name" scope="col" width="120">
			<button style="background: white;border: 0;color:blue" onclick=sortTable(2)><u>Name</u></button></th>
			
			<th id="prodUsers" class="manage-column column-users num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(3)><u>Engaged</u></button></th>
			
			<!--	<th id="prodView" class="manage-column column-view num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(4)><u>Viewed</u></button></th> -->
		
				<th id="prodCard" class="manage-column column-cart num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(4)><u>Cart abandonment</u></button></th>
		
		<!--	<th id="prodNotif" class="manage-column column-notif num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(5)><u>Push Sent</u></button></th> -->
				
				<th id="prodNotif" class="manage-column column-notif num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(5)><u>Reminders  Sent</u></button></th>
				
				<th id="prodNotif" class="manage-column column-notif num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(6)><u> Updates Sent</u></button></th>
				
				<th id="prodNotifOpened" class="manage-column column-notif num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(7)><u>Total Push Opened</u></button></th>
			
				<th id="prodInc" class="manage-column column-inc num" scope="col" width="95">
				<button style="background: white;border: 0;color:blue" onclick=sortTable(8)><u>Direct Sales</u></button></th>
			
		<th id="prodUpdateDate" class="manage-column column-date" scope="col" width="210">
		<button style="background: white;border: 0;color:blue" onclick=sortTable(9)><u>Last Update Date</u></button></th>
			

    </tr>
    </thead>

    <tbody>';
	$i=0;
	foreach($JSONobj->data as $prod)
	{
		//echo '<tr><td>';
	
    echo '
        <tr';
		if($i%2==0)
			echo ' class="alternate" ' ;
		echo 'valign="top"> 
            <th scope="row">'.$prod->PROD_ID .'</th>
			<td > <img height="50" width="60" src="'.$prod->IMAGE .'"/> </td>
            <td class="column-columnname">
			<b><a href="'.admin_url().'/post.php?post='.$prod->PROD_ID .'&action=edit">'.$prod->NAME .'
                </a></b><div class="row-actions">
                  <!--  <span><a href="#">View</a> |</span> -->
                    <span><a href="'.admin_url().'admin.php?page=rim_RetargetIM_CustomMessage&productID='.$prod->PROD_ID .'&productName='.$prod->NAME .'&productLink='.$prod->LINK_TO_PROD .'&imageLink='.$prod->IMAGE .'">Send Notification</a></span>
                </div>
            </td>	
            <td>'.$prod->USERS_ACT .'</td> 
		<!--	<td>'.$prod->USERS_VIEW .'</td> -->
			
			<td>'.$prod->USERS_CART .'</td>		
		<!--	<td>'.$prod->NOTIF_SENT .'</td>-->
			<td>'.$prod->REMINDER_SENT .'</td>
			<td>'.$prod->ALERTS_SENT .'</td>
			<td>'.$prod->NOTIF_OPENED .'</td>
			
			<td>';
//			if($prod-> USERS_BOUGHT == 0) {echo '0';}
//			else{echo (100*$prod-> USERS_BOUGHT_RIM)/$prod->USERS_BOUGHT;} 
		
			echo $prod-> USERS_BOUGHT_RIM . '</td>

				<td>'.$prod->DATE_MODIFIED .'</td>
        </tr>';
		$i = $i+1;
	}
		
	echo '		
        </tr>

    </tbody>
</table>';
		echo '</div>';

	?>
			
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-91172121-2', 'auto');
	  ga('send', 'pageview');

	
	</script>
	<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>
<script>

function sortTable(col) {
  var table, rows, switching, i, x, y, shouldSwitch;
  table = jQuery("#dashboardTable")[0];// document.getElementById("dashboardTable");
  switching = true;
  var asc = 0;
  var change =false;
  
  while (asc!==2)
  {
	  while (switching) {
		//start by saying: no switching is done:
		switching = false;
		rows = table.rows; ;//.getElementsByTagName("TR");
	//	Loop through all table rows (except the
	//	first, which contains table headers):
		for (i = 1; i < (rows.length - 1); i++) {
		  //start by saying there should be no switching:
		  shouldSwitch = false;
	//	  Get the two elements you want to compare,
	//	  one from current row and one from the next:
		  x = rows[i].cells[col] ;//.getElementsByTagName("TD")[col];
		  y = rows[i + 1].cells[col];//.getElementsByTagName("TD")[col];
		  //check if the two rows should switch place:
		  if ((x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() && asc==0)  ||
		  (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() && asc==1))
		  {
			//if so, mark as a switch and break the loop:
			shouldSwitch= true;	
			change=true;
			break;
		  }
		}
		if (shouldSwitch) {
		//  If a switch has been marked, make the switch
		//  and mark that a switch has been done:
		  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
		  switching = true;
		}
	  }
	  //if there we're no change - try asc.
	  //on second try - if nothing cahnge - finish (asc=2)
	  if(change ==false && asc==0)
		{	
			asc =1;
			switching = true;
		}
		else
			asc =2;
  }
}
/*
function sortTable(col) {
  var table, rows, switching, i, x, y, shouldSwitch,asc=0;
  table = document.getElementById("dashboardTable");
  switching = true;
 // Make a loop that will continue until
 // no switching has been done:
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
  //  Loop through all table rows (except the
  //  first, which contains table headers):
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
  //    Get the two elements you want to compare,
  //    one from current row and one from the next:
      x = rows[i].getElementsByTagName("TD")[col];
      y = rows[i + 1].getElementsByTagName("TD")[col];
      //check if the two rows should switch place:
      if ((x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() && asc ==0) ||
			(x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() && asc ==1))
		{
        //if so, mark as a switch and break the loop:
        shouldSwitch= true;
        break;
      }
    }
    if (shouldSwitch) {
  //    If a switch has been marked, make the switch
  //    and mark that a switch has been done:
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
    }
  }
}
*/
</script>
	
	<?php
	//echo '<iframe src="'.$GLOBALS['RIMServer'].'/Dashboard/miniDash.html?code='.$GLOBALS['ServerAuth'].'" width="100%" height="700px"></iframe>';
	}
}

function rim_getStats($isMainDash)
{
	$url = $GLOBALS['RIMServer'].'/DashBoard/'.$GLOBALS['ServerAuth'].'/Main';

	ini_set("allow_url_fopen", 1);
	$json = file_get_contents($url);
	$JSONobj = json_decode($json);
	$users = $JSONobj->data->TOTAL ;
	$notif = $JSONobj->data->NOTIF_SENT;
	$inc = $JSONobj->data->INCREASE;
	if(!isset($inc))
		$inc = 0;
	
	if($isMainDash)
	{
		echo getCurrentPackageMessage();
		//echo "<b>Total Active Users:</b> ".$users." </br>";
		//echo "<b>Package:</b> ".$package;
		echo "<button class=\"button button-primary\" onclick = \"window.location.href = '".admin_url()."admin.php?page=rim_RetargetIM_Packages';\">Upgrade Package</button></br>";
		echo "<hr>";
		echo "<b>Total Notifications sent:</b> ".$notif."</br>";
		echo "<b>RetargetIM direct sale:</b> ".$inc."% </br>";
		echo "<button class=\"button button-primary\" onclick = \"window.location.href = '".admin_url()."admin.php?page=rim_RetargetIM_DashBoard';\" 
		>view all statistics</button></br>";
	}
	else
	{
		/*
		echo "<h3>Total Active Users: $users </h3>";
		echo "<b>you are currently on the $package package.</b><br>";
		if($leftToUp>0)
			echo "after the next $leftToUp users, you'll need to upgrade. <br> click here (and you'll pay only when you reach your current package maximum).</br>"; 
		else
			echo "your users quota has excceeded,click here to get more users </br>"; 
		
		echo getCurrentPackageMessage();

		$GLOBALS['UsersPackageJson']
		
		echo "<button class=\"button button-primary\" onclick = \"window.location.href = '".admin_url()."admin.php?page=rim_RetargetIM_Packages';\">Upgrade Package</button></br>";
		echo "<h3>RetargetIM has sent a total of $notif notifications </h3>";
		if($inc >0)
			echo "<h3><b>which increased your sales in $inc% </b></br></h3>";
		echo "<br>";
		*/
		getCurrentPackageMessage();
		getUsersTable($JSONobj->data); //echos the users,package,and notifications sent
		
			
	}
}

function getUsersTable($data)
{
	echo '<table style="background-color:white;width:80%">';
		echo '<tr>';
			echo '<th colspan="2" style="text-align: left;">';
				echo '<h2> Users and notifications statistics</h2>';
				echo '<hr>';
			echo '</th>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Active Users';
			echo '</td>';
			echo '<td>';
				echo $GLOBALS['UsersPackageJson']->ActiveUsers;
			echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Max users in current package';
			echo '</td>';
			echo '<td>';
				echo $GLOBALS['UsersPackageJson']->MaxUsers;
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Upgrade';
			echo '</td>';
			echo '<td>';
				echo '<a href="'.admin_url().'admin.php?page=rim_RetargetIM_Packages">';
					echo 'Upgrade Package';
				echo '</a>';
			echo '</td>';			
		echo '</tr>';
		
			echo '<tr><th colspan="2"><hr></th></tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Total notification sent';
			echo '</td>';
			echo '<td>';
				echo $data->NOTIF_SENT;
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Total notification clicked';
			echo '</td>';
			echo '<td>';
				echo $data->NOTIF_CLICKED;//clicked
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Notification CTR';
			echo '</td>';
			echo '<td>';
				if($data->NOTIF_SENT==0)
					echo '0%';
				else
					echo number_format(100*floatval($data->NOTIF_CLICKED) / floatval($data->NOTIF_SENT),2) .'%';
												//clicked
			echo '</td>';			
		echo '</tr>';
		
			echo '<tr><th colspan="2"><hr></th></tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Unique users notification sent';
			echo '</td>';
			echo '<td>';
				echo $data->U_NOTIF_SENT;
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Unique users notification clicked';
			echo '</td>';
			echo '<td>';
				echo $data->U_NOTIF_CLICKED;//clicked
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Unique users notification CTR';
			echo '</td>';
			echo '<td>';
				if($data->U_NOTIF_SENT==0)
					echo '0%';
				else
					echo number_format(100*floatval($data->U_NOTIF_CLICKED) / floatval($data->U_NOTIF_SENT), 2) .'%';
												//clicked
			echo '</td>';			
		echo '</tr>';
		
			echo '<tr><th colspan="2"><hr></th></tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Total invitations popped';
			echo '</td>';
			echo '<td>';
				echo $data->INVITES;
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Total invitations approved';
			echo '</td>';
			echo '<td>';
				echo $data->TOTAL;//clicked
			echo '</td>';			
		echo '</tr>';
		
		echo '<tr>';
			echo '<td style="width:360px">';
				echo 'Opt-In rate';
			echo '</td>';
			echo '<td>';
				if($data->INVITES==0)
					echo '0%';
				else
					echo number_format(100*floatval($data->TOTAL) / floatval($data->INVITES),2) .'%';
												//clicked
			echo '</td>';			
		echo '</tr>';
		
		
		
		
	echo '</table>';
	
	
}

function rim_RetargetIM_CustomMessage()
{
	if(!isset($GLOBALS['ServerAuth']) || empty($GLOBALS['ServerAuth']))
	{
		header("Location: ".
		admin_url().'admin.php?page=rim_how_to_set_up' 	); 
		exit();
	}
	else
	{
		rim_media_selector_print_scripts();
		
		echo '<div dir="ltr" style="padding:10px">';
		rim_CustomMessages();
		echo '</div>';
		?>
				
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-91172121-2', 'auto');
			  ga('send', 'pageview');

			
			</script>
		<?php
	}
}



function rim_RetargetIM_Packages()
{
	//echo "<h3> Our billing platform will start working in June 1st, 2017. Until this date, the platform will be free for all the users </h3></br>";
		
	echo '<h1> RetargetIM pricing: </h1>';
	
	echo getCurrentPackageMessage();
	
	echo '<h3>If you are using a different mail than the one you edited on `Site Settings` tab,
	please insert your site\'s URL  in "Notes / Additional Information". </h3>';
	//<h4>if you are not sure - insert</h4>';
	
	echo '<div style="width: 1100px; height: 1500px; overflow: hidden">';
	echo '<iframe src="'.$GLOBALS['RIMWHMCSServer'].'cart.php" width="1100" height="1500"
	style="position: relative; top: -280px"></iframe>';
	echo '</div>';
	
	/*
	?>
	<script>
	document.getElementsByTagName("iframe")[0].onload = function(){
	if(document.getElementsByClassName("form-control")[0] != null )
	{
		addURLtoInfo(document.getElementsByClassName("form-control")[0]);
	}
	};
	
	function addURLtoInfo(e)
	{
		
		var val = "";
		val = val + e.value;
		val = val + 'https://'+document.location.host;
		alert(val);
		e.getElementsByClassName("form-control")[0].value = val;
		
	}
	</script>
	<?php
	*/
/******will the table below relevent?********/	
	
	$strJson = '
	{
		"packages":
		[
		{
			"name":"Free Trial",
			"maxUsers": "100",
			"price":"free",
			"pic":"",
			"maxNotif":2
		},
		{
			"name":"Starter",
			"maxUsers": "2,500",
			"price":"14.90$",
			"pic":"",
			"maxNotif":2
		},
		{
			"name":"Growing",
			"maxUsers": "5,000",
			"price":"24.90$",
			"pic":"",
			"maxNotif":10
		},
		{
			"name":"Bigger",
			"maxUsers": "10,000",
			"price":"44.90$",
			"pic":"",
			"maxNotif":20
		},
		{
			"name":"Big",
			"maxUsers": "20,000",
			"price":"74.90$",
			"pic":"",
			"maxNotif":40
			
		},
		{
			"name":"Enterprise",
			"maxUsers": "20K +",
			"price":"contact us",
			"pic":"",
			"maxNotif":100
		}
		]
	}';
	/*
	$json = json_decode($strJson);
	echo '<table class="widefat fixed" cellspacing="0">
    <thead>
		<tr>
		<th class="manage-column " scope="col" width="10">Pick</th>
            <th class="manage-column " scope="col" width="40">Package Name</th>
            <th class="manage-column " scope="col" width="30"> Automatic Push Notification</th>
			<th class="manage-column " scope="col"width="36">Responsive to eCommerce on-site changes</th>
			<th class="manage-column " scope="col" width="30" >Auto URL Tagging (Google Analytics)</th>
			<th class="manage-column " scope="col" width="30">Manual push notification to all users</th>
			<th class="manage-column " scope="col" width="30">Manual push notification to product</th>
			<th class="manage-column " scope="col" width="30">Max weekly manual push notification </th>
			<th class="manage-column " scope="col" width="30">first month FREE</th>
			<th class="manage-column " scope="col" width="30">Max Active Users</th>
			<th class="manage-column " scope="col" width="30">Monthly fees</th>
			<th class="manage-column " scope="col" width="36">cancel anytime and get one month refund</th>
		</tr>
		</thead>';
		
	$i=1;
	foreach($json->packages as $package)
	{
		
		echo 
		"<tr ";
		$i=$i+1;
		if($i%2==0)
			echo ' class="alternate" ' ;
		echo ">
		<td><input size='1' type='radio' id='".$package->name."' name='packageradio'></td>
				<td> <b>" .$package->name . "</b></td>" .
				"<td>V</td>".
				"<td>V</td>".
				"<td>V</td>".
				"<td>V</td>".
				"<td>V</td>".
				"<td>".$package->maxNotif."</td>".
				"<td>V</td>".
				"<td>".$package->maxUsers ."</td>".
				"<td>".$package->price."</td>".
				"<td>V</td>".
			"</tr>";
	
	}
	echo '</table>';
	echo "</br> <button class='button button-primary' id=" . $package->name . ">Upgrade </button> " ;
	
	echo "</br></br><b>
	 Active user is a user who opted-in to RetargetIM by clicking “Allow”</b></br></br>";
	echo "<b>The eCommerce on-site changes includes:</b>
	<li> Sale starts push notifications
	<li> Sale ends soon push notifications
	<li> Out of stock push notifications
	<li> Last items in stock push notifications
	<li> Back to stock push notifications
	<li> Price changed push notifications";
*/
	?>
				
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-91172121-2', 'auto');
		  ga('send', 'pageview');

		</script>
		
		<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>

	<?php
	
}


function rim_how_to_set_up()
{
	echo '<h1>How to start using RetargetIM</h1>';
	echo '<iframe width="853" height="480" src="https://www.youtube.com/embed/_FcZ3BtH--k?start=51" frameborder="0" allowfullscreen></iframe>';
	echo '</br><p style="font-size:xx-large;text-align:center"><b><a href="'.admin_url().'admin.php?page=rim_RetargetIM_Setup">Let\'s go</a></b></p>';
	
	?>
	<!-- Facebook Pixel Code -->
<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '549574391841010'); // Insert your pixel ID here.
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
		/></noscript>
		<!-- DO NOT MODIFY -->
		<!-- End Facebook Pixel Code -->
		<!-- Google Code for Remarketing Tag -->
		<!--------------------------------------------------
		Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
		--------------------------------------------------->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 853335502;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
		</div>
		</noscript>

	<?php
}

function rim_initNewCompleteSetUp()
{
		echo '<div style="text-align:center;font-size:150%" dir="ltr">
				<p><h1> RetargetIM is all set! </h1></p>
		</br>
		<p> <h2> That\'s it.All done.</br></h2></p>
			<br>
		<p> <h4> and now we wait for the customers. we will take care of it.</br></h4></p>
				<br><br>
		<img height="300" src="'.plugin_dir_url(__FILE__).'/src/RetargetIMNS/RetargetIM/logo.png"/><br><br><br>
		You can edit your <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Opt_In">Opt-In message</a> </br></p>';
		
	
		$fullURL = 'https://'.$_SERVER['SERVER_NAME'];
		$headUrl = $GLOBALS['RIMServer']. '/SETUP/WC?url='.$fullURL;
		ini_set("allow_url_fopen", 1);
		$result = file_get_contents($headUrl);
		
		$response = json_decode( $result );
	
		//CLIENT CONF
		$localUrl = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/clientConf.json';
		$json = file_get_contents( $localUrl);
		$JSONobj = json_decode($json);
		$JSONobj->serverAdd = $response->CODE;
		$GLOBALS['ServerAuth'] =  $response->CODE;
		$GLOBALS['secret'] = $response->SECRET;
		
		ini_set("allow_url_fopen", 1);
		$confUrl = $GLOBALS['RIMServer'] . '/SETUP/'. $response->CODE;
		$serverJson = file_get_contents($confUrl);
		$ServerJsonObj = json_decode($serverJson);
	//	$JSONobj->fcm = $ServerJsonObj->Bot->Tech->fcm;
		
		$JSONobj = initOptIn($JSONobj);

		//SAVE TO DB AND CONF_FILE
		file_put_contents( $localUrl, json_encode($JSONobj));
		rim_SaveToDB(json_encode($JSONobj),$GLOBALS['ServerAuth'],$GLOBALS['secret']);
		
		
		//SEND TO SERVER THE STORE'S NAME
		$headUrl = $GLOBALS['RIMServer'] . '/SETUP/Head?url='.$fullURL.'&secret='.$GLOBALS['secret'].'&name=1';
		
		//$json = json_encode($_POST);
		
		$json = '{"SITE_NAME" : "'.get_bloginfo( 'name' ).'"}';
		
		$options = array(
		  'http' => array(
			'method'  => 'POST',
			'content' => $json,
			'header'=>  "Content-Type: application/json\r\n" .
						"Accept: application/json\r\n"
			)
		);
		ini_set("allow_url_fopen", 1);
		$context  = stream_context_create( $options );
		file_get_contents($headUrl , false, $context );
//		$response = json_decode( $result );
		

		
	
}

function initOptIn($json)
{
	$userCountry = WC_Countries::get_base_country();
	//LANG - check for file with the country name;	
	$langPath = dirname(__FILE__).'/src/RetargetIMNS/RetargetIM/lang.json';
	$langJson = file_get_contents( $langPath);
	$langJsonObj = json_decode($langJson);
	foreach($langJsonObj as $country => $texts)
	{
		if($userCountry==$country)
		{
			foreach($texts as $row)
			{
				foreach ($json->settings as $jsonTextRow)
				{
					if($jsonTextRow->Id == $row->Id)
						$jsonTextRow->Value = $row->Value;
				}

			}
		}
	}

	//COLORS
	$defColor = getDefColor();
	foreach ($json->settings as $kvobj)
	{
		if($kvobj->Id == 'invitationHeadColor' or $kvobj->Id == 'invitationBorderColor' )
		{
			$kvobj->Value = $defColor;
		}
	}

	return $json;
}

function rim_how_to_configure()
{
	echo '<h3>Check out these simple steps to further optimize your RetargetIM for you</h3>';
	echo 
	'<ol>
		<li> Change your site\'s name and time-zone at <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Site_Settings">Site Settings</a>
		<li> Edit your <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Opt_In">Opt-In message</a>
		<li> Set the <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Configuration">Push Notification Configuration</a>
		<li> View your statistics at the  <a href="'.admin_url().'admin.php?page=rim_RetargetIM_DashBoard">Dashboard</a>
		<li> Send Customized mass notifications with <a href="'.admin_url().'admin.php?page=rim_RetargetIM_CustomMessage">Send Push</a>			
		<li> Upgrade RetargetIM so you could reach more customers at  <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Packages">Plans</a>
		<li> If you have questions you can contact us or open a ticket at <a href="'.admin_url().'admin.php?page=rim_RetargetIM_Support">Support</a>
	</ol>';
	
	echo '<iframe width="853" height="480" src="https://www.youtube.com/embed/_FcZ3BtH--k?start=126" frameborder="0" allowfullscreen></iframe>';

	?>
	<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
fbq('track', 'AfterActivation');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>

	
	
	<?php
	
	}

function getCurrentPlan()
{
	echo 'You are currently on `Free Trial` plan. </br>';
	//TODO: get from server...
}

function rim_RetargetIM_Support()
{
	
	echo '<h1> Support </h1>';
	
	echo "<h3> You can read our <a href='http://docs.retargetim.com/retargetim-faq/'>FAQ</a> about the product, or read the documentation site - <a href='https://docs.retargetim.com'>https://docs.retargetim.com </a>.
<br/><br/>".
//"Feel free to e-mail us - 
//<a href='mailto:support@RetargetIM.com' > support@RetargetIM.com </a>".
	"</h3></br>";
	
	
	echo '<div style="width: 1100px; height: 1500px; overflow: hidden">';
	echo '<iframe src="'.$GLOBALS['RIMWHMCSServer'].'submitticket.php" width="1100" height="1300"
	style="position: relative; top: -280px;"> Please wait while our ticketing system starts</iframe>';
	echo '</div>';
	
	
	?>
			
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-91172121-2', 'auto');
	  ga('send', 'pageview');

	
	</script>
	<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '549574391841010'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=549574391841010&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 853335502;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/853335502/?guid=ON&amp;script=0"/>
</div>
</noscript>

	<?php
	
}

function rim_RetargetIM_Info()
{
	if(isset($_GET["initNew"]))	{
		rim_initNewCompleteSetUp();
	}
	else 
	{
		echo '<p style="white-space: pre-wrap;"><h2>RetargetIM is a simple and automated push messaging platform for WooCommerce stores.</h2><h3> Drive traffic back to your store with relevant offers, with zero effort. </h3> <br/>	<b> <br/>Key product benefits:</b> <br/>-	Form free with high opt-in rates (10%+ on average) <br/>-	Engaging placement for the push messages like the mobile notification bar <br/>-	Fully automated  <br/>-	Machine learning algorithms <br/>-	Simple 1-Click installation <br/>-	No coding needed <br/>-	Out-of-the-box solution with optional advanced setting to your needs <br/>-	Fully integrated with the WooCommerce CMS  <br/>-	Free tier with affordable plans for larger stores, 1 month free trial and money back guaranty.  <br/> <br/><b>How does it work?</b> <br/>After the user will opt-in by clicking "Allow" in the browser, RetargetIM will learn his activity in the site and together with the store data – will find the most relevant messages to send him. As the site owner you can sit back and enjoy more sales. <br/> <br/><b>Plugin functionality:</b> <br/>-	Sending automatic push notification according the user activity and actions in the WooCommerce store. <br/>-	Identifying engagement with specific products using machine learning. <br/>-	Optimizing the messages according to your store activity  <br/> <br/><b>Current messaging options:</b> <br/>-	Reminder of products the user engaged with <br/>-	Price drop on engaged items <br/>-	Special sales on engaged items <br/>-	Sale ends in 1 day on engaged items <br/>-	Last items in stock on engaged items <br/>-	Product back on stock on engaged items <br/> <br/>In addition – you will be able to send (up to twice a week) messages to all the registered users. <br/> <br/>The site admin can choose to use all the functions or only part of them, and change specific settings (for example – when will the last items on stock message will appear) <br/> <br/><b>Platforms supported:</b> <br/>-	Chrome (desktop & mobile) <br/>-	Firefox (desktop & mobile) <br/>-	Opera (desktop & mobile) <br/> <br/><b>Pricing</b> <br/>You will pay only for users who opt-in to receive push notification from your eCommerce site (Active users)<br/><br/>Up to 100 active users: FREE <br/>Up to 2,500 active users: $14 a month <br/>Up to 5,000 active users: $24 a month <br/>Up to 10,000 active users: $44 a month <br/>Up to 20,000 active users: $74 a month <br/> **First month free for the paid packages. No need for credit card to start using the paid packages** <br/>*Money back guaranty – if you would like to cancel at any time – send us a message from the plugin and we will pay you back for current month. No question asked* <br/>	</p>';
		echo '</br> <b>Click Here to read the <a href="https://docs.retargetim.com">docs</a> , or <a href="mailto:support@RetargetIM.com">contact us</a>';
	}
	
}

function rim_uninstall_delete_db()
{
	global $wpdb;
	
//	$sql = 'DELETE FROM ' . $wpdb->prefix . 'rim_cs;';
//	if ( ! function_exists('dbDelta') ) {
//		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//	}

	$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix ."rim_cs");
	 
}


function rim_WeekDayDropDown($name,$value)
{
	echo '<select style= "width:320px" NAME='.$name.'>
		<option value="0"';
		if($value==0)
			echo 'selected=1' ;
		echo '>Sunday</option>
		<option value="1"';
		if($value==1)
			echo 'selected=1' ;
		echo '>Monday</option>
		<option value="2"';
		if($value==2)
			echo 'selected=1' ;
		echo '>Tuesday</option>
		<option value="3"';
		if($value==3)
			echo 'selected=1' ;
		echo '>Wednesday</option>
		<option value="4"';
		if($value==4)
			echo 'selected=1' ;
		echo '>Thursday</option>
		<option value="5"';
		if($value==5)
			echo 'selected=1' ;
		echo '>Friday</option>
		<option value="6"';
		if($value==6)
			echo 'selected=1' ;
		echo '>Saturday</option>
	</select>';
}

function rim_TimeZoneDropDown($value)
{
	echo '<select style="width:320px" id="TIME_ZONE" name="TIME_ZONE">
	<option timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-12">(GMT-12:00) International Date Line West</option>
	<option timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-11">(GMT-11:00) Midway Island, Samoa</option>
	<option timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-10">(GMT-10:00) Hawaii</option>
	<option timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-9">(GMT-09:00) Alaska</option>
	<option timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Pacific Time (US & Canada)</option>
	<option timeZoneId="6" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-8">(GMT-08:00) Tijuana, Baja California</option>
	<option timeZoneId="7" gmtAdjustment="GMT-07:00" useDaylightTime="0" value="-7">(GMT-07:00) Arizona</option>
	<option timeZoneId="8" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
	<option timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-7">(GMT-07:00) Mountain Time (US & Canada)</option>
	<option timeZoneId="10" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Central America</option>
	<option timeZoneId="11" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Central Time (US & Canada)</option>
	<option timeZoneId="12" gmtAdjustment="GMT-06:00" useDaylightTime="1" value="-6">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
	<option timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-6">(GMT-06:00) Saskatchewan</option>
	<option timeZoneId="14" gmtAdjustment="GMT-05:00" useDaylightTime="0" value="-5">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
	<option timeZoneId="15" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Eastern Time (US & Canada)</option>
	<option timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-5">(GMT-05:00) Indiana (East)</option>
	<option timeZoneId="17" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Atlantic Time (Canada)</option>
	<option timeZoneId="18" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Caracas, La Paz</option>
	<option timeZoneId="19" gmtAdjustment="GMT-04:00" useDaylightTime="0" value="-4">(GMT-04:00) Manaus</option>
	<option timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-4">(GMT-04:00) Santiago</option>
	<option timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-3.5">(GMT-03:30) Newfoundland</option>
	<option timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Brasilia</option>
	<option timeZoneId="23" gmtAdjustment="GMT-03:00" useDaylightTime="0" value="-3">(GMT-03:00) Buenos Aires, Georgetown</option>
	<option timeZoneId="24" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Greenland</option>
	<option timeZoneId="25" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-3">(GMT-03:00) Montevideo</option>
	<option timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-2">(GMT-02:00) Mid-Atlantic</option>
	<option timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-1">(GMT-01:00) Cape Verde Is.</option>
	<option timeZoneId="28" gmtAdjustment="GMT-01:00" useDaylightTime="1" value="-1">(GMT-01:00) Azores</option>
	<option timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0">(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
	<option timeZoneId="30" gmtAdjustment="GMT+00:00" useDaylightTime="1" value="0">(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
	<option timeZoneId="31" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
	<option timeZoneId="32" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
	<option timeZoneId="33" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
	<option timeZoneId="34" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
	<option timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="1">(GMT+01:00) West Central Africa</option>
	<option timeZoneId="36" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Amman</option>
	<option timeZoneId="37" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Athens, Bucharest, Istanbul</option>
	<option timeZoneId="38" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Beirut</option>
	<option timeZoneId="39" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Cairo</option>
	<option timeZoneId="40" gmtAdjustment="GMT+02:00" useDaylightTime="0" value="2">(GMT+02:00) Harare, Pretoria</option>
	<option timeZoneId="41" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
	<option timeZoneId="42" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Jerusalem</option>
	<option timeZoneId="43" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Minsk</option>
	<option timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="2">(GMT+02:00) Windhoek</option>
	<option timeZoneId="45" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
	<option timeZoneId="46" gmtAdjustment="GMT+03:00" useDaylightTime="1" value="3">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
	<option timeZoneId="47" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Nairobi</option>
	<option timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="3">(GMT+03:00) Tbilisi</option>
	<option timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="3.5">(GMT+03:30) Tehran</option>
	<option timeZoneId="50" gmtAdjustment="GMT+04:00" useDaylightTime="0" value="4">(GMT+04:00) Abu Dhabi, Muscat</option>
	<option timeZoneId="51" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Baku</option>
	<option timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="4">(GMT+04:00) Yerevan</option>
	<option timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="4.5">(GMT+04:30) Kabul</option>
	<option timeZoneId="54" gmtAdjustment="GMT+05:00" useDaylightTime="1" value="5">(GMT+05:00) Yekaterinburg</option>
	<option timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="5">(GMT+05:00) Islamabad, Karachi, Tashkent</option>
	<option timeZoneId="56" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Sri Jayawardenapura</option>
	<option timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="5.5">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
	<option timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="5.75">(GMT+05:45) Kathmandu</option>
	<option timeZoneId="59" gmtAdjustment="GMT+06:00" useDaylightTime="1" value="6">(GMT+06:00) Almaty, Novosibirsk</option>
	<option timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="6">(GMT+06:00) Astana, Dhaka</option>
	<option timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="6.5">(GMT+06:30) Yangon (Rangoon)</option>
	<option timeZoneId="62" gmtAdjustment="GMT+07:00" useDaylightTime="0" value="7">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
	<option timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="7">(GMT+07:00) Krasnoyarsk</option>
	<option timeZoneId="64" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
	<option timeZoneId="65" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Kuala Lumpur, Singapore</option>
	<option timeZoneId="66" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
	<option timeZoneId="67" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Perth</option>
	<option timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="8">(GMT+08:00) Taipei</option>
	<option timeZoneId="69" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
	<option timeZoneId="70" gmtAdjustment="GMT+09:00" useDaylightTime="0" value="9">(GMT+09:00) Seoul</option>
	<option timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="9">(GMT+09:00) Yakutsk</option>
	<option timeZoneId="72" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Adelaide</option>
	<option timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="9.5">(GMT+09:30) Darwin</option>
	<option timeZoneId="74" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Brisbane</option>
	<option timeZoneId="75" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Canberra, Melbourne, Sydney</option>
	<option timeZoneId="76" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Hobart</option>
	<option timeZoneId="77" gmtAdjustment="GMT+10:00" useDaylightTime="0" value="10">(GMT+10:00) Guam, Port Moresby</option>
	<option timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="10">(GMT+10:00) Vladivostok</option>
	<option timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="11">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
	<option timeZoneId="80" gmtAdjustment="GMT+12:00" useDaylightTime="1" value="12">(GMT+12:00) Auckland, Wellington</option>
	<option timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
	<option timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="13">(GMT+13:00) Nuku\'alofa</option>
</select>';

	echo '<input type="hidden" id="TZ_CODE" name="TZ_CODE" value="'.$value.'"></input>';

	?>
	<script>
	var selectTZ = document.getElementById("TIME_ZONE");
	var TZ_CODE = document.getElementById("TZ_CODE");
	//TZ_CODE.value = selectTZ.options[selectTZ.selectedIndex].value;

	 jQuery(function() {
        jQuery("#TIME_ZONE").change(function(){
        var element = jQuery(this).find('option:selected'); 
        var tzcode = element.attr("timeZoneId"); 

        jQuery('#TZ_CODE').val(tzcode);
        });
    });
	 
	 
	 jQuery(document).ready(function(){
	var chosencode =jQuery("#TZ_CODE").val();
	jQuery("[timeZoneId="+chosencode+"]").attr("selected","1");	
	 });

	</script>
	<?php
	
}





function rim_RetargetIMAutoloader($className)
{
    // An array of paths, relative to the current directory, with trailing slashes,
    // to search for autoload classes within.
    $paths = array(
        'src/'
    );

    if (stripos($className, "RetargetIMNS") === false) {
        return;
    }

    foreach ($paths as $path) {
        $className = ltrim($className, '\\');
        $fileName  = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= dirname(__FILE__) . DIRECTORY_SEPARATOR . $path . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($fileName)) {
            require $fileName;
        }
    }
}

spl_autoload_register('rim_RetargetIMAutoloader');

define('RetargetIMNS_RetargetIM_DIR', dirname(__FILE__));
define('RetargetIMNS_RetargetIM_URL', plugin_dir_url(__FILE__));

$RetargetIM = new RetargetIMNS_RetargetIM_RetargetIM(); // PHP 5.2
// $plugin = new {{PLUGIN_NAMESPACE}}\{{PLUGIN_NAME}}\{{PLUGIN_NAME}}(); // PHP 5.3

$RetargetIM->rim_initialize();



?>