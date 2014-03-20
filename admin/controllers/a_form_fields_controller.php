<?php
final class AdminAFormFieldsController {
	public static function updateAction() {
		//if (AFormFieldsValidation::is_valid()) {
      $field_valid = true;
      $fields = $_POST["FID"];
      $index = 0;
      
      foreach ($fields as $field) {
        if ($_POST["FID"][$index] != "") {
          $test_valid = AFormsTomM8::update_record_by_id("a_form_fields", 
            array(
              "section_id" => $_POST["section_id"][$index],
              "field_label" => $_POST["field_label"][$index],
              "field_type" => $_POST["field_type"][$index],
              "field_order" => $_POST["field_order"][$index],
              "validation" => $_POST["validation"][$index]." ".$_POST["validation_category"][$index],
              "value_options" => $_POST["value_options"][$index],
              "file_ext_allowed" => AFormsTomM8::get_query_string_value("file_ext_allowed", $index)
            ),
            "FID",
            $_POST["FID"][$index]
          );
          
          if (!$test_valid) {
            echo "FAIL";
            // If one of the records fails, then fail the lot.
            $field_valid = false;
          }
        }
        $index++;
      }
      return $field_valid;
    //} 
    //return false;
	}

	public static function createAction() {

	}

	public static function deleteAction() {
		// Delete record by id.
    $url = "";
    if (isset($_GET["fid"])) {
      AFormsTomM8::update_record_by_id("a_form_forms", array("field_name_id" => ""), "field_name_id", $_GET["fid"]);
      AFormsTomM8::update_record_by_id("a_form_forms", array("field_email_id" => ""), "field_email_id", $_GET["fid"]);
      AFormsTomM8::update_record_by_id("a_form_forms", array("field_subject_id" => ""), "field_subject_id", $_GET["fid"]);
      AFormsTomM8::delete_record_by_id("a_form_fields", "FID", $_GET["fid"]);
      $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&message=Record Deleted&id=".$_GET["form_id"]."";
      echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
    }
    exit;
	}
}
?>