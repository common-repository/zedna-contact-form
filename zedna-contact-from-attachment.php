<?php
/*
 * Plugin Name: Zedna Contact Form
 * Plugin URI: https://profiles.wordpress.org/zedna#content-plugins
 * Text Domain: zedna-contact-form
 * Domain Path: /languages
 * Description: Contact from with attachment and reCaptcha in shortcode. Users can also attach a file to email. You can keep the attachment also on server.
 * Version: 1.2.2
 * Author: Radek Mezulanik
 * Author URI: http://mezulanik.cz
 * License: GPL3
*/

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'zedna_contact_form_links' );
function zedna_contact_form_links( $links ) {
	 $links[] = '<a href="https://profiles.wordpress.org/zedna/#content-plugins" target="_blank">More plugins from Radek Mezulanik</a>';
	 return $links;
}

//Add admin page
add_action('admin_menu', 'zedna_contact_form_setttings_menu');
if( !defined('ABSPATH') ) die('-1');
function zedna_contact_form_setttings_menu(){        
		add_menu_page( __('Zedna Contact form Settings page','zedna-contact-form'), __('Zedna Contact form Settings','zedna-contact-form'), 'manage_options', 'zedna_contact_form', 'zedna_contact_form_init', 'dashicons-email-alt');
	// Call update_zedna_contact_form function to update database
	add_action( 'admin_init', 'update_zedna_contact_form' );
}

// Create function to register plugin settings in the database
if( !function_exists("update_zedna_contact_form") )
{
function update_zedna_contact_form() {
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_grsitekey' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_grsecretkey' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_allowattachment' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_keepattachment' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_defaultemail' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_defaultsubject' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_wrongmail' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_emptymessage' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_messagesent' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_messagenotsent' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_recaptcha' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_email' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_message' );
		register_setting( 'zedna_contact_form-settings', 'zedna_contact_form_lang_submit' );

		$path = realpath(wp_get_upload_dir()['basedir'].'/form-attachments/');
		if (! is_dir($path)) {
			mkdir( $path, 0700 );
	 }
}
}

function zedna_contact_form_init(){
		$grsitekey = (get_option('zedna_contact_form_grsitekey') != '') ? get_option('zedna_contact_form_grsitekey') : '';
		$grsecretkey = (get_option('zedna_contact_form_grsecretkey') != '') ? get_option('zedna_contact_form_grsecretkey') : '';
		$allowattachment = (get_option('zedna_contact_form_allowattachment') != '') ? get_option('zedna_contact_form_allowattachment') : 'yes';
		$keepattachment = (get_option('zedna_contact_form_keepattachment') != '') ? get_option('zedna_contact_form_keepattachment') : 'no';
		$defaultemail = (get_option('zedna_contact_form_defaultemail') != '') ? get_option('zedna_contact_form_defaultemail') : '';
		$defaultsubject = (get_option('zedna_contact_form_defaultsubject') != '') ? get_option('zedna_contact_form_defaultsubject') : '';

		$lang_wrongmail = (get_option('zedna_contact_form_lang_wrongmail') != '') ? get_option('zedna_contact_form_lang_wrongmail') : 'Wrong E-mail.';
		$lang_emptymessage = (get_option('zedna_contact_form_lang_emptymessage') != '') ? get_option('zedna_contact_form_lang_emptymessage') : 'What about write something?';
		$lang_messagesent = (get_option('zedna_contact_form_lang_messagesent') != '') ? get_option('zedna_contact_form_lang_messagesent') : 'Thank you! Your message has been sent.';
		$lang_messagenotsent = (get_option('zedna_contact_form_lang_messagenotsent') != '') ? get_option('zedna_contact_form_lang_messagenotsent') : 'Message has not been sent. Please try again.';
		$lang_recaptcha = (get_option('zedna_contact_form_lang_recaptcha') != '') ? get_option('zedna_contact_form_lang_recaptcha') : 'Please fill in reCaptcha.';
		$lang_email = (get_option('zedna_contact_form_lang_email') != '') ? get_option('zedna_contact_form_lang_email') : 'E-mail*';
		$lang_message = (get_option('zedna_contact_form_lang_message') != '') ? get_option('zedna_contact_form_lang_message') : 'Message*';
		$lang_submit = (get_option('zedna_contact_form_lang_submit') != '') ? get_option('zedna_contact_form_lang_submit') : 'Submit';
?>

<h1><?php echo __('Zedna Contact form','zedna-contact-form');?></h1>
<img src="<?php echo plugins_url( '/assets/banner-772x250.png' , __FILE__ ); ?>">
<form method="post" action="options.php">
	<?php settings_fields( 'zedna_contact_form-settings' ); ?>
	<?php do_settings_sections( 'zedna_contact_form-settings' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php echo __('Allow attachment:','zedna-contact-form');?></th>
			<td>
				<select name='zedna_contact_form_allowattachment'>
					<?php $allowattachment = get_option('zedna_contact_form_allowattachment'); 
			if ($allowattachment === "no"){
			 echo "<option value='no' selected=selected>".__('No','zedna-contact-form')."</option>
						 <option value='yes'>".__('Yes','zedna-contact-form')."</option>";
			}else{ 
			echo "<option value='no'>".__('No','zedna-contact-form')."</option>
						<option value='yes' selected=selected>".__('Yes','zedna-contact-form')."</option>";
			}
			?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Keep attachment in upload folder:','zedna-contact-form');?></th>
			<td>
				<select name='zedna_contact_form_keepattachment'>
					<?php $allowattachment = get_option('zedna_contact_form_keepattachment'); 
			if ($allowattachment === "no"){
			 echo "<option value='no' selected=selected>".__('No','zedna-contact-form')."</option>
						 <option value='yes'>".__('Yes','zedna-contact-form')."</option>";
			}else{ 
			echo "<option value='no'>".__('No','zedna-contact-form')."</option>
						<option value='yes' selected=selected>".__('Yes','zedna-contact-form')."</option>";
			}
			?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Default email:','zedna-contact-form');?></th>
			<td><input type="text" name="zedna_contact_form_defaultemail"
					value="<?php echo esc_attr($defaultemail);?>" /><?php echo __('(or set one in shortcode)','zedna-contact-form');?>
			</td>
		</tr>
		<?php 
			$grsitekey = get_option('zedna_contact_form_grsitekey');
			$grsecretkey = get_option('zedna_contact_form_grsecretkey');
			?>
		<tr valign="top">
			<th scope="row"><?php echo __('Google reCaptcha site key:','zedna-contact-form');?></th>
			<td><input type="text" name="zedna_contact_form_grsitekey" value="<?php echo esc_attr($grsitekey);?>" /> <a
					href="https://www.google.com/recaptcha/admin"
					target="_blank"><?php echo __('Generate my keys','zedna-contact-form');?></a></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Google reCaptcha secret key:','zedna-contact-form');?></th>
			<td><input type="text" name="zedna_contact_form_grsecretkey" value="<?php echo esc_attr($grsecretkey);?>" /> <a
					href="https://www.google.com/recaptcha/admin"
					target="_blank"><?php echo __('Generate my keys','zedna-contact-form');?></a></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Custom text','zedna-contact-form');?></th>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Wrong E-mail.','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_wrongmail" value="<?php echo esc_attr($lang_wrongmail);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('What about write something?','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_emptymessage"
					value="<?php echo esc_attr($lang_emptymessage);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Thank you! Your message has been sent.','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_messagesent"
					value="<?php echo esc_attr($lang_messagesent);?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Message has not been sent. Please try again.','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_messagenotsent"
					value="<?php echo esc_attr($lang_messagenotsent);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Please fill in reCaptcha.','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_recaptcha" value="<?php echo esc_attr($lang_recaptcha);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('E-mail*','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_email" value="<?php echo esc_attr($lang_email);?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Message*','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_message" value="<?php echo esc_attr($lang_message);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo __('Submit','zedna-load-more-posts');?></th>
			<td><input type="text" name="zedna_contact_form_lang_submit" value="<?php echo esc_attr($lang_submit);?>" /></td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
<p>
	<?php echo __('Insert this shortcode to your post <b>[contact email="yourmail@example.com" subject="Your subject"]</b>','zedna-contact-form');?>
</p>
<p>If you like this plugin, please donate us for faster upgrade</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit"
		alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
}

// Add form button to TinyMCE
add_filter( 'mce_buttons', 'zedna_cfa_register_buttons' );
function zedna_cfa_register_buttons( $buttons ) {
	$buttons[] = 'zednaContactForm';
	return $buttons;
}
// Include form plugin to TinyMCE
add_filter( 'mce_external_plugins', 'zedna_cfa_plugin_register_plugin' );
function zedna_cfa_plugin_register_plugin( $plugin_array ) {
	$plugin_array['zednaContactForm'] = plugins_url( '/tinymce-plugin/zedna-contactform.js',__FILE__ );
  return $plugin_array;
}

/*
shortcode for contact form
*/
function zedna_contact_form_shortcode( $atts ) {
		extract(
				shortcode_atts(
						array(
								'email' => '1',
								'subject' => '1',
						),
				$atts )
		);
$contact_email = $email;
$subject;
ob_start();
wp_enqueue_style('zedna-contact-form-style',plugin_dir_url( __FILE__ ) . '/zedna-contact-from-attachment.css',false,'1.0','all');
		/* CONTACT FORM */
$grsitekey = get_option('zedna_contact_form_grsitekey');
$grsecretkey = get_option('zedna_contact_form_grsecretkey');
$allowattachment = get_option('zedna_contact_form_allowattachment');
$keepattachment = get_option('zedna_contact_form_keepattachment');
$defaultemail = get_option('zedna_contact_form_defaultemail');
$lang_wrongmail = get_option('zedna_contact_form_lang_wrongmail');
$lang_emptymessage = get_option('zedna_contact_form_lang_emptymessage');
$lang_messagesent = get_option('zedna_contact_form_lang_messagesent');
$lang_messagenotsent = get_option('zedna_contact_form_lang_messagenotsent');
$lang_recaptcha = get_option('zedna_contact_form_lang_recaptcha');
$lang_email = get_option('zedna_contact_form_lang_email');
$lang_message = get_option('zedna_contact_form_lang_message');
$lang_submit = get_option('zedna_contact_form_lang_submit');

if(isset($_POST['ContactButton']) && wp_verify_nonce( $_REQUEST['zedna-contact-button-nonce'], 'zedna-contact-button-form-submit' )){
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$privatekey = $grsecretkey;
		$response = wp_remote_retrieve_body( wp_remote_get($url."?secret=".$privatekey."&response=".sanitize_text_field($_POST['g-recaptcha-response'])));
		$data = json_decode($response);

		if((isset($data->success) && $data->success === true) || empty($privatekey)){
				//true

	//response generation function
	$formresponse = "";

	//function to generate response
	function zedna_contact_form_generate_response($type, $message){

		global $formresponse;

		if($type === "success") $formresponse = "<div class='success'>{$message}</div>";
		else $formresponse = "<div class='error'>{$message}</div>";

	}

	//user posted variables
	$email = sanitize_text_field($_POST['message_email']);
	//$subject = $_POST['message_subject'];
	$message = sanitize_text_field($_POST['message_text']);

	//php mailer variables
	
		if (!empty($contact_email)){
						$to = $contact_email;
				}else{
						$to = $defaultemail;
				}
	
	if(empty($subject)){
				$subject = $defaultsubject;
			}
	$headers = 'From: '. $email . "\r\n" .
		'Reply-To: ' . $email . "\r\n";

		$path = realpath(wp_get_upload_dir()['basedir'].'/form-attachments/');
		if (! is_dir($path)) {
			mkdir( $path, 0700 );
		}
		move_uploaded_file($_FILES["attachment"]["tmp_name"],$path.'/'.basename(sanitize_file_name($_FILES['attachment']['name'])));
		$attachments = array(realpath($path.'/'.sanitize_file_name($_FILES["attachment"]["name"])));

			//validate email
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
				
					echo "<div class='error'>".esc_attr($lang_wrongmail)."</div>";
			else //email is valid
			{
				//validate presence of name and message
				if(empty($message)){
					
						echo "<div class='error'".esc_attr($lang_emptymessage)."</div>";
				}
				else //ready to go!
				{
						echo "<div class='hidden'>";
					$sent = wp_mail($to, $subject, strip_tags($message), $headers, $attachments);
						echo "</div>";
					if($sent){
							//message sent!
							echo "<div class='success'>".esc_attr($lang_messagesent)."</div>";
					}
					else
					{
							//message wasn't sent
							echo "<div class='error'>".esc_attr($lang_messagenotsent)."</div>";
							}
						if(count($attachments) && $keepattachment === "no"){
						unlink(realpath($path.'/'.sanitize_file_name($_FILES["attachment"]["name"])));
								}
				}
			}
				} else {
						//message sent!
				echo "<div class='error'>".esc_attr($lang_recaptcha)."</div>";
					}
		}
?>
<?php if(!empty($grsitekey)):?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php endif;?>
<div id="respond" class="zedna-contact-form">

	<?php if (!empty($contact_email)){ ?>
	<?php echo esc_attr($formresponse); ?>
	<form action="<?php the_permalink(); ?>" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label for="message_email" class="label-email">
				<h4 class="zedna-contact-form-title"><?php echo esc_attr($lang_email);?></h4>
				<input type="text" name="message_email" class="form-control" size="46"
					value="<?php echo esc_attr(sanitize_text_field($_POST['message_email'])); ?>">
			</label>
		</div>
		<div class="form-group">
			<label for="message_text" class="label-message">
				<h4 class="zedna-contact-form-title"><?php echo esc_attr($lang_message);?></h4>
				<textarea type="text" name="message_text" class="form-control" cols="45"
					rows="10"><?php echo esc_textarea(sanitize_text_field($_POST['message_text'])); ?></textarea>
			</label>
		</div>
		<?php if($allowattachment === "yes"){?>
		<input type="file" name="attachment" class='btn btn-default btn-attachment'>
		<?php } ?>
		<input type="hidden" name="submitted" value="1">
		<?php if(!empty($grsitekey)):?>
		<div id="recaptcha_footer"></div>
		<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($grsitekey); ?>"></div>
		<?php endif;?>
		<p>
			<input id="ContactButton" name="ContactButton" class="btn btn-primary zedna-contact-button" type="submit"
				value="<?php echo esc_attr($lang_submit);?>">
		</p>
		<?php wp_nonce_field( 'zedna-contact-button-form-submit', 'zedna-contact-button-nonce' );?>
	</form>
	<?php }    ?>
</div>
<!-- # CONTACT FORM -->
<?php
		$output = ob_get_clean();
		return $output;
}
add_shortcode( 'contact', 'zedna_contact_form_shortcode' );
/*
#shortcode for contact form
*/