<?php
// Check if the user is logged in
if (!is_user_logged_in()) {
    echo 'You must be logged in to create a checklist.';
    return;
}

// Form HTML
?>
<form id="checklist-creation-form" action="" method="post">
    <div>
        <label for="checklist_title">Checklist Title:</label>
        <input type="text" id="checklist_title" name="checklist_title" required>
    </div>
    <div>
        <label for="checklist_privacy">Privacy:</label>
        <select id="checklist_privacy" name="checklist_privacy">
            <option value="private">Private</option>
            <option value="public">Public</option>
        </select>
    </div>
    <div>
        <label for="checklist_numbered">Numbered Items:</label>
        <input type="checkbox" id="checklist_numbered" name="checklist_numbered">
    </div>
    <div>
        <label for="checklist_description">Include Descriptions:</label>
        <input type="checkbox" id="checklist_description" name="checklist_description">
    </div>
    <div>
        <label for="checklist_images">Number of Images (up to 4):</label>
        <input type="number" id="checklist_images" name="checklist_images" min="0" max="4">
    </div>
<div id="extra-fields-container">
    <label>Extra Fields:</label>
    <!-- Initially, no extra fields are present -->
</div>
<?php wp_nonce_field('create_checklist_action', 'create_checklist_nonce'); ?>
<button type="button" id="add-extra-field">Add Extra Field</button>

<script>
	
	
jQuery(document).ready(function($) {
    $('#checklist-creation-form').submit(function(event) {
        var isValid = true;

        // Validate Checklist Title
        var title = $('#checklist_title').val().trim();
        if (title === '') {
            alert('Please enter a title for your checklist.');
            isValid = false;
        }

        // Validate Number of Images
        var images = $('#checklist_images').val();
        if (images < 0 || images > 4) {
            alert('The number of images must be between 0 and 4.');
            isValid = false;
        }

        // Validate Number of Extra Text Fields
        $('.extra-field').each(function() {
            var fieldName = $(this).find('input[type="text"]').val().trim();
            if (fieldName === '') {
                alert('Please enter a name for all extra fields.');
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault(); // Prevent form submission
        }
    });
	
var fieldIndex = 0;

    $('#add-extra-field').click(function() {
        var fieldHTML = '<div class="extra-field" data-index="' + fieldIndex + '">' +
            '<input type="text" name="extra_fields[' + fieldIndex + '][name]" placeholder="Field Name">' +
            '<select name="extra_fields[' + fieldIndex + '][type]">' +
            '<option value="text">Text Field</option>' +
            '<option value="textarea">Text Area</option>' +
            '<option value="checkbox">Checkbox</option>' + // Added checkbox option
            '</select>' +
            '<button type="button" class="remove-field">Remove</button>' +
            '</div>';

        $('#extra-fields-container').append(fieldHTML);
        fieldIndex++;
    });

    $(document).on('click', '.remove-field', function() {
        $(this).closest('.extra-field').remove();
    });
});
</script>
    <div>
        <input type="submit" name="submit_checklist" value="Create Checklist">
    </div>
</form>
