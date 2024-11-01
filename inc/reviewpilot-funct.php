<?php require_once 'class-check-website.php';
/**
* WC Detection
*/

/*admin messages*/
function reviewpilot_showMessage($message, $errormsg = false)
{
	if ($errormsg) { echo '<div id="message" class="error">';}
	else {echo '<div id="message" class="updated fade">';}
	echo "<p>$message</p></div>";
}
function showAdminMessages() {reviewpilot_showMessage(__( 'website is niet actief. Activeer eerst website voor u de plugin kunt gebruiken.', 'website_reviewpilot'), true);}
/* WordPress Administration Menu
Toont een nieuw menu item als submenu van website
*/
function website_reviewpilot_admin_menu() {
//$page = add_submenu_page('website', __( 'reviewpilot license key', 'website-reviewpilot' ), __( 'reviewpilot license key', 'website-reviewpilot' ), 'manage_website', 'website_reviewpilot', 'website_reviewpilot_page' );
	//$page = add_submenu_page('options-general.php', __( 'reviewpilot license key', 'website-reviewpilot' ), __( 'reviewpilot license key', 'website-reviewpilot' ), 'manage_website', 'website_reviewpilot', 'website_reviewpilot_page' );
	  $page = add_submenu_page('options-general.php', __( 'reviewpilot license key', 'website-reviewpilot' ), __( 'reviewpilot license key', 'website-reviewpilot' ), 'manage_woocommerce', 'website_reviewpilot', 'website_reviewpilot_page' );
}
/* Add meta boxes to pages
Toont een nieuwe box op de product pagina en op de bestel pagina.
*/
function website_reviewpilot_add_box() {
	add_meta_box( 'website-reviewpilot-box-product', __( 'Upload Files', 'website-reviewpilot' ), 'website_reviewpilot_box_product', 'product', 'side', 'default' );
	add_meta_box( 'website-reviewpilot-box-order-detail', __( 'Uploaded Files', 'website-reviewpilot' ), 'website_reviewpilot_box_order_detail', 'shop_order', 'side', 'default' );
}
function is_reviewpilot() {
	$whitelist = array( '127.0.0.1', '::1' );
	if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) )
		return true;
}
/* Inhoud van de box op de order-detail pagina*/
function website_reviewpilot_box_order_detail($post) {
	$order=new WC_Order($post->ID);
	$j=1;
	/* per product een formulier met gegevens */
	foreach ( $order->get_items() as $order_item ) {
		$max_upload_count=0;
		$max_upload_count=get_max_upload_count($order,$order_item['product_id']);
		if($max_upload_count!=0){
			$item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );
			$forproduct=$order_item['name'].' ('.$item_meta->display($flat=true,$return=true).')';
			echo '<strong>';
			printf( __('File for product: %s:', 'website-reviewpilot'), $forproduct);
			echo '</strong><br>';
			/* Controle of er al een bestand is geupload */
			$i=1;
			$upload_count=0;
			echo '<ul>';
			while ($i <= $max_upload_count) {
				echo '<li>';
				$name = get_post_meta( $post->ID, '_woo_reviewpilot_uploaded_file_name_' . $j, true );
				if (is_reviewpilot()) {
					$url = get_post_meta( $post->ID, '_woo_reviewpilot_uploaded_file_path_' . $j, true );
				} else {
					$url = home_url( str_replace( ABSPATH, '', get_post_meta( $post->ID, '_woo_reviewpilot_uploaded_file_path_' . $j, true ) ) );
				}
				$forproduct = get_post_meta( $post->ID, '_woo_reviewpilot_uploaded_product_name_' . $j, true );
				/* geen bestand geupload, dus toon upload velden */
				if( !empty( $url ) && !empty( $name ) ) {
					printf( '<a href="%s" target="_blank">%s</a>', $url, $name );
					$upload_count++;
				} else {
					echo '<span style="color:red;">';
					printf( __('File #%s has not been uploaded.', 'website-reviewpilot'), $i );
					echo '</span>';
				}
				$i++;
				$j++;
				echo '</li>';
			}
			echo '</ul>';
			/* toon aantal nog aan te leveren bestanden */
			$upload_count=$max_upload_count-$upload_count;
			echo '<p>';
			printf( __('Files to be uploaded for this item: %s', 'website-reviewpilot'), $upload_count );
			echo '</p>';
		}
	}
}
/* Inhoud van de box op de product bewerk pagina*/
function website_reviewpilot_box_product($post) {
	wp_nonce_field( 'woo_reviewpilot_nonce', 'woo_reviewpilot_nonce' );
	echo '<p>';
	echo '<label for="_woo_reviewpilot_enable">' . __('Enable', 'website-reviewpilot' ) . ': </label> ';
	echo '<input type="hidden" name="_woo_reviewpilot_enable" value="0" />';
	$myarray=get_post_meta( $post->ID, '_woo_reviewpilot_enable');
	$checked=checked( get_post_meta( $post->ID, '_woo_reviewpilot_enable', true ), 1, false );
	echo '<input type="checkbox" id="_woo_reviewpilot_enable" class="checkbox" name="_woo_reviewpilot_enable" value="1" ' . $checked . ' />';
	echo '</p>';
}
/* Instellingen bewaren*/
function reviewpilot_save_meta_settings( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !isset( $_POST['woo_reviewpilot_nonce'] ) || !wp_verify_nonce( $_POST['woo_reviewpilot_nonce'], 'woo_reviewpilot_nonce' ) ) return;
	update_post_meta( $post_id, '_woo_reviewpilot_enable', (int) $_POST['_woo_reviewpilot_enable'] );
}
/* functie om de producten te tonen*/
function website_reviewpilot_get_product( $product_id, $args = array() ) {
	$product = null;
	if ( version_compare( website_VERSION, "2.0.0" ) >= 0 ) {
// WC 2.0
		$product = get_product( $product_id, $args );
	} else {
// old style, get the product or product variation object
		if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {
			$product = new WC_Product_Variation( $product_id, $args['parent_id'] );
		} else {
// get the regular product, but if it has a parent, return the product variation object
			$product = new WC_Product( $product_id );
			if ( $product->get_parent() ) {
				$product = new WC_Product_Variation( $product->id, $product->get_parent() );
			}
		}
	}
	return $product;
}
/* functie om de product eigenschappen te tonen*/
function website_reviewpilot_get_product_meta( $product, $field_name ) {
	if ( version_compare( website_VERSION, "2.0.0" ) >= 0 ) {
// even in WC >= 2.0 product variations still use the product_custom_fields array apparently
		if ( $product->variation_id && isset( $product->product_custom_fields[ '_' . $field_name ][0] ) && $product->product_custom_fields[ '_' . $field_name ][0] !== '' ) {
			return $product->product_custom_fields[ '_' . $field_name ][0];
		}
// use magic __get
		return maybe_unserialize( $product->$field_name );
	} else {
// use product custom fields array
// variation support: return the value if it's defined at the variation level
		if ( isset( $product->variation_id ) && $product->variation_id ) {
			if ( ( $value = get_post_meta( $product->variation_id, '_' . $field_name, true ) ) !== '' ) return $value;
// otherwise return the value from the parent
			return get_post_meta( $product->id, '_' . $field_name, true );
		}
// regular product
		return isset( $product->product_custom_fields[ '_' . $field_name ][0] ) ? $product->product_custom_fields[ '_' . $field_name ][0] : null;
	}
}
function get_max_upload_count($order,$order_item=0) {
	$max_upload_count=0;
//product specifiek
	if( (( is_array( get_option( 'website_reviewpilot_status' ) ) && in_array( $order->status, get_option( 'website_reviewpilot_status' ) ) ) ) || $order->status == get_option( 'website_reviewpilot_status' ) ) {
		if($order_item!=0) {
			$product = website_reviewpilot_get_product($order_item);
			if( website_reviewpilot_get_product_meta($product,'woo_reviewpilot_enable') == 1) {
				$max_upload_count=1;
			}
		} else {
// order totaal
			foreach ( $order->get_items() as $order_item ) {
				$product = website_reviewpilot_get_product($order_item['product_id']);
				$limit=1;
				if( website_reviewpilot_get_product_meta($product,'woo_reviewpilot_enable') == 1 && $limit > 0 ) {
					$max_upload_count+=$limit;
				}
			}
		}
	}
	return $max_upload_count;
}
/**
* woo_reviewpilot_styles
* Add basic frontend styling if is choosen in admin
* Since 0.2
*/
function woo_reviewpilot_styles() {
	wp_enqueue_style('woo-reviewpilot-style', plugins_url('css/woo-reviewpilot.css',dirname(__FILE__)));
}
/**
* Get allowed or disallowed filetypes and corresponding language strings
* @since 0.2
*/
function all_reviewpilot_review_shortcode(){
	$params = array("license_code" => get_option( 'website_reviewpilot_license_key' ));
	$postData = '';
	foreach($params as $k => $v)
	{
		$postData .= $k . '='.$v.'&';
	}
	$postData = rtrim($postData, '&');
	$response = wp_remote_post( "https://reviewpilot.nl/key_verification/getAllReviews", array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'headers'     => array(),
	    'body'        => $params,
	    'cookies'     => array()
	    )
	);
	$output = wp_remote_retrieve_body( $response );
	//return $output;
	if($output){
		return $output;
	} else {
		return 'Geen beoordelingen gevonden.';
	}
}
/**
* attach email link to emails, this removes the email templates inside /website-upload-my-file/templates/
* @since 0.1
*/
function reviewpilotmail(){
	$email_sent = false;
	$customer_email = explode(",",$_POST['customer_email']);
	foreach ($customer_email as $key => $ce) {
		$params = array("customer_email" => $ce,'license_code'=> get_option( 'website_reviewpilot_license_key' ));
		$postData = '';
		foreach($params as $k => $v)
		{
			$postData .= $k . '='.$v.'&';
		}
		$postData = rtrim($postData, '&');
		$response = wp_remote_post( "https://reviewpilot.nl/key_verification/send_review_invite", array(
		    'method'      => 'POST',
		    'timeout'     => 45,
		    'redirection' => 5,
		    'httpversion' => '1.0',
		    'blocking'    => true,
		    'headers'     => array(),
		    'body'        => $params,
		    'cookies'     => array()
		    )
		);
		$output = wp_remote_retrieve_body( $response );
		if($output != 'false' ){
			$response = explode("|--|",$output);
			$headers = "From: ".$response[7]." <".get_option( 'website_reviewpilot_sender_email' ).">";
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			if(wp_mail ( $ce, $response[1], $response[0], $headers )){
				$params = array("webshop_id" =>$response[2],'is_send'=>'yes','access_code'=>$response[3],'expired'=>$response[4],'reminder'=>$response[5],'email_d'=>$ce,'member_id'=>$response[6]);
			} else {
				$params = array("webshop_id" =>$response[2],'is_send'=>'no','access_code'=>$response[3],'expired'=>$response[4],'reminder'=>$response[5],'email_d'=>$ce,'member_id'=>$response[6]);
			}
			$postData = '';
			foreach($params as $k => $v)
			{
				$postData .= $k . '='.$v.'&';
			}
			$postData = rtrim($postData, '&');
			$response = wp_remote_post( "https://reviewpilot.nl/key_verification/insert_invited_customer", array(
			    'method'      => 'POST',
			    'timeout'     => 45,
			    'redirection' => 5,
			    'httpversion' => '1.0',
			    'blocking'    => true,
			    'headers'     => array(),
			    'body'        => $params,
			    'cookies'     => array()
			    )
			);
			$output = wp_remote_retrieve_body( $response );
			$email_sent = true;
		}
	}
	return $email_sent;
}
function reviewpilotfields(){
	delete_option('website_reviewpilot_use_style');
	foreach ( $_POST as $key => $value ) {
		if($key == 'website_reviewpilot_license_key'){
			// Check license key
			$params = array("license_code" => $_POST['website_reviewpilot_license_key']);
			$postData = '';
			foreach($params as $k => $v)
			{
				$postData .= $k . '='.$v.'&';
			}
			$postData = rtrim($postData, '&');
			$response = wp_remote_post( "https://reviewpilot.nl/key_verification/magento", array(
			    'method'      => 'POST',
			    'timeout'     => 45,
			    'redirection' => 5,
			    'httpversion' => '1.0',
			    'blocking'    => true,
			    'headers'     => array(),
			    'body'        => $params,
			    'cookies'     => array()
			    )
			);
			$output = wp_remote_retrieve_body( $response );
			if($output == 0){
				$value = '';
			}
// END
		}
		if ( get_option( $key ) != $value ) {
			update_option( $key, $value );
		} else {
			add_option( $key, $value, '', 'no' );
		}
	}
	return $output;
}
?>