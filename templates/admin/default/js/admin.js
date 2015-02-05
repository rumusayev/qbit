/**
 * Created by rumusayev on 2/3/2015.
 */

$(function () {

    function validateURL(textval) {
        var urlregex = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
        return urlregex.test(textval);
    }

    $('#portal_url').bind('blur keydown', function () {
        var url = $("#portal_url").val();
        if (validateURL(url)) {
            $("#portal_url").removeAttr("style").attr('rel', 'valid');
            $(".savePortalData").removeAttr('disabled');
        } else {
            $("#portal_url").css("border", "2px solid red").attr('rel', 'invalid');
            $(".savePortalData").attr('disabled', 'disabled');
        }

        if ($('[rel=invalid]').size() > 0) {
            $(".savePortalData").attr('disabled', 'disabled');
        } else {
            $(".savePortalData").removeAttr('disabled');
        }
    });


    $('.portal_default_lang').on('change', function () {

        value = $(".portal_default_lang:checked").val();
        $('.portal_langs').each(function () {
            $(this).attr("disabled", false);
        });
        $(".portal_langs[value*='" + value + "']").removeAttr('checked').prop('checked', true).attr('disabled', 'disabled');

    });

    $('.portal_langs').on('change', function () {

        value = $(".portal_default_lang:checked").val();
        if ($(this).val() == value) {
            alert('This is language default. You cant change it');
            $(this).removeAttr('checked').prop('checked', true).attr('disabled', 'disabled');
        }
    });

    $('#db_host, #db_name, #db_user, #db_pass').bind('blur', function ()          //whenever you click off an input element
    {
        if ($(this).val().length != 0) {
            $(this).removeAttr("style").attr('rel', 'valid');
        } else {
            $(this).css("border", "2px solid red").attr('rel', 'invalid');
        }

        if ($('[rel=invalid]').size() > 0) {
            $(".savePortalData").attr('disabled', 'disabled');
        } else {
            $(".savePortalData").removeAttr('disabled');
        }
    });

    $('#changeConfigForm').submit(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 100);
        $('.statusRow').removeClass('alert alert-danger alert-info').html('');

        var loading = setInterval(function(){
            $('.statusRow').append('<span style="color:#339bb9" class="glyphicon glyphicon-chevron-right" aria-hidden="true">');
            console.log(1);
        }, 200)

        portal_langs = '';
        $('.portal_langs').each(function(){
            if($(this).is(':checked')){
                portal_langs = portal_langs + $(this).val().split('|')[0] + ',';
            }
        });

        $.ajax({
            dataType: 'json',
            method: 'POST',
            data: $(this).serialize() + '&portal_all_langs=' + portal_langs,
            url: portal_url + 'admin/siteConfigs/',
            success: function (data) {

                clearInterval(loading);

                if (data['status']==true){

                    $('.statusRow').removeClass('alert alert-danger');
                    $('.statusRow').html('<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Configurations successfully changed. Refreshing ').addClass('alert alert-info');
                    setInterval(function(){
                        $('.statusRow').append('<b>.</b>').prepend('<b>.</b>');
                    },200);
                    setTimeout(function(){
                        location.reload();
                    }, 4000)

                } else if (data['status']==false){
                    $('.statusRow').removeClass('alert alert-info');
                    $('.statusRow').html('<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Configurations successfully changed').addClass('alert alert-danger');
                } else if (data['status']['mysql']==false){
                    $('.statusRow').removeClass('alert alert-info');
                    $('.statusRow').html('<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Database connection error. Please check all fields').addClass('alert alert-danger');
                }
            }
        });

        return false;
    });
});