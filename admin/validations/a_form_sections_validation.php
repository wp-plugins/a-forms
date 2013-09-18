<?php
final class AdminAFormSectionsValidation {

	public static function array_validation_rules() {
    return array(
      "section_name" => "required"
    );
  }

	public static function is_valid() {
		return tom_validate_form(AdminAFormSectionsValidation::array_validation_rules());
	}
}
?>