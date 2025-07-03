<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

// Enqueue scripts e estilos para o frontend
function ppp_enqueue_frontend_assets() {
    $options = get_option( 'ppp_settings' );

    // Só carrega se o popup estiver ativo nas configurações
    if ( ! isset( $options['enable'] ) || ! $options['enable'] ) {
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
            'image_url'   => isset($options['image_url']) ? esc_url($options['image_url']) : '',
            'title'       => isset($options['popup_title']) ? esc_html($options['popup_title']) : '',
            'description' => isset($options['popup_desc']) ? esc_html($options['popup_desc']) : '',
            'coupon_text' => isset($options['coupon_text']) ? esc_html($options['coupon_text']) : '',
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
    $image_url = isset($options['image_url']) ? esc_url($options['image_url']) : '';
    $title = isset($options['popup_title']) ? esc_html($options['popup_title']) : '';
    $description = isset($options['popup_desc']) ? esc_html($options['popup_desc']) : '';

    ?>
    <div id="ppp-popup-overlay">
        <div id="ppp-popup-container">
            <button id="ppp-close-button" aria-label="<?php _e( 'Fechar', 'form-popup' ); ?>">&times;</button>
            <div id="ppp-popup-content">
                <?php if ( ! empty( $image_url ) ) : ?>
                    <div class="ppp-popup-image">
                        <img src="<?php echo $image_url; ?>" alt="<?php _e( 'Imagem Promocional', 'form-popup' ); ?>">
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
