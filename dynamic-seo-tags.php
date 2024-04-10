<?php

/**
 * Plugin Name: Dynamic SEO Tags
 * Description: Dynamically set SEO meta tags based on URL parameters.
 * Version: 1.0
 * Author: Hans Steffens
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
                    <td><input type="text" name="seo_variable_name" value="<?php echo get_option('seo_variable_name'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">SEO Meta Description</th>
                    <td><input type="text" name="seo_custom_text" value="<?php echo esc_attr(get_option('seo_custom_text')); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">SEO Meta Title</th>
                    <td><input type="text" name="seo_custom_title" value="<?php echo esc_attr(get_option('seo_custom_title')); ?>" /></td>
                </tr>

            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Filter for Yoast SEO meta description
add_filter('wpseo_metadesc', 'dynamic_seo_meta_description', 10, 1);
function dynamic_seo_meta_description($description) {
    return dynamic_seo_tags_replace('description', $description);
}

// Filter for Yoast SEO title
add_filter('wpseo_title', 'dynamic_seo_title', 10, 1);
function dynamic_seo_title($title) {
    return dynamic_seo_tags_replace('title', $title);
}

// Filter for Yoast SEO canonical
add_filter('wpseo_canonical', 'dynamic_seo_canonical', 10, 1);
function dynamic_seo_canonical($canonical) {
    return dynamic_seo_tags_replace('canonical', $canonical, true);
}

// Common function to replace tags
function dynamic_seo_tags_replace($type, $content, $is_url = false) {
    $variable_name = get_option('seo_variable_name');
    if (isset($_GET[$variable_name])) {
        $variable_value = htmlspecialchars($_GET[$variable_name], ENT_QUOTES, 'UTF-8');
        
        // Decide which custom text to use based on the type
        $custom_text = ($type == 'title') ? get_option('seo_custom_title') : get_option('seo_custom_text');
        
        if(!$is_url) {
            // For description and title
            $content = str_replace("[$variable_name]", $variable_value, $custom_text);
        } else {
            // For canonical URL, append or modify the query parameter
            $content = add_query_arg($variable_name, $variable_value, $content);
        }
    }
    return $content;
}
