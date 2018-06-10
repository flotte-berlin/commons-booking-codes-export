<form method="POST">
<input name="page" value="cb_codes" type="hidden">
<input name="item-filter" value="<?= $item_filter ?>" type="hidden">
<input name="action" value="cb-codes-export" type="hidden">
<label style="padding-bottom: 10px;" for="cb-codes-export"><?= __('BOOKING_CODES_FROM', 'commons-booking-codes-export') ?> <b>"<?= $post_title ?>"</b></label><br>
<input style="padding-bottom: 10px;" type="checkbox" name="export-include-location" value="1">&nbsp;<?= __('INCLUDE_LOCATION_NAME', 'commons-booking-codes-export') ?><br>
<label style="padding-bottom: 10px;"><?= __('FOR_PERIOD', 'commons-booking-codes-export') ?>&nbsp;</label>
<input type="date" name="export-start-date" value="<?= $start_date->format('Y-m-d') ?>">&nbsp;<?= __('UNTIL', 'commons-booking-codes-export') ?>&nbsp;
<input type="date" name="export-end-date" value="<?= $end_date->format('Y-m-d') ?>">&nbsp;<?= __('AS_CSV', 'commons-booking-codes-export') ?>&nbsp;
<input id="cb-codes-export" class="button action" value="<?= __('EXPORT', 'commons-booking-codes-export') ?>" type="submit">
</form>

<script>

  function start_csv_export(csv) {
    setTimeout(function() {
      var blob = new Blob([csv]);
      var a = window.document.createElement("a");
      a.href = window.URL.createObjectURL(blob, {type: "text/plain"});
      a.download = "<?= __('BOOKING_CODES', 'commons-booking-codes-export') ?>_<?= $post_title ?>_<?= $start_date->format('Y-m-d') ?>_<?= $end_date->format('Y-m-d') ?>.csv";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }, 0);

  }

</script>
