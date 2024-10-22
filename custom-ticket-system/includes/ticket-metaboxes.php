<?php
function cts_register_ticket_metaboxes() {
    add_meta_box(
        'cts_ticket_info',
        'Información del Ticket',
        'cts_ticket_info_callback',
        'ticket',
        'normal',
        'high'
    );
}

function cts_ticket_info_callback($post) {
    wp_nonce_field('cts_save_ticket_info', 'cts_ticket_info_nonce');
    $status = get_post_meta($post->ID, '_ticket_status', true);
    $priority = get_post_meta($post->ID, '_ticket_priority', true);
    $nombre = get_post_meta($post->ID, '_ticket_nombre', true);
    $apellidos = get_post_meta($post->ID, '_ticket_apellidos', true);
    $email = get_post_meta($post->ID, '_ticket_email', true);
    $telefono = get_post_meta($post->ID, '_ticket_telefono', true);
    $tipo_entidad = get_post_meta($post->ID, '_ticket_tipo_entidad', true);
    $nombre_entidad = get_post_meta($post->ID, '_ticket_nombre_entidad', true);
    $unidades = get_post_meta($post->ID, '_ticket_unidades', true);
    ?>
    <p>
        <label for="ticket_status">Estado:</label>
        <select name="ticket_status" id="ticket_status">
            <option value="nuevo" <?php selected($status, 'nuevo'); ?>>Nuevo</option>
            <option value="procesando" <?php selected($status, 'procesando'); ?>>Procesando</option>
            <option value="cerrado" <?php selected($status, 'cerrado'); ?>>Cerrado</option>
        </select>
    </p>
    <p>
        <label for="ticket_priority">Prioridad:</label>
        <select name="ticket_priority" id="ticket_priority">
            <option value="baja" <?php selected($priority, 'baja'); ?>>Baja</option>
            <option value="media" <?php selected($priority, 'media'); ?>>Media</option>
            <option value="alta" <?php selected($priority, 'alta'); ?>>Alta</option>
        </select>
    </p>
    <p>
        <label for="ticket_nombre">Nombre:</label>
        <input type="text" id="ticket_nombre" name="ticket_nombre" value="<?php echo esc_attr($nombre); ?>" readonly>
    </p>
    <p>
        <label for="ticket_apellidos">Apellidos:</label>
        <input type="text" id="ticket_apellidos" name="ticket_apellidos" value="<?php echo esc_attr($apellidos); ?>" readonly>
    </p>
    <p>
        <label for="ticket_email">Email:</label>
        <input type="email" id="ticket_email" name="ticket_email" value="<?php echo esc_attr($email); ?>" readonly>
    </p>
    <p>
        <label for="ticket_telefono">Teléfono:</label>
        <input type="text" id="ticket_telefono" name="ticket_telefono" value="<?php echo esc_attr($telefono); ?>" readonly>
    </p>
    <p>
        <label for="ticket_tipo_entidad">Tipo de Entidad:</label>
        <input type="text" id="ticket_tipo_entidad" name="ticket_tipo_entidad" value="<?php echo esc_attr($tipo_entidad); ?>" readonly>
    </p>
    <p>
        <label for="ticket_nombre_entidad">Nombre de la Entidad:</label>
        <input type="text" id="ticket_nombre_entidad" name="ticket_nombre_entidad" value="<?php echo esc_attr($nombre_entidad); ?>" readonly>
    </p>
    <p>
        <label for="ticket_unidades">Unidades:</label>
        <input type="text" id="ticket_unidades" name="ticket_unidades" value="<?php echo esc_attr(implode(', ', (array)$unidades)); ?>" readonly>
    </p>
    <?php
}

function cts_save_ticket_info($post_id) {
    if (!isset($_POST['cts_ticket_info_nonce']) || !wp_verify_nonce($_POST['cts_ticket_info_nonce'], 'cts_save_ticket_info')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = ['ticket_status', 'ticket_priority'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Si el ticket se cierra, deshabilitar comentarios
    if ($_POST['ticket_status'] === 'cerrado') {
        $post = array(
            'ID' => $post_id,
            'comment_status' => 'closed'
        );
        wp_update_post($post);
    }
}
add_action('save_post_ticket', 'cts_save_ticket_info');