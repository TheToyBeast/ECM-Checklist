jQuery(document).ready(function ($) {
    $('#add-product-row').click(function (e) {
        e.preventDefault();
        var rowHtml = '' + '<div class="product-row">' + '<div class="row1">' + '<label>Number:</label><br>' + '<input type="number" name="product_number[]" value=""><br>' + '<label>Name:</label><br>' + '<input type="text" name="product_name[]" value=""><br>' + '<label>Product Link:</label><br>' + '<input type="text" name="product_link[]" value=""><br>' + '</div>' + '<div class="row3">' + '<label>Short Description:</label><br>' + '<input type="text" name="product_description[]" value=""><br>' + '<label>Image:</label><br>' + '<input id="upload_image_button" type="button" class="button upload_image_button" value="Add Image" />' + '<input type="hidden" name="product_image[]" value=""><br>' + '<img class="product_image_display" src="" style="max-width: 100px; max-height: 100px;"/><br>' + '</div>' + '<button class="remove-row button">Remove Item</button>' + '</div>';
        $('#product-list-wrapper').append(rowHtml);
    });
    $(document).on('click', '.remove-row', function (e) {
        e.preventDefault();
        $(this).closest('.product-row').remove();
    });
    $('.upload_image_button').each(function () {
        var imageUrl = $(this).next().val();
        if (imageUrl) {
            $(this).val('Remove Image');
        } else {
            $(this).val('Add Image');
        }
    });
    $('body').on('click', '.upload_image_button', function (e) {
        e.preventDefault();
        var button = $(this);
        if (button.val() === 'Remove Image') {
            // Remove the image URL from the hidden input field
            button.next().val('');
            // Remove the image source from the thumbnail display
            button.nextAll('.product_image_display').first().attr('src', '');
            // Change the button text back to "Add Image"
            button.val('Add Image');
        } else {
            var file_frame;
            // If the media frame already exists, reopen it.
            if (file_frame) {
                file_frame.open();
                return;
            }
            // Create a new media frame
            file_frame = wp.media({
                title: 'Choose Image',
                button: { text: 'Choose Image' },
                multiple: false    // Set to true to allow multiple files to be selected
            });
            // When an image is selected in the media frame...
            file_frame.on('select', function () {
                // Get media attachment details from the frame state
                var attachment = file_frame.state().get('selection').first().toJSON();
                // Send the attachment URL to our custom image input field.
                var imageUrl = attachment.url;
                button.next().val(imageUrl);
                // Update the image source of the thumbnail display
                button.nextAll('.product_image_display').first().attr('src', imageUrl);
                // Change the button text to "Remove Image"
                button.val('Remove Image');
            });
            // Finally, open the modal on click
            file_frame.open();
        }
    });
});
