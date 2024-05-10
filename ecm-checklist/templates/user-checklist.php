<?php
/**
 * Template for user generated checklists
 */

function checklist_frontend_form_shortcode() {
    ob_start(); // Start output buffering to capture the form HTML
    ?>
    <form id="frontend-checklist-form" action="" method="post" enctype="multipart/form-data">
        <div>
            <label for="checklist-name">Checklist Name:</label>
            <input type="text" id="checklist-name" name="checklist_name" required>
        </div>
        <div>
            <label for="checklist-privacy">Privacy:</label>
            <select id="checklist-privacy" name="checklist_privacy">
                <option value="private">Private</option>
                <option value="public">Public</option>
            </select>
        </div>
        <!-- Placeholder for dynamic product rows; JavaScript will handle these -->
        <div id="product-list-wrapper"></div>
        <button type="button" id="add-product-row">Add Product</button>
        <input type="submit" value="Create Checklist">
    </form>
    <?php
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('checklist_frontend_form', 'checklist_frontend_form_shortcode');