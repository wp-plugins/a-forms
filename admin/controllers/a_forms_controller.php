<?php
final class AdminAFormsController {
	public static function indexAction() {
		$forms = tom_get_results("a_form_forms", "*", "");
    if (count($forms) == 0) {
      $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&action=new";
      echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
    } else {
    	AdminAFormsPage::indexPage();
    }
	}

	public static function editAction() {
		AdminAFormsPage::editPage();
	}

	public static function updateAction() {
		$send_confirmation_email_valid = true;
    if ($_POST["send_confirmation_email"] == "1" && $_POST["confirmation_from_email"] == "") {
        $_SESSION["confirmation_from_email_error"] = " must have a value. ";
        $send_confirmation_email_valid = false;
    } 
    $form_valid = AdminAFormsValidation::is_valid();

    $to_email_valid = true;
    if ($_POST["include_admin_in_emails"] != '1') {
      $to_email_valid = tom_validate_value("required", $_POST["to_email"], "to_email_error");
    }

    $fields_valid = AdminAFormFieldsController::updateAction();

    if ($send_confirmation_email_valid && $to_email_valid && $form_valid && $fields_valid) {

      $valid = tom_update_record_by_id("a_form_forms", 
      tom_get_form_query_strings("a_form_forms", array("created_at", "updated_at"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "ID", $_POST["ID"]);
      
      if ($valid && $fields_valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&message=Update Complete&action=edit&id=".$_POST["ID"]."";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&message=Update Complete";
        }
        
        echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
        exit;
      }
      
    }
    AdminAFormsPage::editPage();
	}

	public static function newAction() {
		AdminAFormsPage::newPage();
	}

	public static function createAction() {

		$send_confirmation_email_valid = true;
    if ($_POST["send_confirmation_email"] == "1" && $_POST["confirmation_from_email"] == "") {
        $_SESSION["confirmation_from_email_error"] = " must have a value. ";
        $send_confirmation_email_valid = false;
    } 
    $form_valid = AdminAFormsValidation::is_valid();

    if ($send_confirmation_email_valid && $form_valid) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = tom_insert_record("a_form_forms", 
        tom_get_form_query_strings("a_form_forms", array("ID", "created_at", "updated_at"), array("created_at" => $current_datetime)));
      
      if ($valid) {
        global $wpdb;
        $form_id = $wpdb->insert_id;
        tom_insert_record("a_form_sections", 
          array("section_name" => "Form Fields",
                "form_id" => $form_id, 
                "created_at" => $current_datetime
               )
        );
        $section_id = $wpdb->insert_id;
        
        tom_insert_record("a_form_fields", array("form_id" => $form_id, "validation" => "required", "field_label" => "Name", "field_type" => "text", "section_id" => $section_id, "created_at" => $current_datetime, "field_order" => 0));
        $from_name_id = $wpdb->insert_id;
        tom_insert_record("a_form_fields", array("form_id" => $form_id, "validation" => "required email", "field_label" => "Email", "field_type" => "text", "section_id" => $section_id, "created_at" => $current_datetime, "field_order" => 1));
        $from_email_id = $wpdb->insert_id;
        tom_insert_record("a_form_fields", array("form_id" => $form_id, "validation" => "required", "field_label" => "Subject", "field_type" => "text", "section_id" => $section_id, "created_at" => $current_datetime, "field_order" => 2));
        $from_subject_id = $wpdb->insert_id;
        tom_insert_record("a_form_fields", array("form_id" => $form_id, "validation" => "required", "field_label" => "Phone","field_type" => "text", "section_id" => $section_id, "created_at" => $current_datetime, "field_order" => 3));
        tom_insert_record("a_form_fields", array("form_id" => $form_id, "validation" => "required", "field_label" => "Message", "field_type" => "textarea", "section_id" => $section_id, "created_at" => $current_datetime, "field_order" => 4));

        $valid = tom_update_record_by_id("a_form_forms", array(
          "field_name_id" => $from_name_id,
          "field_email_id" => $from_email_id,
          "field_subject_id" => $from_subject_id
        ), "ID", $form_id);

        $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&id=".$form_id."&message=Record Created";
        echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
        exit;
      }

    }
    AdminAFormsPage::newPage();
	}

	public static function deleteAction() {
		// Delete record by id.
    tom_delete_record_by_id("a_form_forms", "ID", $_GET["id"]);
    tom_delete_record_by_id("a_form_sections", "form_id", $_GET["id"]);
    tom_delete_record_by_id("a_form_fields", "form_id", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&message=Record Deleted";
    echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
    exit;
	}

}
?>