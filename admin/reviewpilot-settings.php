<?php /**
* WordPress Settings Page
*/
function website_reviewpilot_page() {
// Check the user capabilities
	if ( !current_user_can( 'manage_woocommerce' ) ) {
		wp_die( __( 'U heeft onvoldoende rechten om deze pagina te bekijken.', 'website-reviewpilot' ) );
	}
// Save the field values
	if ( isset( $_POST['reviewpilot_fields_submitted'] ) && $_POST['reviewpilot_fields_submitted'] == 'submitted' ) {
		$output = reviewpilotfields();
	} elseif(isset($_POST['reviewpilot_fields_submitted_mail'] ) && $_POST['reviewpilot_fields_submitted_mail'] == 'submitted' && $_POST['customer_email'] != '') {
		$email_sent = reviewpilotmail();
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e( 'website - reviewpilot key configuration', 'website-reviewpilot' ); ?></h2>
		<?php if ( isset( $_POST['reviewpilot_fields_submitted'] ) && $_POST['reviewpilot_fields_submitted'] == 'submitted' ) { ?>
			<div id="message" class="updated fade"><p><strong><?php _e( 'Uw instellingen zijn opgeslagen.', 'website-reviewpilot' ); ?></strong></p></div>
			<?php if($output == 0){ ?>
				<div id="message" class="error"><p><strong><?php _e( 'Uw geheime code is ongeldig. U kunt de geheime code vinden in uw persoonlijke reviewpilots portaal..', 'website-reviewpilot' ); ?></strong></p></div>
			<?php } ?>
		<?php } ?>
		<div id="content">
			<form method="post" action="" id="reviewpilot_settings">
				<input type="hidden" name="reviewpilot_fields_submitted" value="submitted">
				<div id="poststuff">
					<div style="float:left; width:72%; padding-right:3%;">
						<div class="postbox">
							<div class="inside reviewpilot-settings">
								<h3><?php _e( 'General Settings', 'website-reviewpilot' ); ?></h3>
								<table class="form-table">
									<tr>
										<th>
											<label for="website_reviewpilot_license_key"><b><?php _e( 'Geheime code:', 'website-reviewpilot' ); ?></b></label>
										</th>
										<td>
											<input type=text name="website_reviewpilot_license_key" class="regular-text" value="<?php if(!get_option( 'website_reviewpilot_license_key' )) {  } else { echo get_option( 'website_reviewpilot_license_key' ); }?>"/>
										</td>
									</tr>
									<tr>
										<th>
											<label for="website_reviewpilot_sender_email"><b><?php _e( 'Afgezender email:', 'website-reviewpilot' ); ?></b></label>
										</th>
										<td>
											<input type=text name="website_reviewpilot_sender_email" class="regular-text" value="<?php if(!get_option( 'website_reviewpilot_sender_email' )) {  } else { echo get_option( 'website_reviewpilot_sender_email' ); }?>"/>
										</td>
									</tr>
									<tr>
										<td colspan=2>
											<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'website-reviewpilot' ); ?>" /></p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</form>
			<?php if (isset($_POST['reviewpilot_fields_submitted_mail'] ) && $_POST['reviewpilot_fields_submitted_mail'] == 'submitted' && $_POST['customer_email'] != '') { if($email_sent == true){ ?>
				<div id="message" class="updated fade"><p><strong><?php _e( 'E-mail has sent successfully', 'website-reviewpilot-email-success' ); ?></strong></p></div>
			<?php } else { ?>
				<div id="message" class="error"><p><strong><?php _e( 'E-mail has been already sent', 'website-reviewpilot-email-error' ); ?></strong></p></div>
			<?php } } ?>
			<form method="post" action="" id="reviewpilot_settings">
				<input type="hidden" name="reviewpilot_fields_submitted_mail" value="submitted">
				<div id="poststuff">
					<div style="float:left; width:72%; padding-right:3%;">
						<div class="postbox">
							<div class="inside reviewpilot-settings">
								<h3><?php _e( 'Send email invite', 'website-reviewpilot-mail' ); ?></h3>
								<table class="form-table">
									<tr>
										<th>
											<label for="customer_email"><b><?php _e( 'Invite mails:', 'website-reviewpilot-invite-mail' ); ?></b></label>
										</th>
										<td>
											<textarea placeholder="Vul e-mailadressen , gescheiden in... Voorbeeld: mark@domein.nl,tim@domein.nl,peter@domein.nl" name="customer_email" id="customer_email" class="large-text code" rows="3"></textarea>
										</td>
									</tr>
									<tr>
										<td colspan=2>
											<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Invite', 'website-reviewpilot-invite-now' ); ?>" /></p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php }