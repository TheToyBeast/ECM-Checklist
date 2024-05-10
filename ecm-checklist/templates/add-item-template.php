<?php
// Include WordPress functions and methods
require_once(ABSPATH . 'wp-load.php');
echo 'Hello';
// Check for the checklist ID in the query string
$checklist_id = isset($_GET['checklist']) ? intval($_GET['checklist']) : 0;

// You can include additional PHP logic here to fetch checklist details
// based on the $checklist_id, and then use those details within your form.

?>
<form id="add-item-form" method="post" action="">
    <!-- Your form fields go here -->
    <input type="hidden" name="checklist_id" value="<?php echo esc_attr($checklist_id); ?>">
    <!-- Other form inputs -->
    <input type="submit" name="submit_item" value="Add Item">
</form>