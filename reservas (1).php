<?php
/*
Plugin Name: Reservas
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
	//Crearemos las tablas para guardar y gestionara las reservas
	global $wpdb;
	$tabla_reservas = $wpdb->prefix . 'reservas';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $tabla_reservas (
		id mediumint(9) NOT NULL AUTO_INCREMENT , 
		nombre tinytext NOT NULL ,
		email varchar(100) NOT NULL ,
		fecha date NOT NULL , 
		hora time NOT NULL , 
		PRIMARY KEY (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
}
register_activation_hook(__FILE__,'rp_activar_reservas_plugin');

//Función al desactivar el plugin
function rp_desactivar_reservas_plugin(){


	//Borramos los archivos temporales

}
register_deactivation_hook(__FILE__,'rp_desactivar_reservas_plugin');

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

		<input type="submit" value="Reservar">
	</form>
	<?php
	
	return ob_get_clean();
}

add_shortcode('reserva_formulario' , 'rp_mostrar_formulario_reservas');


// Procesar formulario php 

function rp_procesar_formulario_reserva(){

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['fecha']) && isset($_POST['hora'])) {

		$nombre = sanitize_text_field($_POST['nombre']);
		$email = sanitize_email($_POST['email']);
		$fecha = sanitize_text_field($_POST['fecha']);
		$hora = sanitize_text_field($_POST['hora']);

		// Aquí podemos almaacenar  la reserva en la base de datos
		global $wpdb;
		$tabla_reservas = $wpdb->prefix . 'reservas';
		$wpdb->insert($tabla_reservas, array(
			'nombre' => $nombre,
			'email' => $email,
			'fecha' => $fecha,
			'hora' => $hora
		));

		//enviar correo de confirmación al usuario
		$asunto = 'Confirmación de reserva';
		$mensaje = "Hola $nombre , tu reserva se realizó para la fecha del $fecha a las $hora";
		wp_mail($email,$asunto,$mensaje);


		//Notificar al administrador
		$admin_email = get_option('admin_email');
		$asunto_admin = 'Nueva Reserva';
		$mensaje_admin = " Hola , se ha realizado una  nueva reserva a nombre de : $nombre , a fecha : $fecha";
		wp_mail($admin_email,$asunto_admin,$mensaje_admin);

		echo '<p> ¡Reserva Realizada!</p>';

	}

}

add_action('init', 'rp_procesar_formulario_reserva');

// Agregar un menú en el panel de administración
add_action('admin_menu', 'rp_agregar_menu_reservas');

function rp_agregar_menu_reservas() {
	add_menu_page(
		'Gestión de Reservas', 	//Titulo de la página
		'Reservas FCT',				//Nombres del menú
		'manage_options',
		'reservas-plugin',
		'rp_mostrar_reservas',
		'dashicons-calendar-alt',
		6
	);

}
//Mostrar las reservas en el panel de administración
function rp_mostrar_reservas() {
	global $wpdb;
	$tabla_reservas = $wpdb->prefix . 'reservas';
	$reservas = $wpdb->get_results("SELECT * FROM $tabla_reservas");

	echo '<div class="wrap">';
	echo '<h1> Reservas Realizadas</h1>';
	echo '<table class="wp-list-table widefat fixed striped">';
	echo '<thread><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Hora</th><th>Acciones</th></tr></thread>';
	echo '<tbody>';

	if (!empty($reservas)){
		foreach ($reservas as $reserva){
			echo '<tr>';
			echo '<td>' . esc_html($reserva->id) . '</td>';
			echo '<td>' . esc_html($reserva->nombre) . '</td>';
			echo '<td>' . esc_html($reserva->email) . '</td>';
			echo '<td>' . esc_html($reserva->fecha) . '</td>';
			echo '<td>' . esc_html($reserva->hora) . '</td>';
			// echo '<td><a href=?'
			echo '</tr>';

		}
	} else {
		echo '<tr><td colspan="6">No hay reservas registradas.</td></tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';

}

?>