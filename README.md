# Dynamic SEO Tags Plugin

### Authors: 
- [Hans Steffens](https://hanscode.io/)
- The folks behind [Marketing Done Right, LLC](https://marketingdr.co/)

## Description
Dynamic SEO Tags is a WordPress plugin designed to dynamically set SEO meta tags based on URL parameters. This plugin is compatible with the popular Yoast SEO Plugin and provides a robust fallback mechanisms in case Yoast SEO is not installed, ensuring that SEO is effectively managed with or without Yoast SEO.

The plugin allows users to specify custom placeholders within the SEO meta tags that can be replaced dynamically based on the value of URL parameters, enhancing the relevance and specificity of SEO on a per-page basis.

## Key Features
- **Dynamic SEO Tag Management**: Dynamically adjust SEO meta tags (Title, Description, and Canonical URL) based on custom URL parameters.
- **Fallback for SEO Handling**: Implements fallback SEO handling when Yoast SEO is not active.
- **Support for Social Media Tags**: Dynamically adjusts OpenGraph and Twitter meta tags when Yoast SEO is active.
- **Security**: Includes rigorous security practices to prevent common vulnerabilities such as XSS and CSRF.
- **Shortcode Support**: Provides a shortcode `[url_param]` that outputs URL parameter values anywhere on the site.

## Security Practices
This plugin adheres to best security practices, including:
- **Nonces**: Utilizes nonces for form submissions to protect against CSRF attacks.
- **Permissions Checks**: Ensures that only users with appropriate permissions (manage_options) can modify settings.
- **Data Sanitization and Escaping**: Implements comprehensive sanitization and escaping strategies to ensure that all inputs are safe and outputs are securely rendered.

## Installation
1. Download the `dynamic-seo-tags` plugin folder.
2. Upload it to your WordPress site's `/wp-content/plugins/`directory.
3. Log in to your WordPress dashboard and navigate to the 'Plugins' menu.
4. Locate 'Dynamic SEO Tags' in the plugin list and click the 'Activate' link.
5. Once activated, go to `Settings > Dynamic SEO Tags` in your WordPress admin panel to configure the plugin.

### Configuring the Plugin
After activation, it's important to configure the plugin to ensure it operates correctly with your site's SEO strategy:

- **Variable Name**: Navigate to the plugin's settings page and enter the URL parameter name that you want the plugin to dynamically replace in your site's SEO meta tags.
- **SEO Meta Description and Title:** Set up templates for your SEO meta description and title. Use the specified placeholder (e.g., `[variable]`) to mark where the dynamic content should be inserted.

By configuring these settings, you tailor the Dynamic SEO Tags plugin to your site's specific needs, enhancing your SEO dynamically based on URL parameters.

## Yoast SEO Compatibility
Dynamic SEO Tags is designed to work seamlessly with Yoast SEO. When Yoast SEO is installed and active, the plugin leverages Yoast's robust SEO capabilities to enhance:

- **Dynamic SEO Meta Tags**: Utilizes Yoast SEO's filters to dynamically insert SEO meta tags based on URL parameters.
- **OpenGraph Tags**: Dynamically manages OpenGraph metadata for better social media integration.
- **Twitter Cards**: Adjusts Twitter card metadata dynamically, ensuring that social media representations of your pages are always up to date.

### Enhanced Functionality with Yoast SEO
If Yoast SEO is active, Dynamic SEO Tags will use Yoast's advanced filters to manipulate SEO tags directly, providing a more integrated and powerful SEO management experience. If Yoast SEO is not active, Dynamic SEO Tags will still operate effectively by providing fallback mechanisms for essential SEO tags like the title and meta description.

### How It Works with Yoast SEO
When Yoast SEO is detected:
- The plugin adds custom dynamic data to the SEO titles, descriptions, canonical URLs, OpenGraph titles, descriptions, URLs, and Twitter metadata.
- This data is injected dynamically based on the URL parameters specified by the user in the plugin's settings.

If Yoast SEO is not detected:
- The plugin falls back to basic WordPress functions to manage the SEO tags, ensuring your site remains optimized even without Yoast SEO.

### Installing Yoast SEO
For optimal functionality, it is recommended to install Yoast SEO. You can download and install Yoast SEO directly from the WordPress plugin repository or by visiting [Yoast SEO Plugin Page](https://yoast.com/wordpress/plugins/seo/).

### Note on Yoast SEO Integration
While Dynamic SEO Tags enhances and relies on Yoast SEO for advanced features, it maintains core functionality independently, ensuring that your site's SEO is not compromised even in the absence of Yoast SEO.

## Usage
### Configuring the Plugin
Navigate to "Settings > Dynamic SEO Tags" in your WordPress admin panel where you can set:
- **Variable Name**: Specify the URL parameter name that the plugin should listen for.
- **SEO Meta Description**: Set a template for the SEO meta description. Use placeholders like `[variable]` which will be replaced dynamically.
- **SEO Meta Title**: Similarly, set a template for the SEO meta title with placeholders.

### Using the Shortcode
You can use the `[url_param]` shortcode to display the value of a URL parameter anywhere on your site. The shortcode supports two attributes:
- `variable`: The name of the URL parameter to display.
- `default`: A default value to display if the URL parameter is not present.

**Example**:

```plaintext
[url_param variable="city" default="New York"]
```

This will display the value of the city parameter or "New York" if city is not specified.

## URL Parameter Considerations

When using URL parameters, replace spaces with dashes in the URL. For example, use `?city=new-york` instead of `?city=new york`. The plugin will correctly format it for display and SEO tags.

## Contributing
Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated. For major changes, please open an issue first to discuss what you would like to change.

## License
Distributed under the [GPL](https://www.gnu.org/licenses/gpl-3.0.html) License. See LICENSE for more information.



