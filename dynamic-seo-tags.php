<?php

/**
 * Plugin Name: Dynamic SEO Tags
 * Description: Dynamically set SEO meta tags based on URL parameters.
 * Version: 1.0
 * Author: Hans Steffens & Marketing Done Right LLC
 * Author URI:  https://marketingdr.co
 */

// Add the action to set the SEO tags
add_action('admin_menu', 'dynamic_seo_tags_menu');

function dynamic_seo_tags_menu()
{
    add_menu_page('Dynamic SEO Tags', 'SEO Tags', 'manage_options', 'dynamic-seo-tags', 'dynamic_seo_tags_page', null, 99);
}

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

// Filter for Yoast SEO meta description
add_filter('wpseo_metadesc', 'dynamic_seo_meta_description', 10, 1);
function dynamic_seo_meta_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}

// Filter for Yoast SEO title
add_filter('wpseo_title', 'dynamic_seo_title', 10, 1);
function dynamic_seo_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Filter for Yoast SEO canonical
add_filter('wpseo_canonical', 'dynamic_seo_canonical', 10, 1);
function dynamic_seo_canonical($canonical)
{
    return dynamic_seo_tags_replace('canonical', $canonical, true);
}

// Filter for Yoast SEO OpenGraph title
add_filter('wpseo_opengraph_title', 'dynamic_seo_og_title', 10, 1);
function dynamic_seo_og_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Filter for Yoast SEO OpenGraph description
add_filter('wpseo_opengraph_desc', 'dynamic_seo_og_description', 10, 1);
function dynamic_seo_og_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}

// Filter for Yoast SEO OpenGraph URL
add_filter('wpseo_opengraph_url', 'dynamic_seo_og_url', 10, 1);
function dynamic_seo_og_url($url)
{
    return dynamic_seo_tags_replace('url', $url, true);
}

// Filter for Yoast SEO Twitter title
add_filter('wpseo_twitter_title', 'dynamic_seo_twitter_title', 10, 1);
function dynamic_seo_twitter_title($title)
{
    return dynamic_seo_tags_replace('title', $title);
}

// Filter for Yoast SEO Twitter description
add_filter('wpseo_twitter_description', 'dynamic_seo_twitter_description', 10, 1);
function dynamic_seo_twitter_description($description)
{
    return dynamic_seo_tags_replace('description', $description);
}

// Common function to replace tags
function dynamic_seo_tags_replace($type, $content, $is_url = false)
{
    $variable_name = get_option('seo_variable_name');
    if (isset($_GET[$variable_name])) {
        $variable_value_raw = $_GET[$variable_name];
        // Replace dashes with spaces and capitalize each word
        $variable_value = htmlspecialchars(ucwords(str_replace('-', ' ', $variable_value_raw)), ENT_QUOTES, 'UTF-8');

        // Decide which custom text to use based on the type
        $custom_text = ($type == 'title') ? get_option('seo_custom_title') : get_option('seo_custom_text');

        if (!$is_url) {
            // For description and title
            $content = str_replace("[$variable_name]", $variable_value, $custom_text);
        } else {
            // For canonical URL, append or modify the query parameter
            $content = add_query_arg($variable_name, $variable_value_raw, $content); // Use raw value for URLs
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
        $variable_value_raw = $_GET[$variable_name];
    } else {
        $variable_value_raw = sanitize_text_field($attributes['default']); // Use user-defined default
    }

    // Format the output: replace dashes with spaces and capitalize each word
    return htmlspecialchars(ucwords(str_replace('-', ' ', $variable_value_raw)), ENT_QUOTES, 'UTF-8');
}

add_shortcode('url_param', 'handle_url_param_shortcode');
