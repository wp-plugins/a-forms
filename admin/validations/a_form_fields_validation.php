<?php
final class AdminAFormFieldsValidation { 

  public static function array_validation_rules() {
    return array(
      "field_type" => "required"
    );
  }

	public static function is_valid() {
		return AFormsTomM8::validate_form(AdminAFormFieldsValidation::array_validation_rules());
	}
}
?>