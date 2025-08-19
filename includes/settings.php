<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

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
        'ppp_field_coupon_liberated_title',
        __( 'Título do Popup (Etapa 2)', 'form-popup' ),
        'ppp_field_coupon_liberated_title_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_coupon_text_description',
        __( 'Descrição do Cupom (Etapa 2)', 'form-popup' ),
        'ppp_field_coupon_text_description_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_coupon_text',
        __( 'Código do cupom (Etapa 2)', 'form-popup' ),
        'ppp_field_coupon_text_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_image_id',
        __( 'Imagem do Popup (Opcional)', 'form-popup' ),
        'ppp_field_image_id_render',
        'ppp_options_group',
        'ppp_settings_section_main'
    );

    add_settings_field(
        'ppp_field_image_size',
        __( 'Tamanho da Imagem (Ex: 150x150)', 'form-popup' ),
        'ppp_field_image_size_render',
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

function ppp_field_coupon_liberated_title_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['coupon_liberated_title'] ) ? sanitize_text_field( $options['coupon_liberated_title'] ) : 'CUPOM LIBERADO!';
    echo '<input type="text" name="ppp_settings[coupon_liberated_title]" value="' . esc_attr( $value ) . '" class="regular-text">';
    echo '<p class="description">' . __( 'Título a ser exibido na segunda etapa do popup (ex: CUPOM LIBERADO!).', 'form-popup' ) . '</p>';
}

function ppp_field_coupon_text_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['coupon_text'] ) ? sanitize_text_field( $options['coupon_text'] ) : 'CUPOM: MAISVOCE';
    echo '<input type="text" name="ppp_settings[coupon_text]" value="' . esc_attr( $value ) . '" class="regular-text">';
    echo '<p class="description">' . __( 'Texto ou código do cupom a ser exibido na segunda etapa.', 'form-popup' ) . '</p>';
}

function ppp_field_image_id_render() {
    $options = get_option( 'ppp_settings' );
    $image_id = isset( $options['image_id'] ) ? absint( $options['image_id'] ) : '';
    $image_url = '';
    if ( $image_id ) {
        $image_url = wp_get_attachment_image_url( $image_id, 'full' );
    }

    echo '<input type="hidden" name="ppp_settings[image_id]" id="ppp_image_id" value="' . esc_attr( $image_id ) . '">';
    echo '<div id="ppp_image_preview" style="max-width: 200px; max-height: 200px; border: 1px solid #eee; margin-bottom: 10px; display: flex; justify-content: center; align-items: center; overflow: hidden;">';
    echo '<img id="ppp_image_tag" src="' . esc_url( $image_url ) . '" style="max-width: 100%; max-height: 100%; height: auto; display: ' . ( $image_url ? 'block' : 'none' ) . ';">';
    echo '</div>';
    echo '<div id="ppp_image_preview" style="display: flex; gap: 10px">';
    echo '<button type="button" class="button ppp_upload_image_button">' . __( 'Selecionar Imagem', 'form-popup' ) . '</button>';
    echo '<button type="button" class="button ppp_remove_image_button" style="' . ( $image_id ? '' : 'display:none;' ) . '">' . __( 'Remover Imagem', 'form-popup' ) . '</button>';
    echo '</div>';
    echo '<p class="description">' . __( 'Selecione uma imagem da biblioteca de mídia para exibir no popup.', 'form-popup' ) . '</p>';
}

function ppp_field_image_size_render() {
    $options = get_option( 'ppp_settings' );
    $image_size_type = isset( $options['image_size_type'] ) ? sanitize_text_field( $options['image_size_type'] ) : 'full';
    $image_custom_width = isset( $options['image_custom_width'] ) ? absint( $options['image_custom_width'] ) : '';

    echo '<select name="ppp_settings[image_size_type]" id="ppp_image_size_type">';
    echo '<option value="full" ' . selected( $image_size_type, 'full', false ) . '>' . __( 'Tamanho Completo (Full)', 'form-popup' ) . '</option>';
    echo '<option value="custom" ' . selected( $image_size_type, 'custom', false ) . '>' . __( 'Personalizado', 'form-popup' ) . '</option>';
    echo '</select>';

    echo '<div id="ppp_custom_width_field" style="margin-top: 10px;">';
    echo '<label for="ppp_image_custom_width">' . __( 'Largura Personalizada (px):', 'form-popup' ) . '</label>';
    echo '<input type="number" name="ppp_settings[image_custom_width]" id="ppp_image_custom_width" value="' . esc_attr( $image_custom_width ) . '" class="small-text">';
    echo '<p class="description">' . __( 'Insira a largura desejada em pixels para a imagem.', 'form-popup' ) . '</p>';
    echo '</div>';
}

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

function ppp_field_coupon_text_description_render() {
    $options = get_option( 'ppp_settings' );
    $value = isset( $options['coupon_text_description'] ) ? sanitize_textarea_field( $options['coupon_text_description'] ) : 'Copie o código abaixo para usá-lo na finalização da compra e ganhar 5% de desconto.';
    echo '<textarea name="ppp_settings[coupon_text_description]" rows="3" class="regular-text">' . esc_textarea( $value ) . '</textarea>';
    echo '<p class="description">' . __( 'Texto descritivo a ser exibido na segunda etapa do popup, abaixo do título do cupom.', 'form-popup' ) . '</p>';
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