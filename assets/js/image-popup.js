jQuery(document).ready(function($) {
    console.log("image-popup.js carregado. jQuery está disponível.");

    // Verifica se wp.media está definido
    if (typeof wp !== "undefined" && typeof wp.media !== "undefined") {
        console.log("wp.media está disponível.");
    } else {
        console.log("wp.media NÃO está disponível. Verifique se wp_enqueue_media() está sendo chamado.");
    }

    var mediaUploader;

    $(".ppp_upload_image_button").on("click", function(e) {
        e.preventDefault();

        // Se o uploader já existe, abra-o novamente.
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Crie o uploader de mídia do WordPress.
        mediaUploader = wp.media({
            title: "Selecionar Imagem do Popup",
            button: {
                text: "Usar esta imagem"
            },
            multiple: false // Apenas uma imagem pode ser selecionada
        });

        // Quando uma imagem é selecionada, defina o valor do campo oculto e exiba a pré-visualização.
        mediaUploader.on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            $("#ppp_image_id").val(attachment.id);
            $("#ppp_image_tag").attr("src", attachment.url).show(); // Atualiza o src da tag img e mostra
            $(".ppp_remove_image_button").show();
        });

        // Abra o uploader.
        mediaUploader.open();
    });

    // Remover imagem
    $(".ppp_remove_image_button").on("click", function(e) {
        e.preventDefault();
        console.log("Botão 'Remover Imagem' clicado."); // Log para depuração
        $("#ppp_image_id").val("");
        $("#ppp_image_tag").attr("src", "").hide(); // Limpa o src da tag img e esconde
        $(this).hide();
    });
});