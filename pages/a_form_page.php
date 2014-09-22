<?php
final class AFormPage {
	public static function render_form($atts, $return_content, $form_valid, $attachment_urls) {
    $aform_form_nonce = wp_create_nonce( "a-forms-contact-a-form" );
		$form = AFormsTomM8::get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";

    $sections = AFormsTomM8::get_results("a_form_sections", "*", "form_id='".$atts["id"]."'", array("section_order ASC"));
		if (isset($_POST["send_a_form_section"])) {
      $section_index = ($_POST["send_a_form_section"]);
    } else {
      $section_index = 0;
    }

    if(!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      // If not a ajax request.
      $ajax_class = "";
      if ($form->enable_ajax == "1") {
        $ajax_class = "ajaxified";
      }

      $return_content .= "<form id='".str_replace(" ", "_", strtolower($form->form_name))."' method='post' class='a-form $ajax_class' enctype='multipart/form-data'>";
    }
    $return_content .= "<input type='hidden' name='_wpnonce' value='".$aform_form_nonce."'/>";
    $return_content .= "<input type='hidden' name='ga_category' value='".$form->ga_category."'/>";
    $return_content .= "<input type='hidden' name='ga_action' value='".$form->ga_action."'/>";
    $return_content .= "<input type='hidden' name='ga_label' value='".$form->ga_label."'/>";
    $return_content .= "<input type='hidden' name='ga_value' value='".$form->ga_value."'/>";

    if ($form->multipage_sections == "1") {
      // Get next section
      if (($_POST["action"]) == "Next") {
        if ($form_valid) {
          $section_index++;
        } 
      }

      // Get previous section.
      if (($_POST["action"]) == "Back") {
        $section_index--;
      }

      $section = $sections[$section_index];

      $return_content .= AFormPage::render_a_form_one_section_per_page($atts, $form, $form_name, $section, $return_content);
    } else {

      // Render all sections on the same page.
      $sections_content = "";
      foreach ($sections as $section) {
        $sections_content .= "<fieldset id='".preg_replace("/\?|!/", "", str_replace(" ", "_", strtolower($section->section_name)))."'>";
        $sections_content .= AFormPage::render_a_form_section_html($form, $form_name, $section, "");
        $sections_content .= "</fieldset>";
      }
      $return_content .= $sections_content;
    }

    $input_attachment_urls = "";
    if (count($attachment_urls) > 0) {
      $attachment_urls = array_filter( $attachment_urls, 'strlen' );
      $input_attachment_urls = implode("::", str_replace("\\\\", '\\', $attachment_urls));
    }
    $return_content .= "<input type='hidden' name='a_form_attachment_urls' value='".$input_attachment_urls."' />";
    $return_content .= "<input type='hidden' name='a_form_referrer' value='".esc_url($_SESSION["a_forms_referrer"])."' />";
    $return_content .= "<fieldset class='submit'><div>";

    $return_content .= "<input type='hidden' name='send_a_form_section' value='".$section_index."' />";
    $return_content .= "<input type='hidden' name='send_a_form' value='".$atts["id"]."' />";

    // Add action buttons
    // Check if more then one section
    if (count($sections) > 1) {
      // There is more then one section.

      if ($form->multipage_sections != "1" || ($section_index+1) == count($sections)) {
        // Looking at the last section.
        $return_content .= AFormPage::render_a_form_submit_html($form, $form_name);
      } else {
        // Not looking at the last section.
        $return_content .= "<input type='submit' name='action' value='Next' class='next'/>";
      }

    } else {
      // Only one section.
      $return_content .= AFormPage::render_a_form_submit_html($form, $form_name);
    }

    // Check which section your currently looking at.
    if ($section_index > 0) {
      // Not looking at the first section.
      $return_content .= "<input type='submit' name='action' value='Back' class='prev'/>";
    }

    $return_content .= "</div></fieldset>";


    if(!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      $return_content .= "</form>";
    }

    return $return_content;
	}


  function render_a_form_one_section_per_page($atts, $form, $form_name, $section, $return_content) {
    // We only want to display one section per page.

    $return_content .= "<fieldset id='".preg_replace("/\?|!/", "", str_replace(" ", "_", strtolower($section->section_name)))."'>";

    // Navigate through all the other sections and make all fields hidden.
    $hidden_fields = AFormsTomM8::get_results("a_form_fields", "*", "form_id = '".$atts["id"]."' AND section_id <> '".$section->ID."'");
    foreach ($hidden_fields as $field) {
      $field_name = str_replace(" ", "_", strtolower($field->field_label));
      ob_start();
      
      if ($field->field_type == "checkbox") {
        $i = 0;
        foreach (explode(",", $field->value_options) as $key) {
          AFormsTomM8::add_form_field(null, "hidden", $field->field_label, $form_name.$field_name."_".$i, $form_name.$field_name."_".$i, array(), "p", array(), array());  
          $i++;
        }
      } else {

        AFormsTomM8::add_form_field(null, "hidden", $field->field_label, $form_name.$field_name, $form_name.$field_name, array(), "p", array(), array());
        
      }
      
      $return_content .= ob_get_contents();
      ob_end_clean();
    }

    $return_content .= AFormPage::render_a_form_section_html($form, $form_name, $section, $return_content);

    return $return_content .= "</fieldset>";
  }



  function render_a_form_section_html($form, $form_name, $section, $return_content) {
    $fields = AFormsTomM8::get_results("a_form_fields", "*", "section_id='".$section->ID."'", array("field_order ASC"));

    // Render form fields.
    if ($form->show_section_names) {
      $return_content .= "<legend>".$section->section_name."</legend>";
    }

    foreach ($fields as $field) {
      $field_name = str_replace(" ", "_", strtolower($field->field_label));
      $value_options = array();
      if ($field->value_options != "") {
        $options = explode(",", $field->value_options);
        foreach($options as $option_with_label) {
          $temp_array = explode(":", $option_with_label);
          $option = $temp_array[1];
          $value = $temp_array[0];
          if ($option == "") {
            $option = $value;
          }
          $value_options[$option] = $value;
        }
      }
      $field_label = $field->field_label;
      if (preg_match("/required/",$field->validation)) {
        $field_label .= "<abbr title='required'>*</abbr>";
      }

      ob_start();
      if ($field->field_type == "file" && $field->file_ext_allowed != "") {
        echo("<div>");
      } 
      $error_class = "";
      if (isset($_SESSION[$form_name.$field_name."_error"])) {
        $error_class = "error";
      }

      AFormsTomM8::add_form_field(null, $field->field_type, $field_label, $form_name.$field_name, $form_name.$field_name, array("class" => $field->field_type." ".$field->validation), "div", array("class" => $error_class), $value_options);
      if ($field->field_type == "file" && $field->file_ext_allowed != "") {
        $extensions_allowed = $field->file_ext_allowed;
        $extensions_allowed = preg_replace('/(\s)+/',' ', $extensions_allowed);
        $extensions_allowed = preg_replace('/(\s)+$/', '', $extensions_allowed);
        $extensions_allowed = preg_replace('/(\s)/', ', ', $extensions_allowed);
        $extensions_allowed = preg_replace('/ \.([a-z|A-Z])*$/', ' and $0', $extensions_allowed);
        $extensions_allowed = preg_replace('/,(\s)+and/', ' and', $extensions_allowed);
        echo("<span class='file-ext-allowed'>Can only accept: ".$extensions_allowed."</span>");
        echo("</div>");
      }

      $return_content .= ob_get_contents();
      ob_end_clean();
    } 
    return $return_content; 
  }

	function render_a_form_submit_html($form, $form_name) {
	  $return_content = "";
	  if ($form->include_captcha) {
      $error_class = "";
      if (isset($_SESSION[$form_name."captcha_error"])) {
        $error_class = "error";
      }
	    ob_start();
	    if ($form->captcha_type == "1") {

	      AFormsTomM8::add_form_field(null, "captcha", "Captcha", AFormPage::aform_field_name($form, "captcha"), AFormPage::aform_field_name($form, "captcha"), array(), "div", array("class" => "captcha $error_class"));

	    } else if ($form->captcha_type == "2") {

	      $first_number = $_POST[AFormPage::aform_field_name($form, "captcha_first_number")] = rand(1, 20);
	      $second_number = $_POST[AFormPage::aform_field_name($form, "captcha_second_number")] = rand(1, 20);

	      AFormsTomM8::add_form_field(null, "hidden", "First number", AFormPage::aform_field_name($form, "captcha_first_number"), 
	        AFormPage::aform_field_name($form, "captcha_first_number")
	        , array(), "div", array());
	      AFormsTomM8::add_form_field(null, "hidden", "Second number", AFormPage::aform_field_name($form, "captcha_second_number"), AFormPage::aform_field_name($form, "captcha_second_number"), array(), "div", array());

	      AFormsTomM8::add_form_field(null, "text", "What is ".$first_number." + ".$second_number, AFormPage::aform_field_name($form, "captcha"), AFormPage::aform_field_name($form, "captcha"), array(), "div", array("class" => "captcha $error_class"));
	    }
	    $return_content .= ob_get_contents();
	    ob_end_clean();
	  }
	  $return_content .= "<input type='submit' name='action' value='Send' class='send'/>";
	  return $return_content;
	}

	function aform_field_name($form, $field_name) {
	  return "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_".$field_name;
	}
}
?>