<?php

Class CB_Codes_Export {

  /**
  * loads booking codes export functionality - if user has export capability, add export form to code page
  */
  public function load_codes_export() {

    //load translation
    $lang_path = 'commons-booking-codes-export/languages/';
    load_plugin_textdomain( 'commons-booking-codes-export', false, $lang_path );

    add_action( 'toplevel_page_cb_codes', array($this, 'add_codes_export'));

  }

  /**
  * adds booking codes export functionality to codes page
  */
  public function add_codes_export() {
    $item_filter = isset($_REQUEST['item-filter']) ? (int) $_REQUEST['item-filter'] : null;
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    $start_date = isset($_REQUEST['export-start-date']) ? new DateTime($_REQUEST['export-start-date']) : new DateTime();
    $start_date_clone = new DateTime();
    $end_date = isset($_REQUEST['export-end-date']) ? new DateTime($_REQUEST['export-end-date']) : $start_date_clone->add(new DateInterval('P1M'));
    $include_location = isset($_REQUEST['export-include-location']) && $_REQUEST['export-include-location'] == '1' ? true : false;

    echo '<h2>' . __('CODE_EXPORT', 'commons-booking-codes-export') . '</h2>';

    //show export form
    if($item_filter) {

      //get item data (wp post)
      $post = get_post($item_filter);

      if($post && $post->post_type == 'cb_items') {

        $this->render_form($item_filter, $post->post_title, $start_date, $end_date);

        //retrieve booking code data for selected item and convert to csv text
        if($action == 'cb-codes-export') {

          //fetch booking codes from db
          $codes_result = $this->fetch_codes($item_filter, $start_date, $end_date);

          if($include_location) {

            foreach ($codes_result as &$code_result) {
              $code_result->location = $this->fetch_code_location_name($code_result);
            }

          }

          //create csv string
          $csv = $this->create_csv($codes_result, $include_location);

          //trigger download
          echo '<script>start_csv_export("' . $csv . '");</script>';
        }

      }
      else {
        echo '<p>' . __('NO_VALID_ITEM', 'commons-booking-codes-export') . '</p>';
      }

    }
    else {
      echo '<p>' . __('CHOOSE_ITEM', 'commons-booking-codes-export') . '</p>';
    }

  }

  /**
  * render the export form
  */
  private function render_form($item_filter, $post_title, $start_date, $end_date) {

    include_once( CB_CODES_EXPORT_PATH . 'templates/cb-codes-export.php');
  }

  /**
  * fetches booking codes from db for given item in period defined by start & end date
  */
  private function fetch_codes($item_filter, $start_date, $end_date) {
    global $wpdb;

    //get booking code data
    $table_name = $wpdb->prefix . 'cb_codes';
    $select_statement = "SELECT item_id, booking_date, bookingcode FROM $table_name WHERE item_id = %d ".
                        "AND booking_date BETWEEN '".$start_date->format('Y-m-d')."' ".
                        "AND '".$end_date->format('Y-m-d')."' ".
                        "ORDER BY booking_date ASC";

    $codes_result = $wpdb->get_results($wpdb->prepare($select_statement, $item_filter));

    return $codes_result;
  }

  /**
  * fetch the location name for the given $code based on booking date
  */
  private function fetch_code_location_name($code_result) {

    global $wpdb;

    $timeframes_table_name = $wpdb->prefix . 'cb_timeframes';
    $posts_table_name = $wpdb->prefix . 'posts';

    $select_statement = "SELECT wp_cb_timeframes.date_start, wp_cb_timeframes.date_end, wp_cb_timeframes.item_id, wp_posts.post_title " .
                        "FROM $timeframes_table_name INNER JOIN $posts_table_name ".
                        "ON $timeframes_table_name.location_id = $posts_table_name.id ".
                        "WHERE $timeframes_table_name.item_id = %d " .
                        "AND $timeframes_table_name.date_start <= '" . $code_result->booking_date . "' " .
                        "AND $timeframes_table_name.date_end >= '" . $code_result->booking_date . "' ".
                        "ORDER BY $timeframes_table_name.id DESC";

    $prepared_statement = $wpdb->prepare($select_statement, $code_result->item_id);
    $timeframes_result = $wpdb->get_row($prepared_statement);

    if(isset($timeframes_result)) {
      return $timeframes_result->post_title;
    }
    else {
      return 'unbekannt';
    }

  }

  /**
  * create csv string from given codes result
  */
  private function create_csv($codes_result, $include_location) {
    $csv =  __('DATE', 'commons-booking-codes-export') . ';';
    $csv .= __('BOOKING_CODE', 'commons-booking-codes-export') . ';';
    $csv .= $include_location ? __('LOCATION', 'commons-booking-codes-export') . ';' : '';
    $csv .= '\n';

    foreach ($codes_result as $code_result) {
      $booking_date = new DateTime($code_result->booking_date);
      $csv .= $booking_date->format('d.m.Y') . ';' . $code_result->bookingcode . ';';
      $csv .= $include_location ? $code_result->location . ';' : '';
      $csv .= '\n';
    }

    return $csv;
  }

}

?>
