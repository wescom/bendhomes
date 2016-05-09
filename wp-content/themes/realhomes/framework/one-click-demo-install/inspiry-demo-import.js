jQuery(document).ready(function($) {

    "use strict";

    if ( jQuery().validate && jQuery().ajaxSubmit ) {

        var importButton = $( '#import-demo'),
            importAjaxLoader = $( '#import-loader' ),
            importMessage = $( '#import-message'),
            importError = $( "#import-error" );

        var importOptions = {
            beforeSubmit: function () {
                importMessage.fadeOut( 50 );
                importError.fadeOut( 50 );
                importButton.attr('disabled', 'disabled');
                importAjaxLoader.fadeIn( 200 );
            },
            success: function (ajax_response, statusText, xhr, $form) {
                console.log( statusText );
                importAjaxLoader.fadeOut( 100 );
                importButton.removeAttr('disabled');
                if ( 'success' == statusText ) {
                    importMessage.html( ajax_response ).fadeIn( 200 );
                } else {
                    importError.html( ajax_response ).fadeIn( 200 );
                }
            }
        };

        $('#demo-import-form').validate({
            submitHandler: function ( form ) {
                $(form).ajaxSubmit( importOptions );
            }
        });

    }
});