<?php
	$path_theme = substr(sfConfig::get('theme_simad'), -1) == "/" ? sfConfig::get('theme_simad') : sfConfig::get('theme_simad').'/';
	$ruta_base = substr(sfConfig::get('base_simad'), -1) == "/" ? sfConfig::get('base_simad') : sfConfig::get('base_simad').'/';
?>
<!-- dropdowns -->
<li class="dropdown profile-info">
	
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<i class="entypo-help"></i>
		<!--span class="badge badge-info">6</span-->
	</a>
	
	<!-- dropdown menu (tasks) -->
	<ul class="dropdown-menu">
		<li class="top"><p>Nuestro Centro de Ayuda</p></li>

		<li>
			<ul class="dropdown-menu-list scroller">
				<li>
					<a target="_blank" href="<?php echo $ruta_base; ?>ayuda/ManualArchiDHu.pdf">
                        <i class="entypo-gauge"></i>
					    &nbsp;<span>Manual de Ayuda</span>
					</a>
				</li>
			</ul>            
		</li>
	</ul>
</li>