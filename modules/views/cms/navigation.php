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
				<li class="dropdown">
					<a href="<?php echo Backstage::gi()->portal_url; ?>cms/pages/">Pages</a>
				</li>				
				<li class="dropdown">
					<a href="<?php echo Backstage::gi()->portal_url; ?>cms/contents/">Contents</a>
				</li>
				<li class="dropdown">
					<a href="#dummy" class="dropdown-toggle" data-toggle="dropdown">Catalogs <b class="caret"></b></a>
					<ul class="dropdown-menu">
					<?php
						foreach ($catalogs as $catalog)
							if (Pretorian::gi()->check('catalogs', 'POST', $catalog->id))
								echo '<li class="dropdown"><a href="'.Backstage::gi()->portal_url.'cms/catalogs?id='.$catalog->id.'">'.$catalog->catalog_title.'</a></li>';
					?>
					</ul>
				</li>
                <li class="dropdown"><a href="#dummy">|</a></li>
                <li class="dropdown"><a href="#dummy" id="logout">Logout</a></li>				
			</ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
