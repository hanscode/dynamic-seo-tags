<?php

/**
 * Plugin Name: Dynamic SEO Tags
 * Description: Dynamically set SEO meta tags based on URL parameters, with fallbacks if Yoast SEO is not installed.
 * Version: 1.0
 * Author: Hans Steffens & Marketing Done Right LLC
 * Author URI:  https://marketingdr.co
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Add the action to set the SEO tags
function dynamic_seo_tags_menu()
{
    add_options_page(
        'Dynamic SEO Tags', // Page title
        'Dynamic SEO Tags', // Menu title
        'manage_options', // Capability
        'dynamic-seo-tags', // Menu slug
        'dynamic_seo_tags_page' // Function that displays the options page
    );
}
add_action('admin_menu', 'dynamic_seo_tags_menu');

add_action('admin_init', 'dynamic_seo_tags_settings');

function dynamic_seo_tags_settings()
{
    register_setting('dynamic-seo-tags-options', 'seo_variable_name');
    register_setting('dynamic-seo-tags-options', 'seo_custom_text');
    register_setting('dynamic-seo-tags-options', 'seo_custom_title');
}

function dynamic_seo_tags_page()
{
?>
    <div class="wrap">
        <h2>Dynamic SEO Tags</h2>
        <form method="post" action="options.php">
            <?php settings_fields('dynamic-seo-tags-options'); ?>
            <?php do_settings_sections('dynamic-seo-tags-options'); ?>
            <?php wp_nonce_field('update_seo_settings', 'seo_settings_nonce'); ?>  <!-- Nonce field added here -->
            <p>Use the following placeholders in the SEO Meta Description and SEO Meta Title fields to dynamically replace the content:</p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Variable Name</th>
                    <td><input style="width:50%;" type="text" name="seo_variable_name" value="<?php echo get_option('seo_variable_name'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">SEO Meta Description</th>
                    <td>
                        <textarea name="seo_custom_text" rows="5" cols="50"><?php echo esc_textarea(get_option('seo_custom_text')); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">SEO Meta Title</th>
                    <td><input style="width:50%;" type="text" name="seo_custom_title" value="<?php echo esc_attr(get_option('seo_custom_title')); ?>" /></td>
                </tr>

            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// When processing the form submission, verify the nonce to make sure it’s a valid request:
add_action('admin_init', 'dynamic_seo_tags_save_settings');

function dynamic_seo_tags_save_settings() {
    // Check if the user has the necessary permissions
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_POST['seo_variable_name']) && check_admin_referer('update_seo_settings', 'seo_settings_nonce')) {
        // Sanitize and save the settings if the nonce is valid
        update_option('seo_variable_name', sanitize_text_field($_POST['seo_variable_name']));
    }
}

// Initialize SEO tags with fallback
function initialize_seo_tags()
{
    if (defined('WPSEO_VERSION')) {
        // Yoast SEO is active, use Yoast's filters
        add_filter('wpseo_metadesc', 'dynamic_seo_meta_description', 10, 1);
        add_filter('wpseo_title', 'dynamic_seo_title', 10, 1);
        add_filter('wpseo_canonical', 'dynamic_seo_canonical', 10, 1);
        add_filter('wpseo_opengraph_title', 'dynamic_seo_og_title', 10, 1);
        add_filter('wpseo_opengraph_desc', 'dynamic_seo_og_description', 10, 1);
        add_filter('wpseo_opengraph_url', 'dynamic_seo_og_url', 10, 1);
        add_filter('wpseo_twitter_title', 'dynamic_seo_twitter_title', 10, 1);
        add_filter('wpseo_twitter_description', 'dynamic_seo_twitter_description', 10, 1);
    } else {
        // Yoast SEO is not active, use fallback mechanisms
        remove_action('wp_head', 'rel_canonical');
        add_action('wp_head', 'fallback_seo_canonical');
        add_filter('pre_get_document_title', 'dynamic_seo_title');
        add_action('wp_head', 'fallback_seo_meta_description');
    }
}
add_action('init', 'initialize_seo_tags');

// Fallback function for meta description
function dynamic_seo_meta_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}


function dynamic_seo_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Fallback function for canonical URL
function dynamic_seo_canonical($canonical)
{
    return dynamic_seo_tags_replace('canonical', $canonical, true);
}

// Fallback function for OpenGraph title
function dynamic_seo_og_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Fallback function for OpenGraph description
function dynamic_seo_og_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}

// Fallback function for OpenGraph URL
function dynamic_seo_og_url($url)
{
    return dynamic_seo_tags_replace('url', $url, true);
}

// Fallback function for Twitter title
function dynamic_seo_twitter_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Fallback function for Twitter description
function dynamic_seo_twitter_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}

// Fallback SEO Meta Description
function fallback_seo_meta_description()
{
    $description = dynamic_seo_tags_replace('description', get_bloginfo('description'));
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}

// Fallback SEO Canonical URL
function fallback_seo_canonical()
{
    global $wp;

    // Start with the full current URL
    $current_url = home_url(add_query_arg(array(), $wp->request));

    // Retrieve the SEO variable name from the options
    $seo_variable_name = get_option('seo_variable_name');
    if (!empty($seo_variable_name) && isset($_GET[$seo_variable_name])) {
        // Append or modify the query parameter based on the current URL and the variable
        $canonical_url = add_query_arg($seo_variable_name, $_GET[$seo_variable_name], $current_url);
    } else {
        // No specific variable is set, use the current URL
        $canonical_url = $current_url;
    }

    // Output the canonical URL
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
}

// Common function to replace tags
function dynamic_seo_tags_replace($type, $content, $is_url = false)
{
    $variable_name = get_option('seo_variable_name');
    if (isset($_GET[$variable_name])) {
        $variable_value_raw = sanitize_text_field($_GET[$variable_name]);  // Sanitize the raw input immediately
        // Replace dashes with spaces and capitalize each word
        $variable_value = ucwords(str_replace('-', ' ', $variable_value_raw));

        // Decide which custom text to use based on the type
        $custom_text = ($type == 'title') ? get_option('seo_custom_title') : get_option('seo_custom_text');

        $escaped_variable_value = esc_html($variable_value);  // Escape the variable value
        $replaced_content = str_replace("[$variable_name]", $escaped_variable_value, $custom_text);  // Replace the placeholder with the escaped variable value

        if (!$is_url) {
            // For description and title, ensure HTML safety
            $content = esc_html($replaced_content);
        } else {
            // For canonical URL, safely append or modify the query parameter
            $content = add_query_arg($variable_name, $variable_value_raw, $content);  // Ensure safe URL manipulation
        }
    }
    return $content;
}

function handle_url_param_shortcode($atts)
{
    // Define default attributes and merge with user attributes
    $attributes = shortcode_atts(array(
        'variable' => '', // Variable name to look for in the URL
        'default' => '', // Default value if the variable isn't set, empty by default
    ), $atts);

    $variable_name = sanitize_text_field($attributes['variable']);
    // Check if the variable is set in the URL, otherwise use the default value provided by the user
    if (isset($_GET[$variable_name]) && !empty($_GET[$variable_name])) {
        $variable_value_raw = sanitize_text_field($_GET[$variable_name]);
    } else {
        $variable_value_raw = sanitize_text_field($attributes['default']); // Use user-defined default
    }

    // Format the output: replace dashes with spaces and capitalize each word
    return esc_html(ucwords(str_replace('-', ' ', $variable_value_raw)), ENT_QUOTES, 'UTF-8');
}

add_shortcode('url_param', 'handle_url_param_shortcode');
