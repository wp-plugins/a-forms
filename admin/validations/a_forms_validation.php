<?php
final class AdminAFormsValidation {

	public static function array_validation_rules() {
    return array(
      "form_name" => "required", 
      "to_email" => "multi-emails", 
      "to_cc_email" => "multi-emails", 
      "to_bcc_email" => "multi-emails", 
      "subject" => "required"
    );
  }

	public static function is_valid() {
		return tom_validate_form(AdminAFormsValidation::array_validation_rules());
	}
	
}
?>