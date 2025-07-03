<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

/**
 * Classe para gerenciar a interface administrativa de leads
 */
class PPP_Admin {
    
    /**
     * Instância da classe de banco de dados
     */
    private $db;
    
    /**
     * Construtor
     */
    public function __construct( $db ) {
        $this->db = $db;
        
        // Adicionar menu e submenu no painel administrativo
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Adicionar scripts e estilos para a página administrativa
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // Processar ações administrativas (exportar, excluir)
        add_action( 'admin_init', array( $this, 'process_admin_actions' ) );
    }
    
    /**
     * Adiciona o menu e submenu no painel administrativo
     */
    public function add_admin_menu() {
        // Menu principal
        add_menu_page(
            __( 'Popup Promocional', 'form-popup' ),
            __( 'Popup', 'form-popup' ),
            'manage_options',
            'popup_promocional',
            array( $this, 'render_leads_page' ),
            'dashicons-megaphone',
            30
        );
        
        // Submenu para E-mails (Leads)
        add_submenu_page(
            'popup_promocional',
            __( 'E-mails Coletados', 'form-popup' ),
            __( 'E-mails', 'form-popup' ),
            'manage_options',
            'popup_promocional',
            array( $this, 'render_leads_page' )
        );
        
        // Submenu para Configurações
        add_submenu_page(
            'popup_promocional',
            __( 'Configurações', 'form-popup' ),
            __( 'Configurações', 'form-popup' ),
            'manage_options',
            'popup_promocional_configuracoes',
            'ppp_options_page_html'  // Função que já existe
        );
    }
    
    /**
     * Carrega scripts e estilos para a página administrativa
     */
    public function enqueue_admin_assets( $hook ) {
        // Verificar se estamos na página do nosso plugin
        if ( strpos( $hook, 'page_popup_promocional' ) === false ) {
            return;
        }
        
        // Adicionar estilos CSS para a página administrativa
        wp_enqueue_style(
            'ppp-admin-style',
            PPP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            '1.0'
        );
    }
    
    /**
     * Processa ações administrativas (exportar, excluir)
     */
    public function process_admin_actions() {
        // Verificar se estamos na página do nosso plugin
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'popup_promocional' ) {
            return;
        }
        
        // Verificar permissões
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Exportar para CSV
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'export_csv' ) {
            check_admin_referer( 'ppp_export_csv', 'nonce' );
            $this->db->export_leads_csv();
        }
        
        // Excluir lead
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
            check_admin_referer( 'ppp_delete_lead_' . $_GET['id'], 'nonce' );
            
            $id = intval( $_GET['id'] );
            $this->db->delete_lead( $id );
            
            // Redirecionar para a página principal com mensagem de sucesso
            wp_redirect( add_query_arg( 'message', 'deleted', admin_url( 'admin.php?page=popup_promocional' ) ) );
            exit;
        }
    }
    
    /**
     * Renderiza a página de leads
     */
    public function render_leads_page() {
        // Verificar permissões
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Carregar a classe WP_List_Table se ainda não estiver disponível
        if ( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }
        
        // Carregar nossa classe de tabela de leads
        require_once( PPP_PLUGIN_PATH . 'includes/leads-table.php' );
        
        // Criar instância da tabela
        $leads_table = new PPP_Leads_Table( $this->db );
        $leads_table->prepare_items();
        
        // Exibir mensagens de sucesso
        if ( isset( $_GET['message'] ) && $_GET['message'] === 'deleted' ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Lead excluído com sucesso.', 'form-popup' ) . '</p></div>';
        }
        
        // Renderizar a página
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo esc_html__( 'E-mails Coletados', 'form-popup' ); ?></h1>
            
            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=popup_promocional&action=export_csv' ), 'ppp_export_csv', 'nonce' ); ?>" class="page-title-action">
                <?php echo esc_html__( 'Exportar CSV', 'form-popup' ); ?>
            </a>
            
            <hr class="wp-header-end">
            
            <div id="ppp-leads-list">
                <form method="post">
                    <?php $leads_table->display(); ?>
                </form>
            </div>
        </div>
        <?php
    }
}
