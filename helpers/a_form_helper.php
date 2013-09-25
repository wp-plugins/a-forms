<?php
final class AFormHelper {

  // Creates an email as text, records which files were uploaded and returns the email as a string.
  // Add the fields submitted into a global variable called "a_form_field_values".
  // Add the file names uploaded into a global variable called "smtp_attachment_urls".
	public static function create_email_content($atts) {
    $form = tom_get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";
    $field_values = array();
    $smtp_attachment_urls = array();
    // Construct email content.
    $all_fields = tom_get_results("a_form_fields", "*", "form_id='".$atts["id"]."'", array("field_order"));
    foreach ($all_fields as $field) {
      $field_name = str_replace(" ", "_", strtolower($field->field_label));

      if ($field->field_type == "checkbox") {
        $i = 0;
        $email_content .= $field->field_label.": ";
        $answers = "";
        foreach (explode(",", $field->value_options) as $key) {
          if (($_POST[$form_name.$field_name."_".$i]) != "") {
            $content = str_replace('\"', "\"", ($_POST[$form_name.$field_name."_".$i]));
            $content = str_replace("\'", '\'', $content);
            $answers .= $content.", ";
          }
          $i++;
        }
        $email_content .= preg_replace("/, $/", "", $answers);
        $email_content .= "\n\n";
        $field_values[$field_name] = $answers;
      } else if ($field->field_type == "file") {

        if (isset($GLOBALS["upload_".$form_name.$field_name])) {
          // This file was just uploaded.
          array_push($smtp_attachment_urls, $GLOBALS["upload_".$form_name.$field_name]);
          $field_values[$field_name] = $GLOBALS["upload_".$form_name.$field_name];
        }

        // These files may have been uploaded in a previous section.
        // Rip up $attachment_urls so we're left with only the paths to the files uploaded.
        if (($_POST["a_form_attachment_urls"]) != "") {
          $records = explode("::", ($_POST["a_form_attachment_urls"]));
          foreach ($records as $record) {
            $key_value = explode("=>", $record);
            if ($key_value[0] == $form_name.$field_name && $key_value[1] != "") {
              array_push($smtp_attachment_urls, $key_value[1]);
              $field_values[$field_name] = $key_value[1];
            }
          }
        }
        
      } else {
        $content = str_replace('\"', "\"", ($_POST[$form_name.$field_name]));
        $content = str_replace("\'", '\'', $content);
        $email_content .= $field->field_label.": ".$content."\n\n";
        $field_values[$field_name] = $content;
      }
      
    }

    $GLOBALS["a_form_field_values"] = $field_values;
    $GLOBALS["smtp_attachment_urls"] = array_unique($smtp_attachment_urls);
    return $email_content;
  }

	// Upload file.
  public static function upload_file($field_name, $extensions_allowed) {
    $filedest = "";
    $uploadfiles = $_FILES[$field_name];

    if (is_array($uploadfiles)) {

      foreach ($uploadfiles['name'] as $key => $value) {

        // look only for uploded files
        if ($uploadfiles['error'][$key] == 0) {

          $filetmp = $uploadfiles['tmp_name'][$key];

          //clean filename and extract extension
          $filename = $uploadfiles['name'][$key];

          // get file info
          // @fixme: wp checks the file extension....
          $filetype = wp_check_filetype( basename( $filename ), null );
          $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
          $filename = $filetitle . '.' . $filetype['ext'];
          $upload_dir = wp_upload_dir();
          // echo $upload_dir;
          /**
           * Check if the filename already exist in the directory and rename the
           * file if necessary
           */
          $i = 0;
          while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
            $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
            $i++;
          }
          $filedest = $upload_dir['path']. '/' . $filename;

          /**
           * Check write permissions
           */
          if ( !is_writeable( $upload_dir['path'] ) ) {
            throw new Exception(' Unable to write to directory %s. Is this directory writable by the server? ');
            return;
          }

          // Check if extension allowed
          if ($extensions_allowed == "" || preg_match("/\.".$filetype['ext']."/", $extensions_allowed)) {
            /**
             * Extension allowed
             * Save temporary file to uploads dir
             */
            if ( !@move_uploaded_file($filetmp, $filedest) ){
              throw new Exception(" Error, the file $filetmp could not moved to : $filedest. ");
              continue;
            }
          } else {
            $extensions_allowed = preg_replace('/(\s)+/',' ', $extensions_allowed);
            $extensions_allowed = preg_replace('/(\s)+$/', '', $extensions_allowed);
            $extensions_allowed = preg_replace('/(\s)/', ', ', $extensions_allowed);
            $extensions_allowed = preg_replace('/ \.([a-z|A-Z])*$/', ' and $0', $extensions_allowed);
            $extensions_allowed = preg_replace('/,(\s)+and/', ' and', $extensions_allowed);
            if (preg_match("/ and /", $extensions_allowed)) {
              throw new Exception(" these are the only file extensions acceptable: ".trim($extensions_allowed).". ");  
            } else {
              throw new Exception(" ".trim($extensions_allowed)." is the only acceptable file type. ");
            }
            
          }
        }
      }   
    }

    return $filedest;
  }
}
?>