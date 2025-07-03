<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

// Adiciona a página de configurações ao menu do WordPress
function ppp_add_config_submenu() {
    add_submenu_page(
        'popup_promocional',
        __( 'Configurações', 'form-popup' ),
        __( 'Configurações', 'form-popup' ),
        'manage_options',
        'popup_promocional_configuracoes',
        'ppp_options_page_html'
    );
}
add_action( 'admin_menu', 'ppp_add_config_submenu' );

// Registra as configurações
function ppp_settings_init() {
    register_setting( 'ppp_options_group', 'ppp_settings' );

    add_settings_section(
        'ppp_settings_section_main',
        __( 'Configurações Principais', 'form-popup' ),
        'ppp_settings_section_main_callback',
        'ppp_options_group'
    );

    add_settings_field(
        'ppp_field_enable',
        __( 'Ativar Popup', 'form-popup' ),
        'ppp_field_enable_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_webhook_url',
        __( 'URL do Webhook', 'form-popup' ),
        'ppp_field_webhook_url_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

     add_settings_field(
        'ppp_field_popup_title',
        __( 'Título do Popup (Etapa 1)', 'form-popup' ),
        'ppp_field_popup_title_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_popup_desc',
        __( 'Descrição (Etapa 1)', 'form-popup' ),
        'ppp_field_popup_desc_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_coupon_text',
        __( 'Texto/Cupom (Etapa 2)', 'form-popup' ),
        'ppp_field_coupon_text_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

     add_settings_field(
        'ppp_field_image_url',
        __( 'URL da Imagem (Opcional)', 'form-popup' ),
        'ppp_field_image_url_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );
    
    // Seção para Integração com Mautic
    add_settings_section(
        'ppp_settings_section_mautic',
        __( 'Integração com Mautic', 'form-popup' ),
        'ppp_settings_section_mautic_callback',
        'ppp_options_group'
    );
    
    add_settings_field(
        'ppp_field_mautic_enable',
        __( 'Ativar Integração Mautic', 'form-popup' ),
        'ppp_field_mautic_enable_render',
        'ppp_options_group',
        'ppp_settings_section_mautic'
    );
    
    add_settings_field(
        'ppp_field_mautic_url',
        __( 'URL do Mautic', 'form-popup' ),
        'ppp_field_mautic_url_render',
        'ppp_options_group',
        'ppp_settings_section_mautic'
    );
    
    add_settings_field(
        'ppp_field_mautic_form_id',
        __( 'ID do Formulário', 'form-popup' ),
        'ppp_field_mautic_form_id_render',
        'ppp_options_group',
        'ppp_settings_section_mautic'
    );
    
    add_settings_field(
        'ppp_field_mautic_form_name',
        __( 'Nome do Formulário', 'form-popup' ),
        'ppp_field_mautic_form_name_render',
        'ppp_options_group',
        'ppp_settings_section_mautic'
    );
}
add_action( 'admin_init', 'ppp_settings_init' );

// Funções de renderização dos campos
function ppp_settings_section_main_callback() {
    echo __( 'Configure as opções do Popup Promocional.', 'form-popup' );
}

function ppp_field_enable_render() {
    $options = get_option( 'ppp_settings' );
    $checked = isset( $options['enable'] ) ? checked( $options['enable'], 1, false ) : '';
    echo '<input type="checkbox" name="ppp_settings[enable]" value="1" ' . $checked . '>';
}

function ppp_field_webhook_url_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['webhook_url'] ) ? esc_url( $options['webhook_url'] ) : '';
    echo '<input type="url" name="ppp_settings[webhook_url]" value="' . $value . '" class="regular-text">';
    echo '<p class="description">' . __( 'URL para onde os dados (nome e e-mail) serão enviados via POST.', 'form-popup' ) . '</p>';
}

function ppp_field_popup_title_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['popup_title'] ) ? sanitize_text_field( $options['popup_title'] ) : 'VOCÊ GANHOU 5% DE DESCONTO';
    echo '<input type="text" name="ppp_settings[popup_title]" value="' . esc_attr( $value ) . '" class="regular-text">';
}

function ppp_field_popup_desc_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['popup_desc'] ) ? sanitize_textarea_field( $options['popup_desc'] ) : 'Basta digitar seu nome e e-mail abaixo:';
    echo '<textarea name="ppp_settings[popup_desc]" rows="3" class="regular-text">' . esc_textarea( $value ) . '</textarea>';
}

function ppp_field_coupon_text_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['coupon_text'] ) ? sanitize_text_field( $options['coupon_text'] ) : 'CUPOM: MAISVOCE';
    echo '<input type="text" name="ppp_settings[coupon_text]" value="' . esc_attr( $value ) . '" class="regular-text">';
    echo '<p class="description">' . __( 'Texto ou código do cupom a ser exibido na segunda etapa.', 'form-popup' ) . '</p>';
}

function ppp_field_image_url_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['image_url'] ) ? esc_url( $options['image_url'] ) : '';
    echo '<input type="url" name="ppp_settings[image_url]" value="' . $value . '" class="regular-text">';
    echo '<p class="description">' . __( 'URL da imagem a ser exibida no popup (opcional). Deixe em branco para não exibir.', 'form-popup' ) . '</p>';
}

// Funções para a seção Mautic
function ppp_settings_section_mautic_callback() {
    echo __( 'Configure a integração com o Mautic para captura de leads.', 'form-popup' );
}

function ppp_field_mautic_enable_render() {
    $options = get_option( 'ppp_settings' );
    $checked = isset( $options['mautic_enable'] ) ? checked( $options['mautic_enable'], 1, false ) : '';
    echo '<input type="checkbox" name="ppp_settings[mautic_enable]" value="1" ' . $checked . '>';
    echo '<p class="description">' . __( 'Ative para enviar os dados do formulário para o Mautic.', 'form-popup' ) . '</p>';
}

function ppp_field_mautic_url_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['mautic_url'] ) ? esc_url( $options['mautic_url'] ) : '';
    echo '<input type="url" name="ppp_settings[mautic_url]" value="' . $value . '" class="regular-text">';
    echo '<p class="description">' . __( 'URL base da sua instalação do Mautic (ex: http://mautic.seudominio.com.br)', 'form-popup' ) . '</p>';
}

function ppp_field_mautic_form_id_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['mautic_form_id'] ) ? sanitize_text_field( $options['mautic_form_id'] ) : '';
    echo '<input type="text" name="ppp_settings[mautic_form_id]" value="' . esc_attr( $value ) . '" class="regular-text">';
    echo '<p class="description">' . __( 'ID do formulário no Mautic.', 'form-popup' ) . '</p>';
}

function ppp_field_mautic_form_name_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['mautic_form_name'] ) ? sanitize_text_field( $options['mautic_form_name'] ) : '';
    echo '<input type="text" name="ppp_settings[mautic_form_name]" value="' . esc_attr( $value ) . '" class="regular-text">';
    echo '<p class="description">' . __( 'Nome do formulário no Mautic.', 'form-popup' ) . '</p>';
}

// HTML da página de opções
function ppp_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'ppp_options_group' );
            do_settings_sections( 'ppp_options_group' );
            submit_button( __( 'Salvar Configurações', 'form-popup' ) );
            ?>
        </form>
    </div>
    <?php
}
