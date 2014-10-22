<?php
final class AdminAFormSettingsPage {
	public static function indexPage() {
		?>
		<div class="wrap">
		  <h2>Settings</h2>
		  <div class="postbox " style="display: block; ">
		  <div class="inside">
		  <form id="settings_form" method="post" action="options.php">
		    <?php settings_fields( 'a-forms-settings-group' ); ?>
		    <h3>Admin Settings</h3>
		    <table class="form-table">
		      <tbody>
		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_admin_email">Admin Email:</label>
		          </th>
		          <td>
		            <input type="text" id="a_forms_admin_email" name="a_forms_admin_email" value="<?php echo get_option('a_forms_admin_email'); ?>" />
		            <span class="example">e.g: admin@yourcompany.com.au</span>
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="aforms_include_securimage">Include Securimage Library:</label>
		          </th>
		          <td>
		            <input type="hidden" name="aforms_include_securimage" value="0" />
		            <input type="checkbox" id="aforms_include_securimage" name="aforms_include_securimage" value="1"
		              <?php
		                if (get_option("aforms_include_securimage") == "1") {
		                  echo "checked";
		                }
		              ?>
		             />
		          </td>
		        </tr>
		      </tbody>
		    </table>

		    <h3>SMTP Settings</h3>
		    <table class="form-table">
		      <tbody>
		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_mail_host">Mail Host:</label>
		          </th>
		          <td>
		            <input type="text" id="a_forms_mail_host" name="a_forms_mail_host" value="<?php echo get_option('a_forms_mail_host'); ?>" />
		            <span class="example">e.g: mail.yourdomain.com</span>
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_smtp_auth">Enable SMTP Authentication:</label>
		          </th>
		          <td>
		            <input type="hidden" name="a_forms_smtp_auth" value="0" />
		            <input type="checkbox" id="a_forms_smtp_auth" name="a_forms_smtp_auth" value="1" <?php if (get_option('a_forms_smtp_auth')) {echo "checked";} ?> />
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_enable_tls">Enable TLS:</label>
		          </th>
		          <td>
		            <input type="hidden" name="a_forms_enable_tls" value="0" />
		            <input type="checkbox" id="a_forms_enable_tls" name="a_forms_enable_tls" value="1" <?php if (get_option('a_forms_enable_tls')) {echo "checked";} ?> />
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_enable_ssl">Enable SSL:</label>
		          </th>
		          <td>
		            <input type="hidden" name="a_forms_enable_ssl" value="0" />
		            <input type="checkbox" id="a_forms_enable_ssl" name="a_forms_enable_ssl" value="1" <?php if (get_option('a_forms_enable_ssl')) {echo "checked";} ?> />
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_smtp_port">SMTP Port:</label>
		          </th>
		          <td>
		            <input type="text" id="a_forms_smtp_port" name="a_forms_smtp_port" value="<?php echo get_option('a_forms_smtp_port'); ?>" />
		            <span class="example">e.g: 26</span>
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_smtp_username">SMTP Username:</label>
		          </th>
		          <td>
		            <input type="text" id="a_forms_smtp_username" name="a_forms_smtp_username" value="<?php echo get_option('a_forms_smtp_username'); ?>" />
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="a_forms_smtp_password">SMTP Password:</label>
		          </th>
		          <td>
		            <input type="password" id="a_forms_smtp_password" name="a_forms_smtp_password" value="<?php echo get_option('a_forms_smtp_password'); ?>" />
		          </td>
		        </tr>
		    
		      </tbody>
		    </table>

		    <p class="submit">
		      <input type="submit" name="Submit" value="Update Settings" />
		    </p>

		  </form>
		  </div>
		  </div>
		  </div>
  	<?php
	}
}
?>