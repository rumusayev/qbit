<?php
if (!!array_diff(array('add','edit'), $crud_data['restrictions']))
{
	require_once(Backstage::gi()->VIEWS_DIR.'common/validator.php');
	require_once(Backstage::gi()->VIEWS_DIR.'common/plugins.php'); 
}
$name = $crud_params['name'];
?>

<script type="text/javascript" charset="utf-8">
$(function()
{
    <?php echo $name;?>_load();
});
function <?php echo $name;?>_load()
{
    $('#<?php echo $name;?>-crud_table').addClass('loading');
    $.ajax(
    {
        url:"<?php echo Backstage::gi()->portal_url;?>crud/load/",
        type: "POST",
        data: {
				crud_data: '<?php echo base64_encode(json_encode($crud_data));?>',
                crud_params_form: JSON.stringify($('#<?php echo $name;?>-crud_params_form').serializeJSON()), 
                crud_search_form: JSON.stringify($('#<?php echo $name;?>-crud_search_form').serializeJSON())
        },
        success:function(data)
        {
            $('#<?php echo $name;?>-crud_table').html(data);
            $('#<?php echo $name;?>-crud_table').removeClass('loading');
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}
</script>
<form name="<?php echo $name;?>-crud_params_form" id="<?php echo $name;?>-crud_params_form" method="post">
    <input type="hidden" name="name" id="name" value="<?php echo $name;?>"/>
    <input type="hidden" name="crud_current_page" id="crud_current_page" value="<?php echo $crud_params['crud_current_page'];?>"/>
    <input type="hidden" name="crud_count_per_page" id="crud_count_per_page" value="<?php echo $crud_params['crud_count_per_page'];?>"/>
</form>
<div id="<?php echo $name;?>-crud_table"></div>