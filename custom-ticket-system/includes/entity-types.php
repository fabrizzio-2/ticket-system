<?php
function cts_add_entity_types_menu() {
    add_submenu_page(
        'edit.php?post_type=ticket',
        'Tipos de Entidad',
        'Tipos de Entidad',
        'manage_options',
        'cts-entity-types',
        'cts_entity_types_page'
    );
}
add_action('admin_menu', 'cts_add_entity_types_menu');

function cts_entity_types_page() {
    if (isset($_POST['cts_entity_types'])) {
        $entity_types = array_map('sanitize_text_field', explode("\n", $_POST['cts_entity_types']));
        $entity_types = array_filter($entity_types, 'trim');
        update_option('cts_entity_types', $entity_types);
    }

    $entity_types = get_option('cts_entity_types', array());
    ?>
    <div class="wrap">
        <h1>Tipos de Entidad</h1>
        <form method="post">
            <textarea name="cts_entity_types" rows="10" cols="50"><?php echo esc_textarea(implode("\n", $entity_types)); ?></textarea>
            <p>Ingrese un tipo de entidad por línea.</p>
            <?php submit_button('Guardar Tipos de Entidad'); ?>
        </form>
    </div>
    <?php
}