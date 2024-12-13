# Primer paso
Debemos descargar el archivo reservas.php en nuestra máquina donde tenemos instalado wordpress

# Segundo paso 
En la ruta donde hemos instalado wordpress , debemos encontrar la carpeta **wp-content/plugins** , dentro de plugins crear una carpeta (reservas) donde irá almacenado el archivo reservas.php

# Tercer Paso
Debemos darle a la carpeta que hemos creado (reservas) permisos . Para ello utilizamos **chown www-data:www-data reservas/**

# Cuarto paso 

Nos vamos al panel de adminstracion de wordpress y en el apartado plugins activamos el plugins llamado reservas

# Quinto paso 
Para utilizar el formulario debemos escribir en una pagina visible de wordpress el siguiente shortcode : **[reservas_formulario]**
