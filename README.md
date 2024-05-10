# ECM Checklist Plugin for WordPress

The WP Checklist Plugin developed by Cristian Ibanez for East Coast Marketing enables WordPress users to create and manage custom checklists with a dedicated post type. It features comprehensive functionality for handling items in the checklist including creating, updating, and managing products within a checklist.

## Features

### Custom Post Type
- Automatically creates a custom post type `Checklist` upon activation to organize items.
- Provides templates for single and archive pages of checklists.

### Metabox Integration
- Includes metabox functionality for adding detailed information to checklist items.

### AJAX Functionality
- Utilizes AJAX for updating checklists without reloading the page, enhancing the user experience.

### Frontend and Admin Scripts
- Enqueues scripts and styles specific to both the admin panel and frontend to manage checklist interactions smoothly.

### Shortcode Support
- Implements shortcodes for displaying checklists, allowing easy integration into posts or pages.

### Custom Templates
- Custom templates for displaying and managing checklists are provided and used based on the context.

### AJAX Endpoint
- Handles AJAX requests for checklist updates with robust user permission checks.

### Sortable Items
- Includes functionality to change the order of checklist items on the frontend with drag-and-drop ease.

### Settings Page
- A dedicated settings page in the WordPress admin for managing checklist settings.

### Security
- Ensures secure operations with nonce verification and permissions checks for AJAX and other operations.

## Installation

1. Download the plugin from the repository.
2. Upload it to your WordPress website via the WordPress admin panel.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

- Navigate to the "Checklists" section in the WordPress admin to start adding and managing your checklists.
- Use the provided shortcode `[display_checklist]` to display checklists on any post or page.
- Customize the checklist display and functionality via the plugin settings page.

## Dependencies

This plugin requires jQuery for some AJAX and sorting functionalities. Make sure your theme or another plugin does not dequeue the WordPress default jQuery.

## Hooks and Actions

- **Activation Hook**: Initializes the custom post type and flushes rewrite rules to ensure the custom post type URLs are accessible.
- **Deactivation Hook**: Cleans up the database by removing scheduled events and optionally deleting plugin data.

## Shortcodes

- `[display_checklist]`: Displays the checklists using the specified attributes to customize the output.

## License

This plugin is licensed under GPL-3.0.

## Author

Developed by Cristian Ibanez for East Coast Marketers For more information, visit [East Coast Marketers](https://eastcoastmarketers.ca/).


