<html>
<body>
	<?php
		require_once ('functions.php');
		require_once ('tercero.php');
		require_once ('oficinaRegistro.php');
		require_once ('anotacion.php');
		require_once ('anotationDocument.php');

		ob_start();

		$baseurl = 'https://02.g3stiona.com/rest/';
		$addon = ADDON_CORRESPONDIENTE;
		$token = null;
		$accessToken = null;
		
		// Obtenemos los Bookmarks de los recursos de la API, para a partir
		// de ellos empezar a 'navegar' haciendo las peticiones.
		$recursos = getRecursos($baseurl, $addon);

		foreach ($recursos->links as $valor) {
			echo $valor->rel."<br>";
			echo $valor->href."<br><br>";
		}

		ob_end_flush();
		flush();

		// Creamos un token de accesso, al crearlo estará en estado
		// 'pendiente de autorizar' a la espera de que nos logueemos con un
		// usuario y lo autoricemos
		if ($token == null)
			$token = createToken($recursos, $addon);

		// Comprobamos el estado del token
		while (!$accessToken) {
			$accessToken = comprobarToken($recursos, $addon, $token);
			ob_end_flush();
			flush();

			if (!$accessToken)
				sleep(10);
		}	

		echo "========================= LOGIN CORRECTO ===============================";

		// Obtenemos la oficina de registro en la que queremos crear la
		// anotación
		$oficinaRegistro = getOficinaRegistro($recursos, $accessToken, "RC");

		// Creamos el tercero y el solicitante a añadir en las anotacíones
		$tercero = new tercero("33333333T", "Tercero-PHP-03", "INVOLVED", "address", "zone", "España", "Zaragoza", "50009", "PAPER", "JURIDICAL", null, null, null, null, null, null);
		$provider = new tercero("44444444P", "Tercero-PHP-04", null, "address", "zone", "España", "Zaragoza", "50012", "PAPER", "JURIDICAL", null, null, null, null, null, null);

		for ($i = 0; $i < 3; $i++) {
			// Creamos la anotación
			$anotacion = new anotacion(null, null, null, null, "C0D-O4161N", null, null, "API prueba rendimiento", "Aquí van las observaciones de la anotación", "REQUERIMENT", "PRESENTIAL", null, null, null, null, null);
			$anotacion = crearAnotacion($accessToken, $oficinaRegistro->links[1]->href, $anotacion);

			// Añadimos el solicitante y el tercero
			addTercero($accessToken, $anotacion, $provider, true);
			addTercero($accessToken, $anotacion, $tercero, false);

			// Subimos un documento a la anotación
			$upload = crearUpload($accessToken, $recursos);
			subirFichero($accessToken, $upload, "/var/www/html/mapeofinalcampos.pdf");
			addFileToAnotacion($accessToken, $anotacion, $upload, "documentoPrueba");

			// Finalizamos la anotación
			finalizarAnotacion($accessToken, $anotacion);
		}
	?>
</body>
</html>