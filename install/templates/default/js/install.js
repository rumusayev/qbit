/**
 * Created with JetBrains PhpStorm.
 * User: rumusayev
 * Date: 11/14/14
 * Time: 2:09 PM
 * To change this template use File | Settings | File Templates.
 */

$(function () {

    $('#installForm').validate({
        rules: {
            admin_login: {
                required: true
            },
            admin_password: {
                required: true
            },
            portal_email: {
                required: true,
                email: true
            },
            db_host: {
                required: true
            },
            db_name: {
                required: true
            },
            db_user: {
                required: true
            },
            db_pass: {
                required: true
            }
        }
    });

    $('.nextDBconfig').click(function () {

        var comp = 0;
        if ($('#portal_email').val().length <= 0) {
            $('#portal_email').parent('td').addClass('danger');
        } else {
            $('#portal_email').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if ($('#portal_name').val().length <= 0) {
            $('#portal_name').parent('td').addClass('danger');
        } else {
            $('#portal_name').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if ($('#portal_url').val().length <= 0) {
            $('#portal_url').parent('td').addClass('danger');
        } else {
            $('#portal_url').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if ($('#admin_login').val().length <= 0) {
            $('#admin_login').parent('td').addClass('danger');
        } else {
            $('#admin_login').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if ($('#admin_password').val().length <= 0) {
            $('#admin_password').parent('td').addClass('danger');
        } else {
            $('#admin_password').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if ($('#admin_email').val().length <= 0) {
            $('#admin_email').parent('td').addClass('danger');
        } else {
            $('#admin_email').parent('td').removeClass('danger');
            comp = comp + 1;
        }

        if (comp == 6) {
            $('.portalData').hide();
            $('.databaseData').show();
        }

    });

    $('.backPortalConfig').click(function () {

        $('.portalData').show();
        $('.databaseData').hide();

    });

    $('.savePortalData').click(function () {

        console.log( $('.error').length );

        var comp2 = 0;

        if ($('#db_host').val().length <= 0) {
            $('#db_host').parent('td').addClass('danger');
        } else {
            $('#db_host').parent('td').removeClass('danger');
            comp2 = comp2 + 1;
        }

        if ($('#db_name').val().length <= 0) {
            $('#db_name').parent('td').addClass('danger');
        } else {
            $('#db_name').parent('td').removeClass('danger');
            comp2 = comp2 + 1;
        }

        if ($('#db_user').val().length <= 0) {
            $('#db_user').parent('td').addClass('danger');
        } else {
            $('#db_user').parent('td').removeClass('danger');
            comp2 = comp2 + 1;
        }

        if ($('#db_pass').val().length <= 0) {
            $('#db_pass').parent('td').addClass('danger');
        } else {
            $('#db_pass').parent('td').removeClass('danger');
            comp2 = comp2 + 1;
        }

        if (comp2 == 4 && $('input.error').length == 0) {

            $('.statusRow').html('');
            $('.configData').hide();
            $('.savingConfig').show();

            var values = $('input:checkbox:checked.portal_langs').map(function () {

                if ((this.value).split("|")[0] != $('.portal_default_lang:checked').val().split("|")[0]) {
                    $('.portal_default_lang').val(this.value);
                }

            }).get();


            $.ajax({
                dataType: 'json',
                url: "install.php",
                method: "POST",
                data: $('#installForm').serialize(),
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(thrownError);
				},
                success: function (data) {
                    if (data['status'].indexOf('DB Error') > -1) {
                        responseTxt = "<p class='text-danger text-center bg-danger'>Database connection error: " + data['status'] + "</p>";
                        $('.statusRow').html(responseTxt);

                        $('.configData').show();
                        $('.savingConfig').hide();
                    } else if (data['status'].indexOf('Done') > -1) {
                        responseTxt = "<p class='text-info bg-info'>Status: " + data['status'] + "</p>"
                            + "<p class='bg-info'>Welcome</p>"
                            + "<p>Now you can enter your web-site <a href='" + $('#portal_url').val() + "'>" + $('#portal_url').val() + "</a></p>"
                            + "<p>Admin panel of web-site <a href='" + $('#portal_url').val() + "admin/'>" + $('#portal_url').val() + "admin/</a></p>"
                            + "<p class='text-danger'><b>Please remove installation directory !!!</b></p>";
                        $('.statusRow').html(responseTxt);
                        $('.savingConfig').hide();

                    }

                }
            });
        } else {
            responseTxt = "<p class='text-danger text-center bg-danger'>Please check the correctness of the filled information</p>";
            $('.statusRow').html(responseTxt);
        }
    });

    var templatesWithDemoData = ["master"];


    if (!$.inArray($('.template_name').val(), templatesWithDemoData)){
        $('.add_demo_data').attr("disabled", false);
    } else {
        $('.add_demo_data').attr("disabled", true).attr('checked', false);;
    }

    $('.template_name').change(function() {

        if (!$.inArray($(this).val(), templatesWithDemoData)){
            $('.add_demo_data').attr("disabled", false);
        } else {
            $('.add_demo_data').attr("disabled", true).attr('checked', false);;
        }
    });

});