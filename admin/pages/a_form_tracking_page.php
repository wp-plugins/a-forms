<?php
final class AdminAFormTrackingPage {
	public static function indexPage() { ?>
		<div class="wrap">
		  <h2>Tracking</h2>		  
      <?php
      	AFormsTomM8::generate_datatable("a_form_forms", array("ID", "form_name"), "ID", "", array(), "30", "?page=a-forms/a-forms-tracking.php&controller=AFormTracking", true, false, false, false, true, "Y-m-d", array()); 
      ?>
		</div>
		<?php
	}

	public static function showPage() {
		?>
		  <form action="" method="post">
        <?php AFormsTomM8::add_form_field(null, "text", "Search Text", "search_text", "search_text", array(), "p", array()); ?>
        <?php AFormsTomM8::add_form_field(null, "text", "Date From", "search_date_from", "search_date_from", array("class" => "datepicker"), "p", array()); ?>
        <?php AFormsTomM8::add_form_field(null, "text", "Date To", "search_date_to", "search_date_to", array("class" => "datepicker"), "p", array()); ?>
        <p><input type="submit" name="action" value="Search" /></p>
      </form>
      <?php
			$limit_clause = "10";
      
      $page_no = 0;
      if (isset($_GET["a_form_tracks_page"])) {
        $page_no = ($_GET["a_form_tracks_page"]);
      }
      $offset = $page_no * $limit_clause;
      $where_sql = "form_id=".($_GET["id"]);
      if ((AFormsTomM8::get_query_string_value("search_text")) != "") {
        $where_sql .= " AND content LIKE '%".(AFormsTomM8::get_query_string_value("search_text"))."%'";
      }

      if (((AFormsTomM8::get_query_string_value("search_date_from")) != null) && ((AFormsTomM8::get_query_string_value("search_date_to")) != null)) {
        $where_sql .= " AND (created_at BETWEEN '".(AFormsTomM8::get_query_string_value("search_date_from"))." 00:00:00' AND '".(AFormsTomM8::get_query_string_value("search_date_to"))." 23:59:59')";
      } else if ((AFormsTomM8::get_query_string_value("search_date_from")) != null) {
        $where_sql .= " AND created_at > '".(AFormsTomM8::get_query_string_value("search_date_from"))." 00:00:00'";
      } else if ((AFormsTomM8::get_query_string_value("search_date_to")) != null) {
        $where_sql .= " AND created_at < '".(AFormsTomM8::get_query_string_value("search_date_to"))." 23:59:59'";
      }

      $tracks = AFormsTomM8::get_results("a_form_tracks", "*", $where_sql, array("created_at DESC"), "$limit_clause OFFSET $offset");
      $fields = AFormsTomM8::get_results("a_form_fields", "*", "form_id=".($_GET["id"]), array());
      
      $total_tracks = count(AFormsTomM8::get_results("a_form_tracks", "*", $where_sql, array("created_at DESC")));

      if ($total_tracks > 0) {
        AFormsTomM8::generate_datatable_pagination("a_form_tracks", $total_tracks, $limit_clause, ($_GET["a_form_tracks_page"]), "?page=a-forms/a-forms-tracking.php&action=show&id=".($_GET["id"])."&search_text=".(AFormsTomM8::get_query_string_value("search_text"))."&search_date_from=".(AFormsTomM8::get_query_string_value("search_date_from"))."&search_date_to=".(AFormsTomM8::get_query_string_value("search_date_to")), "ASC", "top");
      ?>
        <table id="tracking">
          <thead>
            <tr>
              <td>ID</td>
              <?php           
                foreach ($fields as $field) {
                  echo("<th>".$field->field_label."</th>");
                }
              ?>
              <th>Referrer URL</th>
              <th>Date Sent</th>
              <th>View Form</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tracks as $track) {
              $fields_array = unserialize($track->fields_array); 
              echo("<tr><td>".$track->ID."</td>");
              foreach ($fields as $field) {
                $content = $fields_array[str_replace(" ", "_", strtolower($field->field_label))];
                echo("<td>");
                if ($content != "" && $field->field_type == "file") {
                  $tomm8te_nonce = wp_create_nonce( "tomm8te_download_file_nonce" );
                  echo("<a href='".get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&tomm8te_download=true&_tomm8te_nonce=".$tomm8te_nonce."&file=".$content."'>download</a>");
                } else {
                  echo(preg_replace("/, $/", "", esc_html($content)));
                }
                echo("</td>");
              }

              echo("<td>".$track->referrer_url."</td>");
              echo("<td>".gmdate("Y-m-d H:i:s", strtotime($track->created_at ))." GMT</td>");
              echo("<td><a href='".
get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms-tracking.php&action=view&id=".$track->ID."'>View</a></td>");
              echo("</tr>");
            }?>
          </tbody>
        </table>
        <?php
        AFormsTomM8::generate_datatable_pagination("a_form_tracks", $total_tracks, $limit_clause, $_GET["a_form_tracks_page"], "?page=a-forms/a-forms-tracking.php&action=show&id=".$_GET["id"], "ASC", "bottom");
    }
	}

	public static function viewPage() {
		$view = AFormsTomM8::get_row_by_id("a_form_tracks", "*", "ID", $_GET["id"]);
    echo "<p><textarea rows='40' cols='160'>".esc_html(stripcslashes($view->content))."</textarea></p>";
    echo("<p><a href='".get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms-tracking.php&action=show&id=".$view->form_id."'>Back</a></p>");
	}
}
?>