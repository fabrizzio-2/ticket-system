jQuery(document).ready(function($) {
    $('#ticket_telefono').mask('+00000000000');
    $('#ticket_unidades').select2({
        placeholder: "Seleccione las unidades",
        allowClear: true
    });
});