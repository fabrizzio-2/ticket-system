<?php
function cts_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=ticket',
        'Lista de Tickets',
        'Lista de Tickets',
        'manage_options',
        'cts-ticket-list',
        'cts_ticket_list_page'
    );
}
add_action('admin_menu', 'cts_add_admin_menu');

function cts_ticket_list_page() {
    ?>
    <div class="wrap">
        <h1>Lista de Tickets</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Autor</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tickets = get_posts(array(
                    'post_type' => 'ticket',
                    'posts_per_page' => -1
                ));

                foreach ($tickets as $ticket) {
                    $status = get_post_meta($ticket->ID, '_ticket_status', true);
                    ?>
                    <tr>
                        <td><?php echo $ticket->ID; ?></td>
                        <td><a href="#" class="ticket-details" data-ticket-id="<?php echo $ticket->ID; ?>"><?php echo $ticket->post_title; ?></a></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo get_the_author_meta('display_name', $ticket->post_author); ?></td>
                        <td><?php echo get_the_date('', $ticket->ID); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div id="ticket-content" style="margin-top: 20px;"></div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.ticket-details').on('click', function(e) {
            e.preventDefault();
            var ticketId = $(this).data('ticket-id');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_ticket_details',
                    ticket_id: ticketId
                },
                success: function(response) {
                    $('#ticket-content').html(response);
                }
            });
        });
    });
    </script>
    <?php
}

function cts_get_ticket_details() {
    $ticket_id = intval($_POST['ticket_id']);
    $ticket = get_post($ticket_id);
    
    if ($ticket && $ticket->post_type === 'ticket') {
        $output = '<h2>' . esc_html($ticket->post_title) . '</h2>';
        $output .= '<div>' . wpautop($ticket->post_content) . '</div>';
        
        $meta_fields = array(
            'Nombre' => '_ticket_nombre',
            'Apellidos' => '_ticket_apellidos',
            'Email' => '_ticket_email',
            'Teléfono' => '_ticket_telefono',
            'Tipo de Entidad' => '_ticket_tipo_entidad',
            'Nombre de la Entidad' => '_ticket_nombre_entidad',
            'Unidades' => '_ticket_unidades'
        );
        
        $output .= '<h3>Detalles adicionales</h3>';
        $output .= '<ul>';
        foreach ($meta_fields as $label => $key) {
            $value = get_post_meta($ticket_id, $key, true);
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $output .= '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</li>';
        }
        $output .= '</ul>';
        
        echo $output;
    } else {
        echo 'Ticket no encontrado.';
    }
    
    wp_die();
}
add_action('wp_ajax_get_ticket_details', 'cts_get_ticket_details');