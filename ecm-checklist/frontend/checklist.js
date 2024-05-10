jQuery(document).ready(function($) {
    console.log($('.upload_image_button').length);

    // Disable radio buttons for non-logged-in users
    if (!checklist_vars.user_is_logged_in) {
        $('.product-radio-have, .product-radio-need, .product-radio-ordered, .product-radio-interest').prop('disabled', true);
    }

    // Attach a change event listener to the radio buttons
    $('.product-radio-have, .product-radio-need, .product-radio-ordered, .product-radio-interest').change(function() {
        var postId = $(this).data('post-id');
        var productName = $(this).data('product-name');
        var stateName = $(this).val(); // We can now get the value directly from the radio button

        // Send an AJAX request to the server with the radio button state, post ID, product name, and state name
        $.post({
            url: checklist_vars.ajax_url,
            data: {
                action: 'update_checklist',
                post_id: postId,
                product_name: productName,
                state_name: stateName
            }
        });
    });

    // Filter products based on radio selection
    $('input[type=radio][name=filter]').change(function() {
    var value = $(this).val();
    if (value === 'all') {
        $('.product').show();
        $('article').show(); // Show articles when 'All' is selected
    } else if (value === 'unchecked') {
        $('.product').hide(); // Hide all products initially
        $('.product').each(function() {
            // If a product does not have any checked radio button, show it
            if (!$(this).find('input[type=radio]:checked').length) {
                $(this).show();
            }
        });
        $('article').hide(); // Hide articles when 'Unchecked' is selected
    } else {
        $('.product').hide();
        $('.product.' + value).show();
        $('article').hide(); // Hide articles when other filters are selected
    }
});
	
	$('#add-checklist-item').click(function() {
        var newItemIndex = $('#dynamic-checklist-container .checklist-item').length;
        var newField = `<div class="checklist-item">
            <div class="_preview"><div><label>Number:</label>
            <input type="number" name="product_number[]" placeholder="Number" required></div>
            <div><label>Name:</label>
            <input type="text" name="product_name[]" placeholder="Product Name" required></div></div>
            <div><label>Short Description:</label>
            <textarea type="text" name="product_description[]" placeholder="Description"></textarea></div>
            <div><label>Image:</label>
            <input type="button" class="upload_image_button" value="Upload Image">
            <input type="hidden" name="product_image[]" class="product_image">
            <div class="image_preview"></div></div>
            <button type="button" class="remove-item button">Remove Item</button>
        </div>`;
        $('#dynamic-checklist-container').append(newField);
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('.checklist-item').remove();
    });

    // Handle image upload using WordPress Media Uploader
    $(document).on('click', '.upload_image_button', function(e) {
        e.preventDefault();

        var button = $(this);
        var fileFrame = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            library: { type: 'image' },
            button: { text: 'Use this Image' },
            multiple: false
        });

        fileFrame.on('select', function() {
            var attachment = fileFrame.state().get('selection').first().toJSON();
            
			button.closest('div').find('.product_image').val(attachment.url);
            button.siblings('.image_preview').html('<img src="' + attachment.url + '" style="max-width: 100px;">');
        });

        fileFrame.open();
    });
});