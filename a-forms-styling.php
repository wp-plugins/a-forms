<?php
	
if (are_a_forms_dependencies_installed()) {
	add_action("admin_init", "register_a_form_style_scripts");
	function register_a_form_style_scripts() {
		wp_enqueue_script('jquery');
		wp_register_script("a-forms", plugins_url("js/application.js", __FILE__));
		wp_enqueue_script("a-forms");

		wp_register_style("a-forms", plugins_url("admin_css/style.css", __FILE__));
		wp_enqueue_style("a-forms");

		wp_localize_script( 'a-forms', 'AFormsAjax', array(
		  'ajax_url' => admin_url('admin-ajax.php')
		));
	}
	
	if (isset($_POST["action"]) && $_POST["action"] == "Reset") {
		aform_copy_directory(AdminAFormsPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  		
	}
		
	AdminAFormStylingController::indexAction();

	?>
<?php } ?>