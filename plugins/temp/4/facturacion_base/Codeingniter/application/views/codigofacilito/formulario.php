<?php echo form_open("/codigofacilito/recibirdatos");?>

<?php

	$nombre =$arrayName = array('name' => 'nombre',
	'placeholder' => 'Escribe tu nombre');


	$videos =$arrayName = array('name' => 'videos',
	'placeholder' => 'Cantidad de videos del curso');
?>
<html>
<body>

<?php echo form_label('nombre','nombre'); ?>
<?php echo form_input($nombre) ?>
<br>
<?php echo form_label('numero de videos','videos'); ?>
<?php echo form_input($videos) ?>


<?php echo form_submit('','Subir archivo'); ?>
<?php echo form_close();?>




</body>
</html>