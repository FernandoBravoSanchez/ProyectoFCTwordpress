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
	exit('No puedes acceder a este archivo directamente');
}

//Hook para iniciar el plugin
add_action('init','rp_inicializar_reservas_del_plugin');

function rp_inicializar_reservas_del_plugin(){

	

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

	global $wpdb;
	$tabla_reservas = $wpdb->prefix . 'reservas';

	// Eliminar tabla 
	$wpdb->query("DROP TABLE IF EXISTS $tabla_reservas");

	//Eliminar archivo log 

	$ARCHIVO_LOG = plugin_dir_path(__FILE__) . 'reservas-admin.log';

	if(file_exists($ARCHIVO_LOG)){
		unlink($ARCHIVO_LOG); 
	}



}
register_deactivation_hook(__FILE__,'rp_desactivar_reservas_plugin');

//_________________________________________________________________________________

// Creamos una funcion para mostrar el formulario

function rp_mostrar_formulario_reservas() {
	ob_start();


	global $wpdb; // Acceder base de datos 
	$fecha_actual = date('Y-m-d'); // Fecha de hoy
	$fecha_maxima = date('Y-m-d', strtotime("+ 1 week")); //fecha de 7 dias posteriores 

	

	// horas cada 15 minutos

	$intervalos_hora = [
		'13:00' , '13:15' , '13:30' , '13:45' , '14:00' , '14:15' , '14:30' , '14:45' , '15:00' , '15:15' , '15:30' ,
		'20:00' , '20:15' , '20:30' , '20:45' , '21:00' , '21:15' , '21:30' , '22:00' , '22:15' , '22:30'];

	// Obtenemos la fecha seleccionada por el usuario o si no elige le ponemos la fecha actual
	$fecha_adjudicada = isset($_POST['fecha']) ? sanitize_text_field($_POST['fecha']): $fecha_actual;
	// Comprobamos las horas en la base de datos
	$tabla_reservas = $wpdb->prefix . 'reservas';
	$horas_reservadas = $wpdb->get_col($wpdb->prepare("SELECT hora FROM $tabla_reservas WHERE fecha = %s" , $fecha_adjudicada));

	?>
	<style>
		.reserva-contenedor {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 80vh;
			background-color: #fff

		}
		.reserva-formulario{
			background-color: #fff;
			padding: 30px;
			border-radius: 8px;
			max-width: 400px;
			width: 100%;

		}
		.reserva-formulario h1 {
			text-align: center;
			margin: 0px 0 12vh;

			color: #333;
		}
		.reserva-formulario label {
			display: block;
			margin-top : 2vh;
			font-weight: bold;
			color:#000000;
		}

		.reserva-formulario input[type="text"],
		.reserva-formulario input[type="email"],
		.reserva-formulario input[type="date"],
		.reserva-formulario input[type="time"] {
			width: 100%;
			padding: 10px;
			margin-bottom: 30 px;
			border: 1px solid #ccc;
			border-radius: 5px;
		}

		.reserva-formulario input[type="submit"]{
			width:100%;
			background-color: #000000;
			color: white;
			border: none;
			padding: 12px;
			font-size: 16px;
			border-radius: 5px;
			margin-top: 20px;
		}
		.reserva-formulario input[type="submit"]:hover {
			background-color: #005983;
		}
		.reserva-mensaje{
			display:block;
			padding: 20px 40px;
			border-radius: 8px;
			text-align:center;
			font-size: 18px;
			font-weight: bold;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
			color: white;
		}
		.reserva-formulario .form-image {
			text-align:center; 
			margin-bottom: 80px;
		}
		.reserva-exito {
			background-color: green;
			
		}

		.reserva-error {
			background-color:red;
		}
		
		.footer-text {
			text-align:center;
			margin-top:20px;
			font-size:12px;
			color: #888;
		}
	</style>

<div class="reserva-contenedor">
	<form method="post" action="" class="reserva-formulario">
		<div class="form-image">
			<img src="https://www.shutterstock.com/image-vector/restaurant-logo-template-260nw-1254530365.jpg" />
		</div>
		<label for="nombre">Nombre : </label>
		<input type="text" id="nombre" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+"  title= " Solo se permiten nombres" name="nombre" required>


		<label for="email">Email : </label>
		<input type="email" id="email" name="email" required>
		<br>
		<label for="fecha">Fecha : </label>
		<input type="date" id="fecha" name="fecha" min="<?php echo $fecha_actual; ?>" max="<?php echo $fecha_maxima; ?>" value="<?php echo $fecha_adjudicada; ?>" required onchange="this.form.submit()">


		<label for="hora">Hora : </label>
		<select id="hora" name="hora" required>
			<option value="" disabled selected> Selecciona una hora </option>
			<?php foreach ($intervalos_hora as $hora): ?>
				<?php if (!in_array($hora, $horas_reservadas)): ?>
				<option value="<?php echo $hora; ?>"><?php echo $hora;?></option>																																																																																																																																																																																																					aa
			<?php endif; ?>
			<?php endforeach; ?>
			</select>



		<br>

		<input type="submit" value="Reservar">
	</form>
</div>

<div class="footer-text">
	<p>TFG Fernando Bravo Sanchez</p>
</div>


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





		//verificamos si la hora ya esta reservada

		global $wpdb;
		$tabla_reservas = $wpdb->prefix . 'reservas';
		$reserva_existente = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla_reservas WHERE fecha = %s AND hora = %s", $fecha , $hora));
			
			if ($reserva_existente > 0) {
				echo '<div class="reserva-mensaje reserva-error">  La reserva con la hora seleccionada no esta disponible</div>';
				return;
			}
	
		
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
		$ARCHIVO_LOG = plugin_dir_path(__FILE__) . 'reservas-admin.log';
		$entrada = "Reserva realizada:\n";
		$entrada .= "__________________________\n";
		$entrada .= "Nombre: $nombre\n";
		$entrada .= "Email: $email\n";
		$entrada .= "Fecha: $fecha\n";
		$entrada .= "Hora: $hora\n";
		$entrada .= "_________________________\n";

		//Escribir en el archivo

		file_put_contents($ARCHIVO_LOG, $entrada , FILE_APPEND);

		echo '<div class="reserva-mensaje reserva-exito"> ¡Reserva Realizada! El administrador ha sido notificado</div>';

	}

}

add_action('init', 'rp_procesar_formulario_reserva');

// Agregar un menú en el panel de administración
add_action('admin_menu', 'rp_agregar_menu_reservas');

function rp_agregar_menu_reservas() {
	add_menu_page(
		'Gestión de Reservas', 	//Titulo de la página
		'Reservas FCT',				//Nombres del menú
		'manage_options',			// Capacidad para acceder al menú																																																																																																																																																																																																																																																				aaa
		'reservas-plugin',			// Slug que identifica al menú
		'rp_mostrar_reservas',		// Función que se ejecuta al mostrar el menu
		'dashicons-calendar-alt',	// Icono
		6							// Posición
	);

	//submenu
	add_submenu_page(
		'reservas-plugin' ,
		'Exportar reservas a CSV' ,
		'Exportar CSV',
		'manage_options',
		'exportar-csv',
		'rp_exportar_csv'
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
	echo '<thread><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Hora</th></tr></thread>';
	echo '<tbody>';

	if (!empty($reservas)){
		foreach ($reservas as $reserva){
			echo '<tr>';
			echo '<td>' . esc_html($reserva->id) . '</td>';
			echo '<td>' . esc_html($reserva->nombre) . '</td>';
			echo '<td>' . esc_html($reserva->email) . '</td>';
			echo '<td>' . esc_html($reserva->fecha) . '</td>';
			echo '<td>' . esc_html($reserva->hora) . '</td>';
	echo '</tr>';

		}
	} else {
		echo '<tr><td colspan="6">No hay reservas registradas.</td></tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';

}

//funcion csv

function rp_exportar_csv(){
	// Verificar permisos
	if(!current_user_can('manage_options')){
		wp_die('No tienes permisos para acceder a esta pagina.');
	}

	//comprobaamos si el usuario solicita la exportacion

	if (isset($_GET['exportar_csv'])){
		global $wpdb;
		$tabla_reservas = $wpdb->prefix . 'reservas';

		//Obtener todas las reservas

		$reservas = $wpdb->get_results("SELECT * FROM $tabla_reservas", ARRAY_A);

		if(!empty($reservas)) {
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="reservas.csv"');

			$salida = fopen('php://output', 'wb');

			//Agregar los encabezados al csv

			fputcsv($salida,['ID', 'Nombre', 'Email', 'Fecha', 'Hora']);

			//escribir la reservva en cada fila
			foreach ($reservas as $reserva) {
				fputcsv($salida, $reserva);
			}

			//Cerramos
			fclose($salida);
			exit;
		} else {
			echo '<p> No hay reservas para exportar.</p>';
		}

	}

	//Boton de exportar csv

	echo '<div class="wrap">';
	echo '<h1> Exportar Reservas en un CSV</h1>';
	echo '<p> Descargar las reservas en un CSV.</p>';
	echo '<a href="?page=exportar-csv&exportar_csv=1" class="button-primary">Exportar CSV</a>';
	echo '</div>';
}
?>