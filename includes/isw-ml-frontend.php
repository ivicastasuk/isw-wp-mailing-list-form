<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function add_isw_mailinglist_form() {
    $ml_message = '';
    $ml_message .= '<div class="isw-ml-form-container">';

    $status = isset( $_GET['ml_status'] ) ? sanitize_key( wp_unslash( $_GET['ml_status'] ) ) : '';
    if ( $status ) {
        $status_message = isw_ml_get_status_message( $status );
        $ml_message .= '<div class="' . esc_attr( $status_message['class'] ) . '">' . esc_html( $status_message['text'] ) . '</div>';
    }

    $input_text_color = get_option( 'input_text_color', '#001f53' );
    $input_border_color = get_option( 'input_border_color', '#808080' );
    $button_bg_color = get_option( 'button_bg_color', '#001f53' );
    $button_text_color = get_option( 'button_text_color', '#ffffff' );
    $button_hover_bg_color = get_option( 'button_hover_bg_color', $button_bg_color );
    $button_hover_text_color = get_option( 'button_hover_text_color', $button_text_color );
    $button_text = get_option( 'button_text', __( 'Subscribe to our mailing list', 'isw-wp-mailing-list-form' ) );
    $name_placeholder = get_option( 'input_name_placeholder', __( 'Your name...', 'isw-wp-mailing-list-form' ) );
    $email_placeholder = get_option( 'input_email_placeholder', __( 'Your E-Mail address...', 'isw-wp-mailing-list-form' ) );
    $button_font_family = get_option( 'button_font_family', 'inherit' );
    $button_font_size = get_option( 'button_font_size', '16' );
    $button_font_style = get_option( 'button_font_style', 'normal' );
    $button_font_weight = get_option( 'button_font_weight', 'normal' );
    $button_line_height = get_option( 'button_line_height', '1.2' );
    $button_border_width = get_option( 'button_border_width', '1' );
    $button_border_color = get_option( 'button_border_color', '#001f53' );
    $button_hover_border_color = get_option( 'button_hover_border_color', $button_border_color );
    $button_border_style = get_option( 'button_border_style', 'solid' );
    $button_box_shadow = get_option( 'button_box_shadow', '0 2px 6px rgba(0,0,0,0.15)' );

    if ( '' === trim( (string) $button_hover_bg_color ) ) {
        $button_hover_bg_color = $button_bg_color;
    }

    if ( '' === trim( (string) $button_hover_text_color ) ) {
        $button_hover_text_color = $button_text_color;
    }

    if ( '' === trim( (string) $button_hover_border_color ) ) {
        $button_hover_border_color = $button_border_color;
    }

    $button_width_type = get_option( 'button_width_type', 'full' );
    $button_width_custom = get_option( 'button_width_custom', '' );
    $button_align = get_option( 'button_align', 'center' );

    switch ( $button_width_type ) {
        case 'full':
            $btn_width = '100%';
            break;
        case '1/2':
            $btn_width = '50%';
            break;
        case '1/3':
            $btn_width = '33.3333%';
            break;
        case '1/4':
            $btn_width = '25%';
            break;
        case 'custom':
            $btn_width = '' !== $button_width_custom ? $button_width_custom : 'auto';
            break;
        default:
            $btn_width = '100%';
    }

    switch ( $button_align ) {
        case 'left':
            $btn_align_css = 'margin-left:0;margin-right:auto;';
            break;
        case 'center':
            $btn_align_css = 'margin-left:auto;margin-right:auto;display:block;';
            break;
        case 'right':
            $btn_align_css = 'margin-left:auto;margin-right:0;';
            break;
        default:
            $btn_align_css = '';
    }

    $input_width_type = get_option( 'input_width_type', 'full' );
    $input_width_custom = get_option( 'input_width_custom', '' );
    $input_align = get_option( 'input_align', 'center' );

    switch ( $input_width_type ) {
        case 'full':
            $inp_width = '100%';
            break;
        case '1/2':
            $inp_width = '50%';
            break;
        case '1/3':
            $inp_width = '33.3333%';
            break;
        case '1/4':
            $inp_width = '25%';
            break;
        case 'custom':
            $inp_width = '' !== $input_width_custom ? $input_width_custom : 'auto';
            break;
        default:
            $inp_width = '100%';
    }

    switch ( $input_align ) {
        case 'left':
            $inp_align_css = 'margin-left:0;margin-right:auto;';
            break;
        case 'center':
            $inp_align_css = 'margin-left:auto;margin-right:auto;display:block;';
            break;
        case 'right':
            $inp_align_css = 'margin-left:auto;margin-right:0;';
            break;
        default:
            $inp_align_css = '';
    }

    $input_padding_top = get_option( 'input_padding_top', 16 );
    $input_padding_right = get_option( 'input_padding_right', 16 );
    $input_padding_bottom = get_option( 'input_padding_bottom', 16 );
    $input_padding_left = get_option( 'input_padding_left', 16 );
    $input_padding_same_all = get_option( 'input_padding_same_all', 1 );
    $input_padding = $input_padding_same_all ? "{$input_padding_top}px" : "{$input_padding_top}px {$input_padding_right}px {$input_padding_bottom}px {$input_padding_left}px";

    $button_padding_top = get_option( 'button_padding_top', 16 );
    $button_padding_right = get_option( 'button_padding_right', 16 );
    $button_padding_bottom = get_option( 'button_padding_bottom', 16 );
    $button_padding_left = get_option( 'button_padding_left', 16 );
    $button_padding_same_all = get_option( 'button_padding_same_all', 1 );
    $button_padding = $button_padding_same_all ? "{$button_padding_top}px" : "{$button_padding_top}px {$button_padding_right}px {$button_padding_bottom}px {$button_padding_left}px";

    $input_border_radius = get_option( 'input_border_radius', 16 );
    $button_border_radius = get_option( 'button_border_radius', 16 );
    $form_id = wp_unique_id( 'isw-ml-form-' );

    $isw_ml_form = $ml_message . '<form id="' . esc_attr( $form_id ) . '" class="isw-ml-form" action="" method="post">';
    $isw_ml_form .= '<style>#' . esc_attr( $form_id ) . ' input[type="submit"]{background-color:var(--isw-ml-btn-bg);color:var(--isw-ml-btn-text);border-color:var(--isw-ml-btn-border);transition:background-color .2s ease,color .2s ease,border-color .2s ease;}#' . esc_attr( $form_id ) . ' input[type="submit"]:hover,#' . esc_attr( $form_id ) . ' input[type="submit"]:focus-visible{background-color:var(--isw-ml-btn-hover-bg);color:var(--isw-ml-btn-hover-text);border-color:var(--isw-ml-btn-hover-border);}</style>';
    $isw_ml_form .= '<input type="text" name="isw_ml_name" placeholder="' . esc_attr( $name_placeholder ) . '" required style="color:' . esc_attr( $input_text_color ) . '; border-color:' . esc_attr( $input_border_color ) . ';width:' . esc_attr( $inp_width ) . '; padding:' . esc_attr( $input_padding ) . '; border-radius: ' . esc_attr( $input_border_radius ) . 'px;' . $inp_align_css . '">';
    $isw_ml_form .= '<input type="email" name="isw_ml_email" placeholder="' . esc_attr( $email_placeholder ) . '" required style="color:' . esc_attr( $input_text_color ) . '; border-color:' . esc_attr( $input_border_color ) . ';width:' . esc_attr( $inp_width ) . '; padding:' . esc_attr( $input_padding ) . '; border-radius: ' . esc_attr( $input_border_radius ) . 'px;' . $inp_align_css . '">';
    $isw_ml_form .= '<input type="hidden" name="isw_ml_submit" value="1" />';
    $isw_ml_form .= wp_nonce_field( 'isw_ml_form_action', 'isw_ml_form_nonce', true, false );
    $isw_ml_form .= '<input type="submit" name="isw_ml_submit_btn" value="' . esc_attr( $button_text ) . '" style="--isw-ml-btn-bg:' . esc_attr( $button_bg_color ) . ';--isw-ml-btn-text:' . esc_attr( $button_text_color ) . ';--isw-ml-btn-border:' . esc_attr( $button_border_color ) . ';--isw-ml-btn-hover-bg:' . esc_attr( $button_hover_bg_color ) . ';--isw-ml-btn-hover-text:' . esc_attr( $button_hover_text_color ) . ';--isw-ml-btn-hover-border:' . esc_attr( $button_hover_border_color ) . ';font-family:' . esc_attr( $button_font_family ) . ';font-size:' . esc_attr( $button_font_size ) . 'px;font-style:' . esc_attr( $button_font_style ) . ';font-weight:' . esc_attr( $button_font_weight ) . ';line-height:' . esc_attr( $button_line_height ) . ';border-width:' . esc_attr( $button_border_width ) . 'px;border-style:' . esc_attr( $button_border_style ) . ';border-radius:' . esc_attr( $button_border_radius ) . 'px;box-shadow:' . esc_attr( $button_box_shadow ) . ';min-width:' . esc_attr( $btn_width ) . ';padding:' . esc_attr( $button_padding ) . ';' . $btn_align_css . '">';
    $isw_ml_form .= '</form>';
    $isw_ml_form .= '<div class="isw-ml-form-message-target"></div></div>';

    return $isw_ml_form;
}

add_shortcode( 'add_isw_ml_form', 'add_isw_mailinglist_form' );

function isw_mailing_list_form_styles() {
    wp_enqueue_style( 'isw-wp-ml-form', ISW_ML_PLUGIN_URL . 'isw-wp-mailing-list-form.css', array(), ISW_ML_PLUGIN_VERSION );
}

add_action( 'wp_enqueue_scripts', 'isw_mailing_list_form_styles' );

function save_ml_form_to_db() {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    if ( isset( $_POST['isw_ml_submit'] ) ) {
        $nonce = isset( $_POST['isw_ml_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_form_nonce'] ) ) : '';
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'isw_ml_form_action' ) ) {
            isw_ml_redirect_with_status( 'invalid_nonce' );
        }

        $name = isset( $_POST['isw_ml_name'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_name'] ) ) : '';
        $email = isset( $_POST['isw_ml_email'] ) ? sanitize_email( wp_unslash( $_POST['isw_ml_email'] ) ) : '';
        $result = isw_ml_insert_subscriber( $name, $email );

        if ( is_wp_error( $result ) ) {
            isw_ml_redirect_with_status( $result->get_error_code() );
        }

        isw_ml_redirect_with_status( 'success' );
    }
}

add_action( 'init', 'save_ml_form_to_db' );

function isw_ml_render_frontend_outline_script() {
    $input_outline_color = get_option( 'input_outline_color', '#2684FF' );
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var inputs = document.querySelectorAll('.isw-ml-form-container input[type="text"], .isw-ml-form-container input[type="email"]');
        inputs.forEach(function(input){
            input.addEventListener('focus', function(){
                this.style.outlineColor = '<?php echo esc_js( $input_outline_color ); ?>';
                this.style.outlineStyle = 'solid';
                this.style.outlineWidth = '2px';
            });
            input.addEventListener('blur', function(){
                this.style.outlineColor = '';
                this.style.outlineStyle = '';
                this.style.outlineWidth = '';
            });
        });
    });
    </script>
    <?php
}

add_action( 'wp_footer', 'isw_ml_render_frontend_outline_script' );

function isw_ml_enqueue_frontend_js() {
    if ( ! is_admin() ) {
        wp_enqueue_script(
            'isw-ml-frontend',
            ISW_ML_PLUGIN_URL . 'isw-wp-mailing-list-form-frontend.js',
            array( 'jquery' ),
            ISW_ML_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'isw-ml-frontend',
            'isw_ml_ajax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'success_msg' => esc_html( get_option( 'ml_success_message', __( 'Your E-mail address was successfully submitted. Thank you!', 'isw-wp-mailing-list-form' ) ) ),
                'error_msg' => esc_html( get_option( 'ml_error_message', __( 'There was an error with your submission. Please try again.', 'isw-wp-mailing-list-form' ) ) ),
                'duplicate_msg' => __( 'This email address is already subscribed.', 'isw-wp-mailing-list-form' ),
                'invalid_email_msg' => __( 'Please enter a valid email address.', 'isw-wp-mailing-list-form' ),
                'invalid_nonce_msg' => __( 'Security check failed. Please refresh the page and try again.', 'isw-wp-mailing-list-form' ),
            )
        );
    }
}

add_action( 'wp_enqueue_scripts', 'isw_ml_enqueue_frontend_js' );

function isw_ml_ajax_submit() {
    if ( isset( $_POST['isw_ml_submit'] ) ) {
        $nonce = isset( $_POST['isw_ml_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_form_nonce'] ) ) : '';
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'isw_ml_form_action' ) ) {
            wp_send_json_error( array( 'reason' => 'invalid_nonce' ) );
        }

        $name = isset( $_POST['isw_ml_name'] ) ? sanitize_text_field( wp_unslash( $_POST['isw_ml_name'] ) ) : '';
        $email = isset( $_POST['isw_ml_email'] ) ? sanitize_email( wp_unslash( $_POST['isw_ml_email'] ) ) : '';
        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'reason' => 'invalid_email' ) );
        }

        $result = isw_ml_insert_subscriber( $name, $email );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'reason' => $result->get_error_code() ) );
        }

        wp_send_json_success();
    }

    wp_send_json_error( array( 'reason' => 'other' ) );
}

add_action( 'wp_ajax_isw_ml_submit', 'isw_ml_ajax_submit' );
add_action( 'wp_ajax_nopriv_isw_ml_submit', 'isw_ml_ajax_submit' );