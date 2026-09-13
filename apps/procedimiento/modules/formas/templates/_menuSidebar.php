<header class="logo-env">

     <!-- logo -->
     <div class="logo">
          <a href="<?php print $base_path; ?>backend.php/resumen">
               <img class="img-circle" style="border-radius: unset;" src="<?php print $path_theme;?>assets/images/simad/logo-app-compact.png" width="88px" height="32px"/>
          </a>
     </div>

     <!-- logo collapse icon -->
     <div class="sidebar-collapse">
          <a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
               <i class="entypo-menu"></i>
          </a>
     </div>

                         
     <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
     <div class="sidebar-mobile-menu visible-xs">
          <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
               <i class="entypo-menu"></i>
          </a>
     </div>

</header>

<ul id="main-menu" class="main-menu">
     <!-- add class "multiple-expanded" to allow multiple submenus to open -->
     <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
     <li>
          <a data-toggle="tooltip" data-original-title="Cerrar detalles del registro" href="#" onclick="javascript:parent.jQuery.ReloadAndCloseModalSIMAD();">
               <i class="fa fa-times-circle"></i>
               <span class="title">Cerrar</span>
          </a>
     </li>



     <?php 
     if($procedimiento->getEsUltimaVersion()=="1")
     { 
          ?>   
          <li>
               <?php  
               echo link_to(
                    '<i class="entypo-back-in-time" id="pr_alineo_03"></i> <span class="title">Historial</span>',
                    'formas/historial?procedimiento_id='.$procedimiento->getPrimaryKey(),
                    array("data-toggle" => "tooltip", "data-original-title" => "Ir al historial")
               );
               ?>
          </li>
          <?php 
     }
     ?>                 

     <?php 
     if($procedimiento->getEsUltimaVersion()=="1")
     {
          if($sf_user->checkPerm($currentFormAnular, $currentUser))
          {   
               ?>   
               <li>
                    <?php  
                    echo jq_link_to_function(
                         '<i class="entypo-cancel-squared" id="pr_alineo_03"></i> <span class="title">Anular</span>',
                         'javascript:jQuery.OpenModalSIMAD("'.$base_path.'/procedimiento.php/formas/delete?procedimiento_id='.$procedimiento->getPrimaryKey().'", "480", "320")',array("data-toggle" => "tooltip", "data-original-title" => "Anular formato o procedimiento")
                         );
                    ?>
               </li>
               <?php   
          }
     }
     ?>

     <?php 
     if($procedimiento->getEsUltimaVersion()=="1")
     {
          if($sf_user->checkPerm($currentFormNueva, $currentUser))
          {  

               ?>   
               <li>
                    <?php  
                    echo link_to(
                         '<i class="fa fa-star" id="pr_alineo_02"></i> <span class="title">Nueva Versión</span>',
                         'formas/crear?procedimiento_id='.$procedimiento->getPrimaryKey(),
                         array("data-toggle" => "tooltip", "data-original-title" => "Crear Una Nueva Version De Este Formato")
                    );
                    ?>
               </li>
               <?php  
          }
     }
     ?>

     <?php 
     if($procedimiento->getEsUltimaVersion()=="1")
     {
          if($sf_user->checkPerm($currentFormEditar, $currentUser))
          { 
               ?>   
               <li>
                    <?php  
                    echo link_to(
                         '<i class="fa fa-edit"></i> <span class="title">Editar</span>',
                         'formas/edit?procedimiento_id='.$procedimiento->getPrimaryKey(),
                         array("data-toggle" => "tooltip", "data-original-title" => "Editar Este Formato")
                    );
                    ?>
               </li>
               <?php  
 
          }
     }
     ?> 
</ul>