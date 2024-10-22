<?php
function cts_enqueue_scripts() {
    wp_enqueue_script('jquery-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', array('jquery'), '1.14.16', true);
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('cts-script', plugin_dir_url(__FILE__) . 'js/cts-script.js', array('jquery', 'jquery-mask', 'select2'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'cts_enqueue_scripts');

function cts_ticket_form_shortcode() {
    ob_start();
    if (is_user_logged_in()) {
        ?>
        <form id="new-ticket-form" method="post">
            <div class="form-row">
                <input type="text" id="ticket_nombre" name="ticket_nombre" required placeholder="Nombre">
                <input type="text" id="ticket_apellidos" name="ticket_apellidos" required placeholder="Apellidos">
                <input type="email" id="ticket_email" name="ticket_email" required placeholder="Email">
                <input type="text" id="ticket_telefono" name="ticket_telefono" required placeholder="Teléfono">
            </div>
            <div class="form-row">
                <select id="ticket_tipo_entidad" name="ticket_tipo_entidad" required>
                    <option value="">Seleccione el tipo de entidad</option>
                    <?php
                    $entity_types = get_option('cts_entity_types', array());
                    foreach ($entity_types as $type) {
                        echo '<option value="' . esc_attr($type) . '">' . esc_html($type) . '</option>';
                    }
                    ?>
                </select>
                <input type="text" id="ticket_nombre_entidad" name="ticket_nombre_entidad" required placeholder="Nombre de la entidad">
            </div>
            <div class="form-row">
                <select id="ticket_unidades" name="ticket_unidades[]" multiple required>
                    <?php
                    $unit_names = get_option('cts_unit_names', array());
                    foreach ($unit_names as $unit) {
                        echo '<option value="' . esc_attr($unit) . '">' . esc_html($unit) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-row">
                <input type="text" id="ticket_title" name="ticket_title" required placeholder="Asunto del Ticket">
            </div>
            <div class="form-row">
                <textarea id="ticket_content" name="ticket_content" required placeholder="Descripción detallada del problema"></textarea>
            </div>
            <div class="form-row">
                <?php wp_nonce_field('create_ticket_nonce', 'ticket_nonce'); ?>
                <input type="submit" value="Enviar Ticket">
                <input type="reset" value="Resetear Formulario">
            </div>
        </form>
        <?php
    } else {
        echo 'Debes iniciar sesión para crear un ticket.';
    }
    return ob_get_clean();
}
add_shortcode('ticket_form', 'cts_ticket_form_shortcode');

function cts_process_ticket_submission() {
    if (isset($_POST['ticket_nonce']) && wp_verify_nonce($_POST['ticket_nonce'], 'create_ticket_nonce')) {
        $user_id = email_exists($_POST['ticket_email']);
        
        if (!$user_id) {
            $random_password = wp_generate_password(12, false);
            $user_id = wp_create_user($_POST['ticket_email'], $random_password, $_POST['ticket_email']);
            
            wp_update_user(array(
                'ID' => $user_id,
                'first_name' => sanitize_text_field($_POST['ticket_nombre']),
                'last_name' => sanitize_text_field($_POST['ticket_apellidos'])
            ));
            
            wp_new_user_notification($user_id, null, 'both');
        }
        
        $ticket_data = array(
            'post_title'   => sanitize_text_field($_POST['ticket_title']),
            'post_content' => wp_kses_post($_POST['ticket_content']),
            'post_status'  => 'publish',
            'post_type'    => 'ticket',
            'post_author'  => $user_id
        );

        $ticket_id = wp_insert_post($ticket_data);

        if ($ticket_id) {
            $meta_fields = array(
                '_ticket_status' => 'nuevo',
                '_ticket_nombre' => sanitize_text_field($_POST['ticket_nombre']),
                '_ticket_apellidos' => sanitize_text_field($_POST['ticket_apellidos']),
                '_ticket_email' => sanitize_email($_POST['ticket_email']),
                '_ticket_telefono' => sanitize_text_field($_POST['ticket_telefono']),
                '_ticket_tipo_entidad' => sanitize_text_field($_POST['ticket_tipo_entidad']),
                '_ticket_nombre_entidad' => sanitize_text_field($_POST['ticket_nombre_entidad']),
                '_ticket_unidades' => array_map('sanitize_text_field', $_POST['ticket_unidades'])
            );

            foreach ($meta_fields as $key => $value) {
                update_post_meta($ticket_id, $key, $value);
            }

            wp_redirect(get_permalink($ticket_id));
            exit;
        }
    }
}
add_action('init', 'cts_process_ticket_submission');