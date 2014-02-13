<?php
/*
Plugin Name: A Forms
Plugin URI: http://wordpress.org/extend/plugins/a-forms/
Description: Adds a contact form to your wordpress site.

Installation:

1) Install WordPress 3.8 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/tom-m8te 

http://wordpress.org/extend/plugins/jquery-ui-theme 

http://wordpress.org/extend/plugins/a-forms

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 1.6.3
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

require_once("admin/controllers/a_forms_controller.php");
require_once("admin/pages/a_forms_page.php");
require_once("admin/validations/a_forms_validation.php");

require_once("admin/controllers/a_form_fields_controller.php");
require_once("admin/pages/a_form_fields_page.php");
require_once("admin/validations/a_form_fields_validation.php");

require_once("admin/controllers/a_form_sections_controller.php");
require_once("admin/pages/a_form_sections_page.php");
require_once("admin/validations/a_form_sections_validation.php");

require_once("admin/controllers/a_form_settings_controller.php");
require_once("admin/pages/a_form_settings_page.php");

require_once("admin/controllers/a_form_tracking_controller.php");
require_once("admin/pages/a_form_tracking_page.php");

require_once("admin/controllers/a_form_styling_controller.php");
require_once("admin/pages/a_form_styling_page.php");

require_once("controllers/a_form_controller.php");
require_once("validations/a_form_validation.php");
require_once("pages/a_form_page.php");
require_once("helpers/a_form_helper.php");

require_once("a-forms-path.php");
include_once (dirname (__FILE__) . '/tinymce/tinymce.php'); 

define(__AFORMS_DEFAULT_LIMIT__, "10");

function a_forms_activate() {
  global $wpdb;

  $a_form_forms_table = $wpdb->prefix . "a_form_forms";
  $checktable = $wpdb->query("SHOW TABLES LIKE '$a_form_forms_table'");
  if ($checktable == 0) {

    $sql = "CREATE TABLE $a_form_forms_table (
      ID mediumint(9) NOT NULL AUTO_INCREMENT, 
      form_name VARCHAR(255) DEFAULT '',
      to_email VARCHAR(255) DEFAULT '',
      to_cc_email VARCHAR(255) DEFAULT '',
      to_bcc_email VARCHAR(255) DEFAULT '',
      subject VARCHAR(255) DEFAULT '',
      show_section_names tinyint(4) NOT NULL DEFAULT 1,
      field_name_id mediumint(9), 
      field_email_id mediumint(9), 
      field_subject_id mediumint(9), 
      send_confirmation_email tinyint(4) NOT NULL DEFAULT 0,
      confirmation_from_email VARCHAR(255) DEFAULT '',
      success_message longtext DEFAULT '',
      success_redirect_url VARCHAR(255) DEFAULT '',
      include_captcha tinyint(4) NOT NULL DEFAULT 0,
      tracking_enabled tinyint(4) NOT NULL DEFAULT 1,
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (ID),
      UNIQUE (form_name)
    )";
    $wpdb->query($sql); 

    $a_form_sections_table = $wpdb->prefix . "a_form_sections";
    $sql = "CREATE TABLE $a_form_sections_table (
      ID mediumint(9) NOT NULL AUTO_INCREMENT, 
      section_name VARCHAR(255) DEFAULT '',
      section_order mediumint(9) NOT NULL DEFAULT 0, 
      form_id mediumint(9) NOT NULL, 
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (ID)
    )";
    $wpdb->query($sql); 

    $a_form_fields_table = $wpdb->prefix . "a_form_fields";
    $sql = "CREATE TABLE $a_form_fields_table (
      FID mediumint(9) NOT NULL AUTO_INCREMENT, 
      field_type VARCHAR(255) DEFAULT '',
      field_label VARCHAR(255) DEFAULT '', 
      value_options longtext DEFAULT '',
      field_order mediumint(9) NOT NULL DEFAULT 0, 
      validation VARCHAR(255) DEFAULT '',
      file_ext_allowed VARCHAR(255) DEFAULT '',
      form_id mediumint(9) NOT NULL,
      section_id mediumint(9) NOT NULL,
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (FID)
    )";
    $wpdb->query($sql);

    $a_form_tracks_table = $wpdb->prefix . "a_form_tracks";
    $sql = "CREATE TABLE $a_form_tracks_table (
      ID mediumint(9) NOT NULL AUTO_INCREMENT, 
      content longtext NOT NULL,
      track_type VARCHAR(255) DEFAULT '',
      form_id mediumint(9) NOT NULL,
      referrer_url VARCHAR(255) DEFAULT '',
      fields_array mediumtext DEFAULT '',
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (ID)
    )";
    $wpdb->query($sql);

  }

  $checkcol = $wpdb->query("SHOW COLUMNS FROM '$a_form_forms_table' LIKE 'enable_ajax'");
  if ($checkcol == 0) {
    $sql = "ALTER TABLE $a_form_forms_table ADD enable_ajax VARCHAR(1)";
    $wpdb->query($sql); 
  }

  $checkcol = $wpdb->query("SHOW COLUMNS FROM '$a_form_forms_table' LIKE 'include_admin_in_emails'");
  if ($checkcol == 0) {
    $sql = "ALTER TABLE $a_form_forms_table ADD include_admin_in_emails VARCHAR(1)";
    $wpdb->query($sql); 
  }

  $checkcol = $wpdb->query("SHOW COLUMNS FROM '$a_form_forms_table' LIKE 'captcha_type'");
  if ($checkcol == 0) {
    $sql = "ALTER TABLE $a_form_forms_table ADD captcha_type VARCHAR(1) DEFAULT '0'";
    $wpdb->query($sql); 
  }

  if (!is_dir(get_template_directory()."/aforms_css")) {
    aform_copy_directory(AFormsPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  
  } else {
    add_option("aform_current_css_file", "default.css");
  }

}
register_activation_hook( __FILE__, 'a_forms_activate' );

//call register settings function
add_action( 'admin_init', 'register_a_forms_settings' );
function register_a_forms_settings() {
  register_setting( 'a-forms-settings-group', 'a_forms_admin_email' );
  register_setting( 'a-forms-settings-group', 'a_forms_mail_host' );
  register_setting( 'a-forms-settings-group', 'a_forms_smtp_auth' );
  register_setting( 'a-forms-settings-group', 'a_forms_smtp_port' );
  register_setting( 'a-forms-settings-group', 'a_forms_enable_tls' );
  register_setting( 'a-forms-settings-group', 'a_forms_enable_ssl' );
  register_setting( 'a-forms-settings-group', 'a_forms_smtp_username' );
  register_setting( 'a-forms-settings-group', 'a_forms_smtp_password' );

  global $wpdb;
  $a_form_forms_table = $wpdb->prefix . "a_form_forms";
  $checkcol = $wpdb->query("SHOW COLUMNS FROM '$a_form_forms_table' LIKE 'multipage_sections'");
  if ($checkcol == 0) {
    $sql = "ALTER TABLE $a_form_forms_table ADD multipage_sections VARCHAR(1) DEFAULT 1";
    $wpdb->query($sql); 
  }
}

function are_a_forms_dependencies_installed() {
  return is_plugin_active("tom-m8te/tom-m8te.php") && is_plugin_active("jquery-ui-theme/jquery-ui-theme.php");
}

add_action( 'admin_notices', 'a_forms_notice_notice' );
function a_forms_notice_notice(){
  $activate_nonce = wp_create_nonce( "activate-a-forms-dependencies" );
  $tom_active = is_plugin_active("tom-m8te/tom-m8te.php");
  $jquery_ui_theme_active = is_plugin_active("jquery-ui-theme/jquery-ui-theme.php");
  if (!($tom_active && $jquery_ui_theme_active)) { ?>
    <div class='updated below-h2'><p>Before you can use A Forms, please install/activate the following plugin(s):</p>
    <ul>
      <?php if (!$tom_active) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/tom-m8te/">Tom M8te</a> 
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/tom-m8te/tom-m8te.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?a_forms_install_dependency=tom-m8te&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=tom-m8te&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php }
      if (!$jquery_ui_theme_active) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/jquery-ui-theme/">JQuery UI Theme</a>
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/jquery-ui-theme/jquery-ui-theme.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?a_forms_install_dependency=jquery-ui-theme&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=jquery-ui-theme&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
    </div>
    <?php
  }

}

add_action( 'admin_init', 'register_a_forms_install_dependency_settings' );
function register_a_forms_install_dependency_settings() {
  if (isset($_GET["a_forms_install_dependency"])) {
    if (wp_verify_nonce($_REQUEST['_wpnonce'], "activate-a-forms-dependencies")) {
      switch ($_GET["a_forms_install_dependency"]) {
        case 'jquery-ui-theme':
          activate_plugin('jquery-ui-theme/jquery-ui-theme.php', 'plugins.php?error=false&plugin=jquery-ui-theme.php');
          wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php");
          exit();
          break; 
        case 'tom-m8te':  
          activate_plugin('tom-m8te/tom-m8te.php', 'plugins.php?error=false&plugin=tom-m8te.php');
          wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php");
          exit();
          break;   
        default:
          throw new Exception("Sorry unable to install plugin.");
          break;
      }
    } else {
      die("Security Check Failed.");
    }
  }
}

add_action('admin_menu', 'register_a_forms_page');
function register_a_forms_page() {
  if (are_a_forms_dependencies_installed()) {
    add_menu_page('A Forms', 'A Forms', 'manage_options', 'a-forms/a-forms.php', 'a_form_router', plugins_url("/tinymce/images/logo.png", __FILE__));
    add_submenu_page('a-forms/a-forms.php', 'Settings', 'Settings', 'manage_options', 'a-forms/a-forms-settings.php', 'a_form_router');
    add_submenu_page('a-forms/a-forms.php', 'Tracking', 'Tracking', 'manage_options', 'a-forms/a-forms-tracking.php', 'a_form_router');
    add_submenu_page('a-forms/a-forms.php', 'Styling', 'Styling', 'update_themes', 'a-forms/a-forms-styling.php', 'a_form_router');
  }
}

add_action('wp_ajax_aform_css_file_selector', 'aform_css_file_selector');
function aform_css_file_selector() {
  if (are_a_forms_dependencies_installed()) {
    update_option("aform_current_css_file", ($_POST["css_file_selection"]));
    echo(@file_get_contents(get_template_directory()."/aforms_css/".($_POST["css_file_selection"])));
  }
  die();  
}

add_action('wp_ajax_add_field_to_section', 'add_field_to_section');
function add_field_to_section() {
  global $wpdb;
  $section = tom_get_row_by_id("a_form_sections", "*", "ID", ($_POST["section_id"]));
  tom_insert_record("a_form_fields", array("field_order" => ($_POST["field_order"]), "section_id" => ($_POST["section_id"]), "form_id" => $section->form_id));
  echo $section->ID."::".$wpdb->insert_id;
  die();  
}


add_action('wp_ajax_aforms_tinymce', 'aforms_tinymce');
/**
 * Call TinyMCE window content via admin-ajax
 * 
 * @since 1.7.0 
 * @return html content
 */
function aforms_tinymce() {
  if (are_a_forms_dependencies_installed()) {
    // check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
      die(__("You are not allowed to be here"));
          
    include_once( dirname( dirname(__FILE__) ) . '/a-forms/tinymce/window.php');
    
    die(); 
  } 
}

add_action("admin_init", "a_form_register_admin_scripts");
function a_form_register_admin_scripts() {
  if (preg_match("/a-form/", $_REQUEST["page"])) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');

    wp_register_script("a-forms", plugins_url("/js/application.js", __FILE__));
    wp_enqueue_script("a-forms");

    wp_localize_script( 'a-forms', 'AFormsAjax', array(
      "ajax_url" => admin_url('admin-ajax.php'),
      "base_url" => get_option('siteurl')."/wp-admin/admin.php?page=a-forms/a-forms.php",
      "sort_section_url" => get_option('siteurl')."/wp-admin/admin.php?page=a-forms/a-forms.php&controller=AFormSections&action=index",
      "sort_field_url" => get_option('siteurl')."/wp-admin/admin.php?page=a-forms/a-forms.php&controller=AFormSections&action=index"
    ));

    wp_register_style("a-forms", plugins_url("/admin_css/style.css", __FILE__));
    wp_enqueue_style("a-forms");
  }
}

function a_form_router() {
  if (are_a_forms_dependencies_installed()) {
    // If you don't use Securimage and Tom M8te is not setup to use Securimage, then ...
    if (get_option("include_securimage") != "1" && !class_exists("Securimage")) {
      // Make Tom M8te use Securimage.
      update_option("include_securimage", "1");
    } 

    if (preg_match("/a-forms-tracking/", $_REQUEST["page"])) {

      if (($_REQUEST["sub_action"] == "") && ($_REQUEST["action"] == "")) {
        AdminAFormTrackingPage::indexPage();
      } else if ($_REQUEST["action"] == "show") {
        AdminAFormTrackingPage::showPage();
      } else if ($_REQUEST["action"] == "Search") {
        AdminAFormTrackingPage::showPage();
      } else if ($_REQUEST["action"] == "view") {
        AdminAFormTrackingPage::viewPage();
      }

    } else if (preg_match("/a-forms-styling/", $_REQUEST["page"])) {
      
      if ($_REQUEST["action"] == "Reset") {
        AdminAFormStylingController::ResetAction();
      } 

      AdminAFormStylingController::indexAction();
      
    } else if (preg_match("/a-forms-settings/", $_REQUEST["page"])) {
      AdminAFormSettingsController::indexAction();
    } else {
      if ($_REQUEST["controller"] == "" || $_REQUEST["controller"] == "AForms") {

        if (($_REQUEST["sub_action"] == "") && ($_REQUEST["action"] == "")) {
          AdminAFormsController::indexAction();
        } else if ($_REQUEST["action"] == "edit") {
          AdminAFormsController::editAction();
        } else if ($_REQUEST["sub_action"] == "Update" || $_REQUEST["sub_action"] == "Save and Finish") {
          AdminAFormsController::updateAction();
        } else if ($_REQUEST["action"] == "new") {
          AdminAFormsController::newAction();
        } else if ($_REQUEST["action"] == "Create") {
          AdminAFormsController::createAction();
        } else if ($_REQUEST["action"] == "delete") {
          AdminAFormsController::deleteAction();
        }       

      } else if ($_REQUEST["controller"] == "AFormFields"){

        if ($_REQUEST["action"] == "Update") {
          AdminAFormFieldsController::updateAction();
        } else if ($_REQUEST["action"] == "delete") {
          AdminAFormFieldsController::deleteAction();
        }

      } else if ($_REQUEST["controller"] == "AFormSections") {

        if ($_REQUEST["action"] == "edit") {
          AdminAFormSectionsController::editAction();
        } else if ($_REQUEST["action"] == "Update") {
          AdminAFormSectionsController::updateAction();
        } else if ($_REQUEST["action"] == "new") {
          AdminAFormSectionsController::newAction();
        } else if ($_REQUEST["action"] == "Create") {
          AdminAFormSectionsController::createAction();
        } else if ($_REQUEST["action"] == "delete") {
          AdminAFormSectionsController::deleteAction();
        }

      }
    }

    ?>
    <div class="clear"></div>
    <?php 
    tom_add_social_share_links("http://wordpress.org/extend/plugins/a-forms/");
  }
  
}


add_action( 'widgets_init', 'aforms_register_form_widget' );

/**
 * Adds AFormFormWidget widget.
 */
class AFormFormWidget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'aform_widget', // Base ID
      __('A Form', 'a_form_widget'), // Name
      array( 'description' => __( 'A widget that allows you to add your AForm to your sidebar', 'a_form_widget' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    if ( isset( $instance[ 'a_form_selection' ] ) ) {
      if ($instance[ 'a_form_selection' ] != "") {
        $atts = array();
        $a_form_selection = $instance[ 'a_form_selection' ];
        $atts["id"] = $a_form_selection;
        echo a_form_shortcode($atts);
      }
    }
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    if ( isset( $instance[ 'a_form_selection' ] ) ) {
      $a_form_selection = $instance[ 'a_form_selection' ];
    }
    $aforms_list = tom_get_results("a_form_forms", "*", "");
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'a_form_selection' ); ?>">Select Form</label> 
      <select id="<?php echo $this->get_field_id( 'a_form_selection' ); ?>" name="<?php echo $this->get_field_name( 'a_form_selection' ); ?>">
        <option value=""></option>
        <?php foreach ($aforms_list as $aform) { ?>
          <option value="<?php echo($aform->ID); ?>" 
          <?php 
            if ($a_form_selection == $aform->ID) {
              echo ("selected");
            }
          ?>
          ><?php echo($aform->form_name); ?>
        </option>
        <?php }?>
      </select>
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['a_form_selection'] = ( ! empty( $new_instance['a_form_selection'] ) ) ? strip_tags( $new_instance['a_form_selection'] ) : '';

    return $instance;
  }

} // class Foo_Widget

function aforms_register_form_widget() {
  register_widget( 'AFormFormWidget' );
}

add_action("init", "a_form_ajax_responder");
function a_form_ajax_responder() {
  // Check if ajax request
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Further checks to see if this really is an A Form post.
    if (isset($_POST["send_a_form"]) && $_POST["send_a_form"] != "") {
      // It happens to be an A Form post. Not only that, but a A Form ajax post.
      // So render A Form.
      $atts = array("id" => $_POST["send_a_form"]);
      echo a_form_shortcode($atts);
      exit;
    }
  }
}

add_shortcode( 'a-form', 'a_form_shortcode' );

function a_form_shortcode($atts) {
  if (is_plugin_active("tom-m8te/tom-m8te.php") && is_plugin_active("jquery-ui-theme/jquery-ui-theme.php")) {

    $captcha_valid = true;
    $form_valid = false;
    $nonce_passed = true;
    $mail_message = "";
    $return_content = "";
    $attachment_urls = array();
    
    $form = tom_get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";

    // Check to see if User submits a form action.
    if (isset($_POST["send_a_form"]) && ($atts["id"] == $_POST["send_a_form"])) {

      // User has submitted an aform.
      $form_valid = AFormValidation::is_valid($atts);
      
      // Check to see if the user has clicked the Send button and check to see if the form is using a captcha.
      if (isset($_POST["action"]) && $_POST["action"] == "Send" && isset($_POST[$form_name."captcha"]) && $form->include_captcha) {
        $captcha_valid = AFormValidation::is_valid_captcha($atts);
      }
      
      // Check to see if form is valid.
      $nonce_passed = wp_verify_nonce($_REQUEST["_wpnonce"], "a-forms-contact-a-form");
      if ($nonce_passed && $form_valid && $captcha_valid) {
        try {
          $attachment_urls = AFormController::formAction($atts);
        } catch(Exception $e) {
          $form_valid = false;
        }
        
        // Form is valid.
        if (($_POST["action"]) == "Send") {
          $mail_message = AFormController::submitAction($atts);
        }
      } else {

        // Check to see if the input field values are valid, but not the wpnonce value.
        if ($form_valid && $captcha_valid && $nonce_passed == false) {
          // The input field values are valid except the wpnonce value. Therefore there must have been a cross site spam attack. So display fail send email message.
          $return_content .= "<div class='a-form error'>Failed to send your message. Please try again later.</div>";
        }
        $form_valid = false;

      }

    } else {
      $_SESSION["a_forms_referrer"] = $_SERVER["HTTP_REFERER"];
    }

    if (preg_match("/class='success'/", $mail_message)) {
      return $mail_message;
    } else {
      return $mail_message.AFormPage::render_form($atts, $return_content, $form_valid, $attachment_urls);
    }
  }
}

add_action('wp_head', 'add_a_forms_js_and_css');
function add_a_forms_js_and_css() { 
  wp_enqueue_script('jquery');

  wp_register_script("a-forms-ajax-form", plugins_url("/js/jquery-form.js", __FILE__));
  wp_enqueue_script("a-forms-ajax-form");

  wp_register_script("a-forms", plugins_url("/js/application.js", __FILE__));
  wp_enqueue_script("a-forms");

  wp_register_script("jquery-placeholder", plugins_url("/js/jquery-placeholder.js", __FILE__));
  wp_enqueue_script("jquery-placeholder");

  wp_localize_script( 'a-forms', 'AFormsAjax', array(
    "base_url" => get_option('siteurl'),
  ));

  wp_register_style("a-forms", get_template_directory_uri().'/aforms_css/'.get_option("aform_current_css_file"));
  wp_enqueue_style("a-forms");
} 

function aform_field_name($form, $field_name) {
  return "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_".$field_name;
}

// Copy directory to another location.
function aform_copy_directory($src,$dst) { 
    $dir = opendir($src); 
    try{
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    aform_copy_directory($src . '/' . $file,$dst . '/' . $file); 
                } else { 
                    copy($src . '/' . $file,$dst . '/' . $file);
                } 
            }   
        }
        closedir($dir); 
    } catch(Exception $ex) {
        return false;
    }
    return true;
}

?>