<?php
function cts_register_ticket_post_type() {
    $labels = array(
        'name'               => 'Tickets',
        'singular_name'      => 'Ticket',
        'menu_name'          => 'Tickets',
        'name_admin_bar'     => 'Ticket',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Ticket',
        'new_item'           => 'Nuevo Ticket',
        'edit_item'          => 'Editar Ticket',
        'view_item'          => 'Ver Ticket',
        'all_items'          => 'Todos los Tickets',
        'search_items'       => 'Buscar Tickets',
        'parent_item_colon'  => 'Ticket Padre:',
        'not_found'          => 'No se encontraron tickets.',
        'not_found_in_trash' => 'No se encontraron tickets en la papelera.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'ticket'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'comments')
    );

    register_post_type('ticket', $args);
}

// Restringir la visibilidad de los tickets
function cts_posts_for_current_author($query) {
    if(!is_admin() && $query->is_main_query() && !current_user_can('manage_options')) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'cts_posts_for_current_author');