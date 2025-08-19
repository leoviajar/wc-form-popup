jQuery(document).ready(function($) {
    console.log('admin-js carregado!')
    function toggleCustomWidthField() {
        if ($("#ppp_image_size_type").val() === "custom") {
            $("#ppp_custom_width_field").show();
        } else {
            $("#ppp_custom_width_field").hide();
        }
    }

    toggleCustomWidthField();

    $("#ppp_image_size_type").change(function() {
        toggleCustomWidthField();
    });
});
