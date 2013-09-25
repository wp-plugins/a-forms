<?php
final class AdminAFormsPage {
	public static function indexPage() { 
		AdminAFormsPage::common_header();
		?>
			<div class="postbox " style="display: block; ">
	    <div class="inside">
	      <?php tom_generate_datatable("a_form_forms", array("ID", "form_name", "include_admin_in_emails", "to_email", "tracking_enabled"), "ID", "", array("form_name ASC"), __AFORMS_DEFAULT_LIMIT__, get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php", false, true, true, true, true);   ?>
	    </div>
	    </div>
	  </div>
    <?php
	}

	public static function editPage() {
		AdminAFormsPage::common_header();
		// Display Edit Page
    $a_form = tom_get_row_by_id("a_form_forms", "*", "ID", ($_GET["id"]));
    ?>
      <div class="postbox " style="display: block; ">
      <div class="inside">
        <form action="" method="post">
          <?php AdminAFormsPage::render_admin_a_form_forms_form($a_form, "Update"); ?>
        </form>
      </div>
      </div>
		</div>
		<?php
	}

	public static function newPage() {
		AdminAFormsPage::common_header();
		?>
	    <div class="postbox " style="display: block; ">
      <div class="inside">
        <form action="" method="post">
          <?php 

          if (!isset($_POST["to_email"])) {
            $_POST["to_email"] = get_option("admin_email");
          }
          if (!isset($_POST["show_section_names"])) {
            $_POST["show_section_names"] = "1";
          }
          if (!isset($_POST["tracking_enabled"])) {
            $_POST["tracking_enabled"] = "1";
          }

          AdminAFormsPage::render_admin_a_form_forms_form(null, "Create"); ?>
        </form>
      </div>
      </div>
		</div>
		<?php
	}

	public static function common_header() { ?>
		<div class="wrap a-form">
	  <h2>A Forms <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=new">Add New Form</a></h2>
	  <?php

	  if (isset($_GET["message"]) && $_GET["message"] != "") {
	    echo("<div class='updated below-h2'><p>".($_GET["message"])."</p></div>");
	  }
	}

	public static function render_admin_a_form_forms_form($instance, $action) { ?>
    <div id="setting_column">
	  <?php
		  tom_add_form_field($instance, "hidden", "ID *", "ID", "ID", array(), "span", array("class" => "hidden"));
		  tom_add_form_field($instance, "text", "Name *", "form_name", "form_name", array("class" => "text"), "p", array());

		  if (get_option("a_forms_admin_email") != "") {
        tom_add_form_field($instance, "checkbox", "Send Emails To", "include_admin_in_emails", "include_admin_in_emails", array(), "p", array(), array("1" => "Admin User"));
      }
      tom_add_form_field($instance, "text", "Send Emails To *", "to_email", "to_email", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "CC Emails To", "to_cc_email", "to_cc_email", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "BCC Emails To", "to_bcc_email", "to_bcc_email", array("class" => "text"), "p", array());
      
      tom_add_form_field($instance, "text", "Email Subject *", "subject", "subject", array("class" => "text"), "p", array());
      
      if ($instance != null) {
        $field_options = array("" => "");
        $fields = tom_get_results("a_form_fields", "*", "form_id=".$instance->ID);
        foreach ($fields as $field) {
          $field_options[$field->FID] = $field->field_label;
        }
        tom_add_form_field($instance, "select", "From Name Field", "field_name_id", "field_name_id", array(), "p", array(), $field_options);
        tom_add_form_field($instance, "select", "From Email Field", "field_email_id", "field_email_id", array(), "p", array(), $field_options);
        tom_add_form_field($instance, "select", "Include In Subject Field", "field_subject_id", "field_subject_id", array(), "p", array(), $field_options);

      }

      tom_add_form_field($instance, "checkbox", "Section Names", "show_section_names", "show_section_names", array(), "p", array(), array("1" => "Visible"));
      tom_add_form_field($instance, "radio", "Confirmation Emails", "send_confirmation_email", "send_confirmation_email", array(), "p", array(), array("1" => "Send", "0" => "Don't Send"));
      tom_add_form_field($instance, "text", "Confirmation From Email", "confirmation_from_email", "confirmation_from_email", array("class" => "text"), "p", array(), array());

      tom_add_form_field($instance, "textarea", "Successful Message", "success_message", "success_message", array(), "p", array(), array());
      tom_add_form_field($instance, "text", "Successful Redirect URL", "success_redirect_url", "success_redirect_url", array("class" => "text"), "p", array(), array());
      
      tom_add_form_field($instance, "checkbox", "Tracking", "tracking_enabled", "tracking_enabled", array(), "p", array(), array("1" => "Enabled"));

      tom_add_form_field($instance, "checkbox", "Ajax", "enable_ajax", "enable_ajax", array(), "p", array(), array("1" => "Enabled"));  

      tom_add_form_field($instance, "checkbox", "Captcha", "include_captcha", "include_captcha", array(), "p", array(), array("1" => "Include Before Send Button"));

      tom_add_form_field($instance, "select", "Captcha Type", "captcha_type", "captcha_type", array(), "p", array("id" => "captcha_type_container"), array("0" => "Securimage Captcha", "1" => "Math Captcha"));    

	  ?>
    <input type="hidden" name="action" value="<?php echo($action); ?>" />
	  <p><input type="submit" name="sub_action" value="<?php echo($action); ?>" /> <?php if ($instance != null) { ?><input type="submit" name="sub_action" value="Save and Finish" /><?php } ?></p>
    </div>
    <div id="form_column">
	  <?php if ($action == "Update") { ?>
	    <h2 id="sections_heading">Sections <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=new&controller=AFormSections&form_id=<?php echo($instance->ID); ?>">Add New</a></h2>
      <?php AdminAFormsPage::render_admin_a_form_fields_form($instance, $action); ?>
    <?php } ?>
    </div>
    <?php
	}

  public static function render_admin_a_form_fields_form($instance, $action) {

    if ($instance != null) { ?>
      <h2><?php echo $instance->form_name; ?></h2>
    <?php } 
    ?>
    
    <?php
    tom_add_form_field($instance, "hidden", "ID", "ID", "ID", array(), "span", array("class" => "hidden"));

    ?>
    <ul id="fields_row_clone">
      <li class="shiftable">
        <?php
          AdminAFormFieldsPage::render_admin_a_form_fields_row(null, "-1");
        ?>
      </li>
    </ul>
    
    <p>Use shortcode [a-form id="<?php echo($instance->ID); ?>"][/a-form] in your page/post to use the form.</p>

    <ul id="fields_sortable">
      <?php
        $sections = tom_get_results("a_form_sections", "*", "form_id=".$instance->ID, $order_array = array("section_order ASC"), $limit = "");
        $index = 0;
        foreach ($sections as $section) { ?>
          <li class='shiftable section-heading' id="section_id_<?php echo($section->ID); ?>">
            <h3><?php echo($section->section_name); ?>
              <a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&controller=AFormSections&id=<?php echo($section->ID); ?>">Edit</a>
            </h3>
            <a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=delete&controller=AFormSections&id=<?php echo($section->ID); ?>" class="delete">Delete</a>
          </li>
          <?php
          $fields = tom_get_results("a_form_fields", "*", "section_id=".$section->ID, $order_array = array("field_order ASC"), $limit = "");

          foreach ($fields as $field) { ?>
            <li id="<?php echo($field->FID); ?>" class="shiftable">
              <?php
                AdminAFormFieldsPage::render_admin_a_form_fields_row($field, $index);
                $index++;
              ?>
            </li>
          <?php }
        } ?>

    </ul>
    <?php if ($instance != null) { ?>
      <p class="actions"><a href='#' id="new_form_row">New Field</a></p>
    <?php } ?>
    <input type="hidden" name="action" value="<?php echo($action); ?>" />
    <p id="aform_save_and_continue_panel"><input type="submit" name="sub_action" value="<?php echo($action); ?>" /> <?php if ($instance != null) { ?><input type="submit" name="sub_action" value="Save and Finish" /><?php } ?></p>

    <?php
  }

}
?>