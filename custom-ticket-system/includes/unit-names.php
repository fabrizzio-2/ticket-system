<?php
function cts_add_unit_names_menu() {
    add_submenu_page(
        'edit.php?post_type=ticket',
        'Nombres de Unidad',
        'Nombres de Unidad',
        'manage_options',
        'cts-unit-names',
        'cts_unit_names_page'
    );
}
add_action('admin_menu', 'cts_add_unit_names_menu');

function cts_unit_names_page() {
    if (isset($_POST['cts_unit_names'])) {
        $unit_names = array_map('sanitize_text_field', explode("\n", $_POST['cts_unit_names']));
        $unit_names = array_filter($unit_names, 'trim');
        update_option('cts_unit_names', $unit_names);
    }

    $unit_names = get_option('cts_unit_names', array());
    ?>
    <div class="wrap">
        <h1>Nombres de Unidad</h1>
        <form method="post">
            <textarea name="cts_unit_names" rows="10" cols="50"><?php echo esc_textarea(implode("\n", $unit_names)); ?></textarea>
            <p>Ingrese un nombre de unidad por línea.</p>
            <?php submit_button('Guardar Nombres de Unidad'); ?>
        </form>
    </div>
    <?php
}