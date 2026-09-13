<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.style1 {
	font-family: "Trebuchet MS";
	font-size: 8px;
	color: #990000;
	text-align: center;
}
body {
	margin-left: 0px;
	margin-top: 00px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-image: url();
	background-repeat: no-repeat;
	background-color: #F3F3F3;
}
.style2 {
	font-family: "Trebuchet MS";
	font-size: 9.5px;
	text-align: center;
}
.style3 {
	font-family: "Trebuchet MS";
	font-size: 8px;
	color: #990000;
	text-align: center;
}
-->
</style>
</head>
<body>
<table width="150" height="80" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="50" align="center"  class="style2">
	<img src="<?php echo $usuario->getRutaFoto() ?>" width="50" height="50" />
	</td>
    <td align="center" valign="top" nowrap="nowrap" class="style2">
      <span class="style1" align="center"><?php echo $usuario->getUserName()?><br>
    <img src="<?php echo $licencia_image; ?>" width="70" height="42" /><br>
	<?php if($usuario->getEstadousuarioId() == 3){ ?> 
		 <font color="red" ><?php echo 'Cta. Redireccionada'; ?>		 
		 </font>
	<?php }?>        
	</span>	
	</td>				      
  </tr>  
</table>

