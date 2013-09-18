<?php
final class AdminAFormStylingController {
	public static function indexAction() {
		$css_content = file_get_contents(get_template_directory_uri()."/aforms_css/".get_option("aform_current_css_file"));
		if (isset($_POST["css_content"])) {
			$location = get_template_directory()."/aforms_css/".get_option("aform_current_css_file");
			$css_content = $_POST["css_content"];
			$css_content = str_replace('\"', "\"", $css_content);
			$css_content = str_replace("\'", '\'', $css_content);
			tom_write_to_file($css_content, $location);
			$_GET["message"] = "Update Complete";
		}
		AdminAFormStylingPage::indexPage($css_content);
	}
}
?>