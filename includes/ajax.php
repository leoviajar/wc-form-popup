<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

// Manipulador AJAX para submissão do formulário
function ppp_handle_form_submission() {
    // Verificar nonce de segurança
    check_ajax_referer( 'ppp_submit_form_nonce', 'nonce' );

    // Obter e sanitizar dados do POST
    $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

    // Validação básica (simplesmente verifica se não estão vazios)
    if ( empty( $name ) || empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'Por favor, preencha o nome e um e-mail válido.', 'form-popup' ) ] );
        return;
    }

    // Salvar dados no banco de dados
    global $ppp_database;
    if ( isset( $ppp_database ) ) {
        $ppp_database->save_lead( $name, $email );
    }

    // Obter URL do webhook das configurações
    $options = get_option( 'ppp_settings' );
    $webhook_url = isset( $options['webhook_url'] ) ? esc_url_raw( $options['webhook_url'] ) : '';

    // Se a URL do webhook estiver definida, enviar os dados
    if ( ! empty( $webhook_url ) ) {
        $response = wp_remote_post( $webhook_url, [
            'method'    => 'POST',
            'timeout'   => 15, // Aumentar timeout se necessário
            'blocking'  => true, // Espera pela resposta, mas não por muito tempo
            'headers'   => [ 'Content-Type' => 'application/json; charset=utf-8' ],
            'body'      => json_encode( [ 'name' => $name, 'email' => $email ] ),
            'data_format' => 'body',
        ]);

        // Verificar se houve erro no envio para o webhook
        if ( is_wp_error( $response ) ) {
            // Logar o erro (opcional, bom para debug)
            // error_log( 'Erro ao enviar para webhook: ' . $response->get_error_message() );
            // Informar o usuário sobre o erro pode não ser ideal, mas enviar sucesso mesmo assim
            // ou enviar um erro específico? Por ora, envia sucesso para não bloquear o usuário.
            // wp_send_json_error( [ 'message' => __( 'Erro ao conectar com o servidor externo.', 'form-popup' ) ] );
            // return;
        } else {
            $http_code = wp_remote_retrieve_response_code( $response );
            // Considerar sucesso se for 2xx
            if ( $http_code < 200 || $http_code >= 300 ) {
                // Logar o erro (opcional)
                // error_log( 'Webhook retornou status HTTP não esperado: ' . $http_code );
                // Novamente, decidir se informa o erro ao usuário.
            }
        }
    }

    // Enviar resposta de sucesso para o JavaScript
    wp_send_json_success();
}
// Hook para usuários logados e não logados
add_action( 'wp_ajax_ppp_submit_form', 'ppp_handle_form_submission' );
add_action( 'wp_ajax_nopriv_ppp_submit_form', 'ppp_handle_form_submission' );