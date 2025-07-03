<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

// Verificar se a classe WP_List_Table existe
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Classe para exibir a tabela de leads usando WP_List_Table
 */
class PPP_Leads_Table extends WP_List_Table {
    
    /**
     * Instância da classe de banco de dados
     */
    private $db;
    
    /**
     * Construtor
     */
    public function __construct( $db ) {
        parent::__construct( array(
            'singular' => 'lead',
            'plural'   => 'leads',
            'ajax'     => false
        ) );
        
        $this->db = $db;
    }
    
    /**
     * Define as colunas da tabela
     */
    public function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'name'       => __( 'Nome', 'form-popup' ),
            'email'      => __( 'E-mail', 'form-popup' ),
            'ip_address' => __( 'Endereço IP', 'form-popup' ),
            'created_at' => __( 'Data/Hora', 'form-popup' )
        );
    }
    
    /**
     * Define quais colunas são ordenáveis
     */
    public function get_sortable_columns() {
        return array(
            'name'       => array( 'name', false ),
            'email'      => array( 'email', false ),
            'created_at' => array( 'created_at', true )
        );
    }
    
    /**
     * Define as ações em massa
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __( 'Excluir', 'form-popup' )
        );
    }
    
    /**
     * Prepara os itens para exibição
     */
    public function prepare_items() {
        // Definir colunas
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        // Processar ações em massa
        $this->process_bulk_action();
        
        // Parâmetros de paginação
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        // Parâmetros de ordenação
        $orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'created_at';
        $order = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';
        
        // Obter dados
        $data = $this->db->get_leads( $per_page, $current_page, $orderby, $order );
        
        // Configurar paginação
        $total_items = $this->db->count_leads();
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
        
        // Definir itens
        $this->items = $data;
    }
    
    /**
     * Renderiza a coluna de checkbox
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="leads[]" value="%s" />',
            $item['id']
        );
    }
    
    /**
     * Renderiza a coluna de nome com ações
     */
    public function column_name( $item ) {
        // Criar URL de exclusão com nonce
        $delete_url = wp_nonce_url(
            admin_url( sprintf( 'admin.php?page=popup_promocional&action=delete&id=%s', $item['id'] ) ),
            'ppp_delete_lead_' . $item['id'],
            'nonce'
        );
        
        // Definir ações
        $actions = array(
            'delete' => sprintf( '<a href="%s">%s</a>', $delete_url, __( 'Excluir', 'form-popup' ) )
        );
        
        // Retornar nome com ações
        return sprintf(
            '<strong>%1$s</strong> %2$s',
            esc_html( $item['name'] ),
            $this->row_actions( $actions )
        );
    }
    
    /**
     * Renderiza a coluna de data/hora
     */
    public function column_created_at( $item ) {
        // Formatar data/hora para o formato local
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        
        $date_time = mysql2date( $date_format . ' ' . $time_format, $item['created_at'] );
        
        return esc_html( $date_time );
    }
    
    /**
     * Renderiza as colunas padrão
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'email':
            case 'ip_address':
                return esc_html( $item[ $column_name ] );
            default:
                return print_r( $item, true );
        }
    }
    
    /**
     * Processa ações em massa
     */
    public function process_bulk_action() {
        // Verificar permissões
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Verificar ação
        if ( 'delete' === $this->current_action() ) {
            // Verificar nonce
            check_admin_referer( 'bulk-' . $this->_args['plural'] );
            
            // Verificar se há leads selecionados
            if ( isset( $_POST['leads'] ) && is_array( $_POST['leads'] ) ) {
                foreach ( $_POST['leads'] as $lead_id ) {
                    $this->db->delete_lead( intval( $lead_id ) );
                }
                
                // Redirecionar para a página principal com mensagem de sucesso
                wp_redirect( add_query_arg( 'message', 'deleted', admin_url( 'admin.php?page=popup_promocional' ) ) );
                exit;
            }
        }
    }
    
    /**
     * Mensagem para quando não há leads
     */
    public function no_items() {
        _e( 'Nenhum e-mail coletado ainda.', 'form-popup' );
    }
}
