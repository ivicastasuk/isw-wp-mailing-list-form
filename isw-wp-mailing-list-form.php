<?php
/**
 * Plugin Name: ISW WP Mailing List Form
 * Description: The ISW WP Mailing List Form plugin seamlessly integrates a subscription form into your WordPress site, allowing visitors to enter their email addresses to subscribe to your newsletter.
 * Version: 0.1
 * Author: Ivica Stasuk
 * Author URI: https://www.stasuk.in.rs
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: isw-wp-ml-form
 */

 function add_isw_mailinglist_form(){
    $isw_ml_form = '<div class="isw-ml-form-container">
                        <form action="" method="post">
                            <input type="text" name="isw_ml_name" placeholder="Your name..." required>
                            <input type="email" name="isw_ml_email" required>
                            <input type="submit" name="isw_ml_submit" value="Subscribe to our mailing list">
                        </form>
                    </div>';
    return $isw_ml_form;
 }

 add_shortcode('add_isw_ml_form', 'add_isw_mailinglist_form');

 function isw_mailing_list_form_styles(){
    $css_url = plugins_url('isw-wp-mailing-list-form.css', __FILE__);

    wp_register_style('isw-wp-mailing-list-form');
 }