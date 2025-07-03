<?php
// Impedir acesso direto
defined( 'ABSPATH' ) or die;

/**
 * Classe para gerenciar a tabela de leads do popup promocional
 */
class PPP_Database {
    
    // Nome da tabela (sem prefixo)
    private $table_name = 'ppp_leads';
    
    /**
     * Construtor
     */
    public function __construct() {
        // Registrar hook de ativação para criar a tabela
        register_activation_hook( PPP_PLUGIN_FILE, array( $this, 'create_table' ) );
        
        // Verificar se a tabela existe ao inicializar o plugin
        add_action( 'plugins_loaded', array( $this, 'check_table' ) );
    }
    
    /**
     * Obtém o nome completo da tabela com prefixo
     */
    public function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }
    
    /**
     * Cria a tabela de leads no banco de dados
     */
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $this->get_table_name();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            ip_address varchar(45) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * Verifica se a tabela existe e cria se necessário
     */
    public function check_table() {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $this->create_table();
        }
    }
    
    /**
     * Salva um novo lead na tabela
     */
    public function save_lead( $name, $email ) {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        // Obter endereço IP do usuário
        $ip_address = $this->get_client_ip();
        
        // Inserir dados na tabela
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'ip_address' => $ip_address,
                'created_at' => current_time( 'mysql' )
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        
        return $result;
    }
    
    /**
     * Obtém todos os leads da tabela
     */
    public function get_leads( $per_page = 20, $page_number = 1, $orderby = 'created_at', $order = 'DESC' ) {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        // Sanitizar parâmetros
        $orderby = sanitize_sql_orderby( $orderby ) ? $orderby : 'created_at';
        $order = strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql = "SELECT * FROM $table_name ORDER BY $orderby $order";
        
        if ( $per_page > 0 ) {
            $offset = ( $page_number - 1 ) * $per_page;
            $sql .= " LIMIT $per_page OFFSET $offset";
        }
        
        return $wpdb->get_results( $sql, ARRAY_A );
    }
    
    /**
     * Retorna o número total de leads
     */
    public function count_leads() {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    }
    
    /**
     * Exclui um lead pelo ID
     */
    public function delete_lead( $id ) {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        return $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
            array( '%d' )
        );
    }
    
    /**
     * Obtém o endereço IP do cliente
     */
    private function get_client_ip() {
        $ip = '';
        
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field( $ip );
    }
    
    /**
     * Exporta todos os leads para CSV
     */
    public function export_leads_csv() {
        global $wpdb;
        $table_name = $this->get_table_name();
        
        $leads = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A );
        
        if ( empty( $leads ) ) {
            return false;
        }
        
        $filename = 'popup-leads-' . date('Y-m-d') . '.csv';
        
        // Cabeçalhos para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        // Criar arquivo CSV
        $output = fopen('php://output', 'w');
        
        // Cabeçalhos do CSV
        fputcsv($output, array('ID', 'Nome', 'E-mail', 'Endereço IP', 'Data/Hora'));
        
        // Dados
        foreach ( $leads as $lead ) {
            fputcsv($output, array(
                $lead['id'],
                $lead['name'],
                $lead['email'],
                $lead['ip_address'],
                $lead['created_at']
            ));
        }
        
        fclose($output);
        exit;
    }
}
