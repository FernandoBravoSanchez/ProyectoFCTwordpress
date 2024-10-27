<?php
/*
Plugin Name : Reservas
Description: Un plugin para realizar reservas
Version: 1.0
Author : Fernando Bravo Sánchez
License : GPL2
*/

// Evitar el acceso directo
if (!defined('ABSPATH')){
	exit;
}

//Hook para iniciar el plugin
add_action('init','rp_inicializar_reservas_del_plugin');

function rp_inicializar_reservas_del_plugin(){

	// Aqui van las funciones básicas del plugin  
	
}

//Funcion al activar el plugin
function rp_activar_reservas_plugin(){
	//Crearemos las tablas
}
registrar_activacion_hook(__FILE__,'rp_activar_reservas_plugin');

//Función al desactivar el plugin
function rp_desactivar_reservas_plugin(){


	//Borramos los archivos temporales

}
registrar_desactivacion_hook(__FILE__,'rp_desactivar_reservas_plugin');

//_________________________________________________________________________________

// Creamos una funcion para mostrar el formulario 

function rp_mostrar_formulario_reservas() {
	ob_start(); 

	?>
	<form method="post" action="">
		<label for="nombre">Nombre : </label>
		<input type="text" id="nombre" name="nombre" required>

		
		<label for="email">Email :  </label>
		<input type="email" id="email" name="email" required>
		
		<label for="fecha">Fecha : </label>
		<input type="date" id="fecha" name="fecha" required>


		<label for="hora">Hora : </label>
		<input type="time" id="hora" name="hora" required>
	</form>
	<?php
	
	return ob_get_clean();
}

add_shortcode('reserva_formulario' , 'rp_mostrar_formulario_reservas');


// Procesar formulario php 

