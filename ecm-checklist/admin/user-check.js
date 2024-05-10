// JavaScript Document
jQuery(document).ready(function($) {
    $('#add-product-row').on('click', function() {
        var newRow = `<div class="product-row">
            <input type="text" name="product_name[]" placeholder="Product Name" required>
            <!-- Add more fields as needed -->
            <button class="remove-product-row">Remove</button>
        </div>`;

        $('#product-list-wrapper').append(newRow);
    });

    $(document).on('click', '.remove-product-row', function() {
        $(this).closest('.product-row').remove();
    });
});