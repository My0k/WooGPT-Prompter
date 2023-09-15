<?php
/*
Plugin Name: GPTdescription
Description: Create prompts for product descriptions using GPT for WooCommerce products. Navigate to the GPTdescription menu in the admin dashboard to configure settings.
Version: 1.0
Author: Myok
*/

// Add custom fields on the edit product page
function gpt_add_custom_fields() {
    global $post;

    $product = wc_get_product($post->ID);
    $title = $product->get_name();
    $description = $product->get_description();
    $store_desc = get_option('gpt_store_description', '');
    $word_count = get_option('gpt_max_words', '100');
    $language = get_option('gpt_language', 'English');
    $format = get_option('gpt_format', 'HTML format');

    // Determine the format text based on user selection
    $format_text = $format === "HTML format" ? "WordPress WooCommerce product description using HTML for headers, bold text, and other formatting" : "product ecommerce description";

    $prompt = "Create a {$format_text}. Max {$word_count} words about a product named '{$title}' considering {$description} for a store about {$store_desc}. Give me the content in {$language}.";

    // Display the prompt in a textarea with copy and load data buttons
    echo '<div class="options_group">';
    echo '<p class="form-field">';
    echo '<label for="gpt_prompt">GPT Prompt</label>';
    echo '<textarea id="gpt_prompt" style="width:100%;">' . esc_textarea($prompt) . '</textarea>';
    echo '<button type="button" onclick="loadPromptData()">Load Data</button>';
    echo '<button type="button" onclick="copyToClipboard()">Copy Prompt</button>';
    echo '</p>';
    echo '</div>';

    // Javascript functions for copying to clipboard and loading the product data
    ?>
    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("gpt_prompt");
            copyText.select();
            document.execCommand("copy");
            alert("Prompt copied to clipboard! Now copy this prompt on ChatGPT!");
        }

        function loadPromptData() {
            let title = document.getElementById("title").value;
            let description = (document.getElementById("content") && document.getElementById("content").value) || (tinyMCE.get("content") && tinyMCE.get("content").getContent());
            let store_desc = "<?php echo esc_js($store_desc); ?>";
            let word_count = "<?php echo esc_js($word_count); ?>";
            let language = "<?php echo esc_js($language); ?>";
            let format_text = "<?php echo $format === 'HTML format' ? 'WordPress WooCommerce product description using HTML for headers, bold text, and other formatting' : 'product ecommerce description'; ?>";

            let prompt = "Create a " + format_text + ". Max " + word_count + " words about a product named '" + title + "' considering " + description + " for a store about " + store_desc + ". Give me the content in " + language + ".";
            document.getElementById("gpt_prompt").value = prompt;
        }
    </script>
    <?php
}
add_action('woocommerce_product_options_general_product_data', 'gpt_add_custom_fields');

// Create admin menu page for GPTdescription settings
function gpt_create_menu() {
    add_menu_page('GPTdescription Settings', 'GPTdescription', 'manage_options', 'gptdescription', 'gpt_settings_page');
}
add_action('admin_menu', 'gpt_create_menu');

// Display the settings page content
function gpt_settings_page() {
    ?>
    <div class="wrap">
        <h1>GPTdescription Settings</h1>
        <p>This tool helps create prompts easily for ChatGPT. Use the store description field to give context to your website so the generated description aligns with the theme of your store. Ensure you've detailed the product title and description in the WooCommerce product editor for a richer prompt. Have fun!</p>
        <form method="post" action="options.php">
            <?php settings_fields('gpt-settings-group'); ?>
            <?php do_settings_sections('gpt-settings-group'); ?>
            <table class="form-table">
                <!-- Store Description -->
                <tr valign="top">
                <th scope="row">Store Description <br><small>Describe your store in a few words. This helps in giving context for the generated prompt.</small></th>
                <td><input type="text" name="gpt_store_description" value="<?php echo esc_attr(get_option('gpt_store_description')); ?>" /></td>
                </tr>
                <!-- Max Words -->
                <tr valign="top">
                <th scope="row">Max Words <br><small>Set the maximum word count for the generated description.</small></th>
                <td><input type="number" name="gpt_max_words" value="<?php echo esc_attr(get_option('gpt_max_words', '100')); ?>" /></td>
                </tr>
                <!-- Language Selection -->
                <tr valign="top">
                <th scope="row">Language <br><small>Select the desired language for the generated content.</small></th>
                <td>
                    <select name="gpt_language">
                        <option value="English" <?php selected(get_option('gpt_language'), 'English'); ?>>English</option>
                        <option value="Spanish" <?php selected(get_option('gpt_language'), 'Spanish'); ?>>Spanish</option>
                        <option value="Português Brasil" <?php selected(get_option('gpt_language'), 'Português Brasil'); ?>>Português Brasil</option>
                        <option value="Italian" <?php selected(get_option('gpt_language'), 'Italian'); ?>>Italian</option>
                        <option value="German" <?php selected(get_option('gpt_language'), 'German'); ?>>German</option>
                        <option value="French" <?php selected(get_option('gpt_language'), 'French'); ?>>French</option>
                        <!-- Add other languages as needed -->
                    </select>
                </td>
                </tr>
                <!-- Format Selection -->
                <tr valign="top">
                <th scope="row">Format <br><small>Choose between a structured HTML format or a plain ecommerce product description.</small></th>
                <td>
                    <select name="gpt_format">
                        <option value="HTML format" <?php selected(get_option('gpt_format'), 'HTML format'); ?>>HTML format</option>
                        <option value="Plain text" <?php selected(get_option('gpt_format'), 'Plain text'); ?>>Plain text</option>
                    </select>
                </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings for GPTdescription
function gpt_register_settings() {
    register_setting('gpt-settings-group', 'gpt_store_description');
    register_setting('gpt-settings-group', 'gpt_max_words');
    register_setting('gpt-settings-group', 'gpt_language');
    register_setting('gpt-settings-group', 'gpt_format');
}
add_action('admin_init', 'gpt_register_settings');
?>
