<?php
/**
 * Plugin Name: Custom Ticket System
 * Plugin URI: http://tudominio.com/plugin
 * Description: Un sistema de tickets de soporte personalizado para WordPress
 * Version: 1.0
 * Author: Tu Nombre
 * Author URI: http://tudominio.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'includes/ticket-post-type.php');
require_once(plugin_dir_path(__FILE__) . 'includes/ticket-metaboxes.php');
require_once(plugin_dir_path(__FILE__) . 'includes/ticket-frontend.php');
require_once(plugin_dir_path(__FILE__) . 'includes/ticket-admin.php');
require_once(plugin_dir_path(__FILE__) . 'includes/entity-types.php');
require_once(plugin_dir_path(__FILE__) . 'includes/unit-names.php');

function cts_activate() {
    add_role('support_agent', 'Agente de Soporte', array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));
    
    cts_register_ticket_post_type();
    
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cts_activate');

function cts_deactivate() {
    remove_role('support_agent');
    
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cts_deactivate');

function cts_init() {
    cts_register_ticket_post_type();
    cts_register_ticket_metaboxes();
}
add_action('init', 'cts_init');

// Restringir el acceso al dashboard solo para administradores
function cts_restrict_admin_access() {
    if (!current_user_can('manage_options') && !defined('DOING_AJAX')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'cts_restrict_admin_access');