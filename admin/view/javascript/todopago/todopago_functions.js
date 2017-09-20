//Credentials

$(document).ready(function () {
    $('#open').click(function () {
        $('#popup').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        //return false;
    });
    $('#confirm_test').click(function () {
        $.post("view/template/extension/todopago/todopago_credentials.php", {
            mail: $("#mail").val(),
            pass: $("#pass").val(),
            ambiente: "test"
        }, function (data) {
            $("#mail").val('');
            $("#pass").val('');
            json_data = JSON.parse(data);
            console.log(json_data);
            if (json_data.codigoResultado === 0) {
                $('input:text[name=payment_todopago_authorizationHTTPtest]').val(json_data.apikey);
                $('input:text[name=payment_todopago_idsitetest]').val(json_data.merchandid);
                $('input:text[name=payment_todopago_securitytest]').val(json_data.security);
            } else {
                alert(json_data.mensajeResultado);
            }
        });
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    $('#cancel').click(function () {
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    $('#borrar').click(function () {
        $('input:text[name=payment_todopago_authorizationHTTPtest]').val('');
        $('input:text[name=payment_todopago_idsitetest]').val('');
        $('input:text[name=payment_todopago_securitytest]').val('');
    });
});

$(document).ready(function () {
    $('#open_prod').click(function () {

        $('#popup_prod').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        //return false;
    });

    $('#confirm_prod').click(function () {
        $.post("view/template/extension/todopago/todopago_credentials.php", {
            mail: $("#mail_prod").val(),
            pass: $("#pass_prod").val(),
            ambiente: "prod"
        }, function (data) {
            $("#mail_prod").val('');
            $("#pass_prod").val('');
            json_data = JSON.parse(data);
            console.log(json_data);
            if (json_data.codigoResultado === 0) {
                $('input:text[name=payment_todopago_authorizationHTTPproduccion]').val(json_data.apikey);
                $('input:text[name=payment_todopago_idsiteproduccion]').val(json_data.merchandid);
                $('input:text[name=payment_todopago_securityproduccion]').val(json_data.security);
            } else {
                alert(json_data.mensajeResultado);
            }
        });

        $('#popup_prod').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    $('#cancel_prod').click(function () {
        $('#popup_prod').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    $('#borrar_prod').click(function () {
        $('input:text[name=payment_todopago_authorizationHTTPproduccion]').val('');
        $('input:text[name=payment_todopago_idsiteproduccion]').val('');
        $('input:text[name=payment_todopago_securityproduccion]').val('');
    });
});

$(document).ready(function () {
    if ($('#habilitar').attr('checked')) {
        $("#payment_todopago_maxinstallments").removeAttr("disabled");

    } else {
        $("#payment_todopago_maxinstallments").val("0");

    }
    $("#habilitar").click(function () {

        if ($('#habilitar').prop('checked')) {
            $("#payment_todopago_maxinstallments").removeAttr("disabled");

        } else {
            $("#payment_todopago_maxinstallments").prop('disabled', true);
            $("#payment_todopago_maxinstallments").val("");

        }
    });
    // CHECKBOX TIMEOUT FORM
    if ($('#payment_todopago_timeout_form_enabled').attr('checked')) {
        $("#payment_todopago_timeout_form").removeAttr("disabled");

    }
    $("#payment_todopago_timeout_form_enabled").click(function () {

        //alert( $('input:checkbox[name=payment_todopago_timeout_form_enabled]:checked').is(':checked') );

        if ($('#payment_todopago_timeout_form_enabled').prop('checked')) {
            $("#payment_todopago_timeout_form").removeAttr("disabled");

        } else {
            $("#payment_todopago_timeout_form").prop('disabled', true);
        }

    });
});
