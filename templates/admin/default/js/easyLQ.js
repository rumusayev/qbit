$(function () {

    $('body').on('click', '.addLQbtn', function () {
        $('#insertLQModal').modal();
        $('#insertLQModal').find('.lqRow').attr('rel', $(this).attr('rel'));
        $('#insertLQModal').find('.lqRowConstants').attr('rel', $(this).attr('rel'));
    });

    $('body').on('click', '.getLQ', function () {

        if ($(this).attr('rel') == 'container') {
            var newContainerLQ = '[[container name=' + $('#containerName').val() + ']]';
            $('.lqRow').html(newContainerLQ);
        } else if ($(this).attr('rel') == 'translation') {
            var newTranslationLQ = '[[translation name=' + $('#translationName').val() + ']]';
            $('.lqRow').html(newTranslationLQ);
        } else if ($(this).attr('rel') == 'constant') {
            var newConstantLQ = '[[$' + $('#constantName').val() + ']]';
            $('.lqRow').html(newConstantLQ);
        } else if ($(this).attr('rel') == 'parameter') {
            var newParameterLQ = '[[@' + $('#parameterName').val() + ']]';
            $('.lqRow').html(newParameterLQ);
        } else if ($(this).attr('rel') == 'module') {

            if ($('input[name=moduleType]:checked').val() == 'catalogs'){
                moduleType = $('input[name=moduleType]:checked').val();
                moduleName = $('.moduleName option:selected').val();
                designName = $('.designName option:selected').val();
                materialDesignName = $('.materialDesignName option:selected').val();
                moduleAction = $('.actionModule').val();
                moduleContainerName = $('.containerName').val();
                navigation = $('.countNavigation').val();
                navigationDesignName = $('.navigationDesignName option:selected').val();


                if (materialDesignName != '----' ){
                    materialDesign = " material_design=" + materialDesignName;
                } else {
                    materialDesign = "";
                }
                if (navigationDesignName != '----' ){
                    if (navigation!=''){
                        navigationCount = " navigation=" + navigation;
                    } else {
                        navigationCount = " navigation=10";
                    }
                    navigationDesign = navigationCount + " navigation_design=" + navigationDesignName;
                } else {
                    navigationDesign = "";
                }
                if (moduleAction == "getCatalogItem"){
                    id = " id=" + $('.moduleName option:selected').attr('alt');
                } else if (moduleAction == "getCatalog" && $('.catalogItems :selected').length!=0) {
                    var ids = [];
                    $('.catalogItems :selected').each(function(i, selected){
                        ids[i] = $(selected).val();
                    });
                    id = " id=" + ids.join(',');
                }
                else {
                    id = "";
                }
                if ($('.containerIsExist').is(':checked')){
                    lqRowText = "[[module type=" + moduleType + " name=" + moduleName + " action=" + moduleAction + " container=" + moduleContainerName + id + " design=" + designName + materialDesign + navigationDesign + "]] [[container name=" + moduleContainerName + "]]";
                } else {
                    lqRowText = "[[module type=" + moduleType + " name=" + moduleName + " action=" + moduleAction + " container=" + moduleContainerName + id + " design=" + designName + materialDesign + navigationDesign + "]]";
                }

            } else if ($('input[name=moduleType]:checked').val() == 'contents'){
                moduleType = $('input[name=moduleType]:checked').val();
                moduleName = $('.moduleName option:selected').val();
                moduleAction = $('.actionModule').val();
                moduleContainerName = $('.containerName').val();

                if ($('.containerIsExist').is(':checked')){
                    lqRowText = "[[module type=" + moduleType + " name=" + moduleName + " action=" + moduleAction + " container=" + moduleContainerName + "]] [[container name=" + moduleContainerName + "]]";
                } else {
                    lqRowText = "[[module type=" + moduleType + " name=" + moduleName + " action=" + moduleAction + " container=" + moduleContainerName + "]]";
                }

            }
            $('.lqRow').html(lqRowText);
        }
    });

    $('body').on('click', '.lqRow', function () {

        if ($('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').hasClass('ckeditor_w')) {

            ckeditorInstance = $('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').attr('id');
            CKEDITOR.instances[ckeditorInstance].insertText($(this).html());

        } else {
            $('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').val($('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').val() + $(this).html());
        }
        $('#insertLQModal').modal('hide');
        $('.lqRow').html('');
        return false;
    });

    $('body').on('click', '.lqRowConstants', function () {

        if ($('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').hasClass('ckeditor_w')) {

            ckeditorInstance = $('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').attr('id');
            CKEDITOR.instances[ckeditorInstance].insertText($(this).html());

        } else {
            $('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').val($('#' + name + '-crud_edit_modal [name*="' + $(this).attr('rel') + '"]').val() + $(this).html());
        }
        $('#insertLQModal').modal('hide');
        $('.lqRow').html('');
        return false;
    });

    $('body').on('change', '.moduleType', function () {

        moduleNamesList = '<option>----</option>';

        if ($(this).val() == 'contents') {

            $('.designName').parent().hide();
            $('.catalogItems').parent().hide();
            $('.materialDesignName').parent().hide();
            $('.navigationDesignName').parent().parent().hide();
            $('.actionModule').val('getContent');

            $.ajax({
                url: portal_url + 'contents',
                method: 'GET',
                dataType: 'json',
                success: function (json) {
                    $.each(json, function (key, data) {
                        moduleNamesList = moduleNamesList + '<option value="' + data['content_name'] + '">' + data['content_name'] + '</option>';
                    });

                    $('.moduleName').html(moduleNamesList).parent().show();
                }
            });
        } else if ($(this).val() == 'catalogs') {

            $('.actionModule').val('');
            $.ajax({
                url: portal_url + 'catalogs',
                method: 'GET',
                dataType: 'json',
                success: function (json) {
                    $.each(json, function (key, data) {
                        if (data['is_category'] == 1) {
                            is_category = 'CATEGORY: ';
                        } else {
                            is_category = 'ITEM: '
                        }
                        moduleNamesList = moduleNamesList + '<option alt="' + data['id'] + '" rel="' + data['is_category'] + '" value="' + data['catalog_name'] + '">' + is_category + data['catalog_title'] + '</option>';
                    });

                    $('.moduleName').html(moduleNamesList).parent().show();
                }
            });
        }

    });

    $('body').on('change', '.moduleName', function () {

        if ($('.moduleName option:selected').attr('rel') == 1) {
            $('.actionModule').val('getCatalog');
        } else if ($('.moduleName option:selected').attr('rel') == 0) {
            $('.actionModule').val('getCatalogItem');
            $('.catalogItems').parent().hide();
        } else {
        }

        $('.containerName').parent().show();

        designNamesList = '<option>----</option>';
        materialDesignNamesList = '<option>----</option>';

        if ($('.actionModule').val() == 'getCatalog' || $('.actionModule').val() == 'getCatalogItem') {

            $('.countNavigation').parent().parent().show();
            $('.countNavigation').parent().parent().show();

            if ($('.actionModule').val() == 'getCatalog'){
                catalogItems = '';
                $.ajax({
                    url: portal_url + 'catalogs',
                    method: 'GET',
                    dataType: 'json',
                    data: 'parent_id=1',
                    success: function (json) {
                        $.each(json, function (key, data) {
                            catalogItems = catalogItems + '<option value="' + data['id'] + '">' + data['catalog_title'] + '</option>';
                        });

                        $('.catalogItems').html(catalogItems).parent().show();
                    }
                });
            }

            $.ajax({
                url: portal_url + 'designs',
                method: 'GET',
                dataType: 'json',
                success: function (json) {
                    $.each(json, function (key, data) {

                        designNamesList = designNamesList + '<option rel="' + data['design_name'] + '" value="' + data['design_name'] + '">' + data['design_name'] + '</option>';
                    });

                    $('.designName').html(designNamesList).parent().show();
                    $('.navigationDesignName').html(designNamesList).parent().show();
                }
            });

            $.ajax({
                url: portal_url + 'designs',
                method: 'GET',
                dataType: 'json',
                success: function (json) {
                    $.each(json, function (key, data) {

                        materialDesignNamesList = materialDesignNamesList + '<option rel="' + data['design_name'] + '" value="' + data['design_name'] + '">' + data['design_name'] + '</option>';
                    });

                    $('.materialDesignName').html(materialDesignNamesList).parent().show();
                }
            });
        }


    });



});