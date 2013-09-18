<?php
final class AFormController {

  public static function submitAction($atts) {
    $attachment_urls = array();
    if (isset($_POST["a_form_attachment_urls"]) && $_POST["a_form_attachment_urls"] != "") {
      $attachment_urls = explode("::", ($_POST["a_form_attachment_urls"]));
    }
    $email_content = AFormHelper::create_email_content($atts);
    $form = tom_get_row_by_id("a_form_forms", "*", "ID", $atts["id"]);
    $form_name = "a_form_".str_replace(" ", "_", strtolower($form->form_name))."_";
    // User clicked Send, so since form is valid and they click Send, send the email.

    $subject = $form->subject;
    $from_name = "";
    $user_email = "";
    if ($form->field_name_id != "") {
      $row = tom_get_row_by_id("a_form_fields", "*", "FID", $form->field_name_id);
      $from_name = ($_POST[$form_name.str_replace(" ", "_", strtolower($row->field_label))]);
    }
    if ($form->field_email_id != "") {
      $row = tom_get_row_by_id("a_form_fields", "*", "FID", $form->field_email_id);
      $user_email = ($_POST[$form_name.str_replace(" ", "_", strtolower($row->field_label))]);
    }
    if ($form->field_subject_id != "") {
      $row = tom_get_row_by_id("a_form_fields", "*", "FID", $form->field_subject_id);
      if (isset($_POST[$form_name.str_replace(" ", "_", strtolower($row->field_label))])) {
        $subject .= " - ".($_POST[$form_name.str_replace(" ", "_", strtolower($row->field_label))]);
      }
    }

    if ($form->confirmation_from_email != "") {
      $from_email = $form->confirmation_from_email;
    }

    // Send Email.
    $cc_emails = $form->to_cc_email;
    if ($user_email != "" && $form->send_confirmation_email) {
      if ($cc_emails == "") {
        $cc_emails .= $user_email;
      } else {
        $cc_emails .= ", ".$user_email;
      }
    }
    if ($cc_emails == "") {
      $cc_emails .= $from_email;
    } else {
      $cc_emails .= ", ".$from_email;
    }

    $secure_algorithms = array();
    if (get_option("a_forms_enable_tls")) {
      $secure_algorithms["tls"] = "tls";
    }
    if (get_option("a_forms_enable_ssl")) {
      $secure_algorithms["ssl"] = "ssl";
    }

    $mail_message = tom_send_email(false, get_option("a_forms_admin_email").", ".$form->to_email, $cc_emails, $form->to_bcc_email, $from_email, $from_name, $subject, $email_content, "", $GLOBALS["smtp_attachment_urls"], get_option("a_forms_smtp_auth"), get_option("a_forms_mail_host"), get_option("a_forms_smtp_port"), get_option("a_forms_smtp_username"), get_option("a_forms_smtp_password"), $secure_algorithms);        
    
    if ($mail_message == "<div class='success'>Message sent!</div>") {

      if ($form->success_message != "") {
        $mail_message = "<div class='success'>".$form->success_message."</div>";
      }

      if ($form->tracking_enabled) {
        tom_insert_record("a_form_tracks", array("created_at" => $current_datetime, "form_id" => ($_POST["send_a_form"]), "content" => $email_content, "track_type" => "Successful Email", "referrer_url" => $_SERVER["HTTP_REFERER"], "fields_array" => serialize($field_values)));  
      }        

      if ($form->success_redirect_url != "") {
        tom_javascript_redirect_to($form->success_redirect_url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
      }

    } else {
      if ($form->tracking_enabled) {
        tom_insert_record("a_form_tracks", array("created_at" => $current_datetime, "form_id" => ($_POST["send_a_form"]), "content" => "Error Message: ".$mail_message.".\n\nContent: ".$email_content, "track_type" => "Failed Email", "referrer_url" => $_SERVER["HTTP_REFERER"], "fields_array" => serialize($field_values)));
      }
    }

    return $mail_message;

  }

}
?>