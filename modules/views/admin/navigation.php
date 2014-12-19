<script type="text/javascript" charset="utf-8">
$(function()
{
    $('#logout').click(function(e)
    {   
		$.ajax(
		{
            url: "<?php echo Backstage::gi()->portal_url;?>auth/logout/",
			type: "GET",
			success:function(data, status, jqxhr)
			{
				$.cookie("AUTH", '', {expires: 2, path: '/'});
				location.reload(false);			
			},
			error: function (request, status, error) 
			{
				console.log(request.responseText);
			}                        
		}); 
		e.preventDefault();
    });
});
</script>

<div class="navbar navbar-default" role="navigation">
    <div class="container">
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/layouts/">Layouts</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/pages/">Pages</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/contents/">Contents</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/catalogs/">Catalogs</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/designs/">Designs</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/forms/">Forms</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/users/">Users</a></li>
                <li class="dropdown"><a href="<?php echo Backstage::gi()->portal_url; ?>admin/grants/">Grants</a></li>
                <li class="dropdown"><a href="#dummy">|</a></li>
                <li class="dropdown"><a href="#dummy" id="logout">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
