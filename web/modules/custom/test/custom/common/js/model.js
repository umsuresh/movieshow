(function ($, Drupal) {
    'use strict';

    $(document).ready(function() {
        $('#edit-field-designation').on('change', function() {
            var selectedText = $(this).find("option:selected").text();
            if (selectedText == "Director") {
                $( "#edit-field-sub-categories" ).prop( "disabled", true );
                $( "#edit-field-division" ).prop( "disabled", true );
            } else {
                $( "#edit-field-sub-categories" ).prop( "disabled", false );
                $( "#edit-field-division" ).prop( "disabled", false );
            }
        });

        var designation = $('#node-message-edit-form #edit-field-designation').find("option:selected").text();
        if (designation == "Director") {
            $( "#edit-field-sub-categories" ).prop( "disabled", true );
            $( "#edit-field-division" ).prop( "disabled", true );
        } else {
            $( "#edit-field-sub-categories" ).prop( "disabled", false );
            $( "#edit-field-division" ).prop( "disabled", false );
        }

        $("#user-login-form #edit-submit").prop("disabled",true); 
        $('#edit-legal-accept').on('change', function() {
            let isChecked = $('#edit-legal-accept')[0].checked;
            if(isChecked !== true) {
                $("#user-login-form #edit-submit").prop("disabled", true); 
            }
            else
            {
                $("#user-login-form #edit-submit").prop("disabled",false); 
            }
        });
    });

})(jQuery, Drupal);

//window.location.href.indexOf("franky") != -1

