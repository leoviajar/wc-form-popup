<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

// Enqueue scripts e estilos para o frontend
function ppp_enqueue_frontend_assets() {
    $options = get_option( 'ppp_settings' );

    // Só carrega se o popup estiver ativo nas configurações
    if ( ! isset( $options['enable'] ) || ! $options['enable'] || ! is_product() ) {
        return;
    }

    // Carrega o CSS
    wp_enqueue_style(
        'ppp-style',
        PPP_PLUGIN_URL . 'assets/css/style.css',
        [],
        '1.0'
    );

    // Carrega o JavaScript
    wp_enqueue_script(
        'ppp-script',
        PPP_PLUGIN_URL . 'assets/js/script.js',
        [ 'jquery' ], // Depende do jQuery
        '1.0',
        true // Carrega no footer
    );



    // Configurações do Mautic
    $mautic_config = [
        'enabled'   => isset($options['mautic_enable']) ? (bool)$options['mautic_enable'] : false,
        'url'       => isset($options['mautic_url']) ? esc_url($options['mautic_url']) : '',
        'form_id'   => isset($options['mautic_form_id']) ? esc_attr($options['mautic_form_id']) : '',
        'form_name' => isset($options['mautic_form_name']) ? esc_attr($options['mautic_form_name']) : ''
    ];

    // Passa dados do PHP para o JavaScript (configurações, ajax url, nonce)
    wp_localize_script( 'ppp-script', 'ppp_ajax_object', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'ppp_submit_form_nonce' ),
        'settings' => [
            'image_id'           => isset($options['image_id']) ? absint($options['image_id']) : '',
            'image_size_type'    => isset($options['image_size_type']) ? sanitize_text_field($options['image_size_type']) : 'full',
            'image_custom_width' => isset($options['image_custom_width']) ? absint($options['image_custom_width']) : '',
            'title'              => isset($options['popup_title']) ? esc_html($options['popup_title']) : '',
            'description'        => isset($options['popup_desc']) ? esc_html($options['popup_desc']) : '',
            'coupon_liberated_title' => isset($options['coupon_liberated_title']) ? esc_html($options['coupon_liberated_title']) : 'CUPOM LIBERADO!',
            'coupon_text'        => isset($options['coupon_text']) ? esc_html($options['coupon_text']) : '',
            'coupon_description' => isset($options['coupon_text_description']) ? esc_html($options['coupon_text_description']) : 'Copie o código abaixo para usá-lo na finalização da compra e ganhar 5% de desconto.',
        ],
        'mautic'   => $mautic_config
    ]);

    // Adiciona o HTML do popup ao footer
    add_action( 'wp_footer', 'ppp_render_popup_html' );
}
add_action( 'wp_enqueue_scripts', 'ppp_enqueue_frontend_assets' );

// Renderiza o HTML base do popup
function ppp_render_popup_html() {
    $options = get_option( 'ppp_settings' );
    $image_id = isset($options['image_id']) ? absint($options['image_id']) : '';
    $image_size = isset($options['image_size']) ? sanitize_text_field($options['image_size']) : 'full';
    $image_html = '';

    if ( $image_id ) {
        $image_size_type = isset( $options["image_size_type"] ) ? sanitize_text_field( $options["image_size_type"] ) : "full";
        $image_custom_width = isset( $options["image_custom_width"] ) ? absint( $options["image_custom_width"] ) : null;

        if ( $image_size_type === "custom" && $image_custom_width ) {
            $image_url = wp_get_attachment_image_url( $image_id, array( $image_custom_width, 9999 ), false );
            $image_style = 'width:' . esc_attr( $image_custom_width ) . 'px; height: auto;';
        } else {
            $image_url = wp_get_attachment_image_url( $image_id, "full" );
            $image_style = 'width: 100%; height: auto;'; // Ou remova esta linha se o CSS já lida com isso
        }
        if ( ! empty( $image_url ) ) {
            $image_html = 
                '<img src="' . esc_url( $image_url ) . '" alt="Popup Image" style="' . $image_style . '">';
        }
    }

    $title = isset($options['popup_title']) ? esc_html($options['popup_title']) : '';
    $description = isset($options['popup_desc']) ? esc_html($options['popup_desc']) : '';

    ?>
    <div id="ppp-popup-overlay">
        <div id="ppp-popup-container">
            <button id="ppp-close-button" aria-label="<?php _e( 'Fechar', 'form-popup' ); ?>">&times;</button>
            <div id="ppp-popup-content">
                <?php if ( ! empty( $image_html ) ) : ?>
                    <div class="ppp-popup-image">
                        <?php echo $image_html; ?>
                    </div>
                <?php endif; ?>
                <div class="ppp-popup-form-section">
                    <!-- Conteúdo da Etapa 1 será injetado aqui pelo JS -->
                </div>
            </div>
        </div>
    </div>
    <?php
}
