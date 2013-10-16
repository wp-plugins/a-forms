<?php
final class AFormValidation {


  public static function is_valid_captcha($atts) {
    $form = tom_get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";
    $captcha_valid = false;
    // Check to see if the user has clicked the Send button and check to see if the form is using a captcha.
    if (isset($_POST["action"]) && $_POST["action"] == "Send" && isset($_POST[$form_name."captcha"]) && $form->include_captcha) {

      // User clicked on Send button and the form has a captcha.
      // Check the type of captcha.
      if ($form->captcha_type == "1") {
        // Form is using the Securimage Captcha.
        $captcha_valid = tom_check_captcha($form_name."captcha");
      } else if ($form->captcha_type == "2") {
        // Form is using the Math Captcha.

        // Check that the answer is first number plus second number.
        $captcha_valid = 
        (
          (
            ($_POST[aform_field_name($form, "captcha_first_number")]) 
            + 
            ($_POST[aform_field_name($form, "captcha_second_number")])
          ) 
          == ($_POST[aform_field_name($form, "captcha")])
        );

      }
    }

    // Check to see if captcha is valid.
    if ($captcha_valid == false) {
      // Captcha is invalid, so display error message.
      $_SESSION["a_form_".str_replace(" ", "_", strtolower($form->form_name))."_captcha_error"] = "invalid captcha code, try again!";
    }

    return $captcha_valid;
  }

  public static function is_valid($atts) {
    $validation_array = array();
    $form = tom_get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $sections = tom_get_results("a_form_sections", "*", "form_id='".$atts["id"]."'", array("section_order ASC"));

    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";

    $form_valid = true;
    $section_index = 0;

    if (isset($_POST["send_a_form_section"])) {
      $section_index = ($_POST["send_a_form_section"]);
    } else {
      $section_index = 0;
    }

    // Get this section.
    $section = $sections[$section_index];

    // Add validation for this section only.
    $fields = tom_get_results("a_form_fields", "*", "section_id='".$section->ID."'");
    foreach ($fields as $field) {
      $field_name = str_replace(" ", "_", strtolower($field->field_label));
      $validation_array[$form_name.$field_name] = $field->validation;
    }
    return tom_validate_form($validation_array);
  }
}
?>