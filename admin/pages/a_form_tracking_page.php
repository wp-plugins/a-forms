<?php
final class AdminAFormTrackingPage {
	public static function indexPage() { ?>
		<div class="wrap">
		  <h2>Tracking</h2>		  
      <?php
      	tom_generate_datatable("a_form_forms", array("ID", "form_name"), "ID", "", array(), "30", "?page=a-forms/a-forms-tracking.php&controller=AFormTracking", true, false, false, false, true, "Y-m-d", array()); 
      ?>
		</div>
		<?php
	}

	public static function showPage() {
		?>
		  <form action="" method="post">
        <?php tom_add_form_field(null, "text", "Search Text", "search_text", "search_text", array(), "p", array()); ?>
        <?php tom_add_form_field(null, "text", "Date From", "search_date_from", "search_date_from", array("class" => "datepicker"), "p", array()); ?>
        <?php tom_add_form_field(null, "text", "Date To", "search_date_to", "search_date_to", array("class" => "datepicker"), "p", array()); ?>
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
      if ((tom_get_query_string_value("search_text")) != "") {
        $where_sql .= " AND content LIKE '%".(tom_get_query_string_value("search_text"))."%'";
      }

      if (((tom_get_query_string_value("search_date_from")) != null) && ((tom_get_query_string_value("search_date_to")) != null)) {
        $where_sql .= " AND (created_at BETWEEN '".(tom_get_query_string_value("search_date_from"))." 00:00:00' AND '".(tom_get_query_string_value("search_date_to"))." 23:59:59')";
      } else if ((tom_get_query_string_value("search_date_from")) != null) {
        $where_sql .= " AND created_at > '".(tom_get_query_string_value("search_date_from"))." 00:00:00'";
      } else if ((tom_get_query_string_value("search_date_to")) != null) {
        $where_sql .= " AND created_at < '".(tom_get_query_string_value("search_date_to"))." 23:59:59'";
      }

      $tracks = tom_get_results("a_form_tracks", "*", $where_sql, array("created_at DESC"), "$limit_clause OFFSET $offset");
      $fields = tom_get_results("a_form_fields", "*", "form_id=".($_GET["id"]), array());
      
      $total_tracks = count(tom_get_results("a_form_tracks", "*", $where_sql, array("created_at DESC")));

      if ($total_tracks > 0) {
        tom_generate_datatable_pagination("a_form_tracks", $total_tracks, $limit_clause, ($_GET["a_form_tracks_page"]), "?page=a-forms/a-forms-tracking.php&action=show&id=".($_GET["id"])."&search_text=".(tom_get_query_string_value("search_text"))."&search_date_from=".(tom_get_query_string_value("search_date_from"))."&search_date_to=".(tom_get_query_string_value("search_date_to")), "ASC", "top");
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
                  echo("<a href='".get_option("siteurl")."/wp-content/plugins/tom-m8te/tom-download-file.php?file=".$content."'>download</a>");
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
        tom_generate_datatable_pagination("a_form_tracks", $total_tracks, $limit_clause, $_GET["a_form_tracks_page"], "?page=a-forms/a-forms-tracking.php&action=show&id=".$_GET["id"], "ASC", "bottom");
    }
	}

	public static function viewPage() {
		$view = tom_get_row_by_id("a_form_tracks", "*", "ID", $_GET["id"]);
    echo "<p><textarea rows='40' cols='160'>".esc_html(stripcslashes($view->content))."</textarea></p>";
    echo("<p><a href='".get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms-tracking.php&action=show&id=".$view->form_id."'>Back</a></p>");
	}
}
?>