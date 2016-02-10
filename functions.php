<?php
	/*
	 * Rellenamos la variable recursos con todos los bookmarks de la API
	 */
	function getRecursos ($baseurl, $addon) {
		$curl = curl_init($baseurl);
	  	curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Addon-Token: ' . $addon)); 

		$response = curl_exec($curl);

		if (!$response)
			die("Connection Failure.\n");

		$result = json_decode($response);

		curl_close($curl);

		if ( isset($result->error) )
			die($result->error_message . "\n");

		return $result;
	}

	/*
	 * Crea el token con el que tendremos que loguearnos para obtener la autorización.
	 */
	function createToken ($recursos, $addon) {
		$curl = curl_init($recursos->links[10]->href);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Addon-Token: ' . $addon)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 201) {
			$headers = explode("Location: ", $response);
			$headers = explode("\n", $headers[1]);
			$location = $headers[0];

			$token = trim(substr($location, strrpos($location, '/') + 1));
			echo '::TOKEN ==> ' . $token . '<br>';
		} else if ($http_status_code == 403) {
			die("Error al crear el accessToken, no se encuentra el addon " . $addon);
		} else {
			die("Error al crear el accessToken: " . $http_status_code);
		}

		return $token;
	}

	/*
	 * Comprueba que el token que se le pasa como parámetro esté en estado autorizado. En
	 * caso de estar pendiente de autorización nos devuelve la URL en la que nos debemos
	 * loguear con un usuario y contraseña para autorizar ese token.
	 */
	function comprobarToken ($recursos, $addon, $token) {
		$curl = curl_init($recursos->links[10]->href . '/' . urlencode($token));
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Addon-Token: ' . $addon)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 200) {
			$body = strstr($response, '{');
			$result = json_decode($body);

			$accessToken = $result->access_token;

			echo '::TOKEN ==> ' . $token . ' ACCESS_TOKEN ==> ' . $accessToken . '<br>';

			return $accessToken;
		} else if ($http_status_code == 401) {
			$headers = explode("Location: ", $response);
			$headers = explode("\n", $headers[1]);
			$location = $headers[0];

			echo "Entre en esta URL y logueese con su usuario y contraseña para validar el token: " . $location . "<br>[Pulse el botón cuando ya lo haya actualizado]";
		} else {
			die($result->error_message . "\n");
		}
	}

	/*
	 * Buscar oficina de registro según el código de la oficina que se le pasa como
	 * parámetro.
	 */
	function getOficinaRegistro ($recursos, $accessToken, $code) {
		$filter = '{ "code": "' . $code . '" }';
		$b64 = base64_encode($filter);

		$curl = curl_init($recursos->links[37]->href . '?filter-view=' . $b64);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 200) {
			$result = json_decode($response);
			$resultBody = $result->content[0];

			$oficinaRegistro = new oficinaRegistro();
			$oficinaRegistro->set($resultBody);

			return $oficinaRegistro;
		} else {
			die("Error al obtener la oficina de registro: " . $result->error_message . "\n");
		}
	}

	/*
	 * Hace el POST sobre la uri que le pasamos para crear la anotación con los datos
	 * que le pasamos en el objeto Anotacion
	 */
	function crearAnotacion ($accessToken, $uri, $anotacion) {
		$data_string = json_encode($anotacion);

		$curl = curl_init($uri);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.gestiona.registry-annotation', 'X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 201) {
			$headers = explode("Location: ", $response);
			$headers = explode("\n", $headers[1]);
			$location = $headers[0];

			$anotacionDevolver = getAnotacion($accessToken, $location);
			return $anotacionDevolver;
		} else {
			die("Error al crear anotación: " . $result->error_message . "\n");
		}
	}

	/*
	 * Dado el link de una anotación existente, hará la petición GET y nos devolverá los
	 * datos de dicha anotación mapeados en el objeto Anotacion que nos hemos creado.
	 */
	function getAnotacion ($accessToken, $uri) {
		$curl = curl_init(trim($uri));
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 200) {
			$result = json_decode($response);
			
			$anotacion = new anotacion();
			$anotacion->set($result);

			return $anotacion;
		} else {
			die("Error al obtener anotación: " . $http_status_code . "\n");
		}
	}

	/*
	 * Añade el tercero que se le pasa como parámetro a la anotación que también se le
	 * pasa como parámetro.
	 */
	function addTercero ($accessToken, $anotacion, $tercero, $isProvider) {
		$data_string = json_encode($tercero);

		$uri = null;

		if ($isProvider)
			$uri = $anotacion->links[2]->href;
		else
			$uri = $anotacion->links[3]->href;

		$curl = curl_init($uri);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.gestiona.thirdparty', 'X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 201) {
			return true;
		} else if ($http_status_code == 401) {
			die("Error al añadir tercero: no tiene autorización " . $result->error_message . "\n");
		} else {
			die("Error al añadir tercero: " . $http_status_code . "\n");
		}
	}

	/*
	 * Crea un nuevo recurso upload sobre el cual tendremos que hacer la suvbida del
	 * fichero posteriormente.
	 */
	function crearUpload ($accessToken, $recursos) {
		$curl = curl_init($recursos->links[11]->href);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 201) {
			$headers = explode("Location: ", $response);
			$headers = explode("\n", $headers[1]);
			$location = $headers[0];

			return $location;
		} else if ($http_status_code == 401) {
			die("Error al crear el upload, no tiene autorización");
		} else {
			die("Error al crear el upload: " . $http_status_code);
		}
	}

	/*
	 * Hace el PUT para subir el fichero.
	 */
	function subirFichero ($accessToken, $uri, $pathfile) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, trim($uri));
		curl_setopt($curl, CURLOPT_PUT, 1);

		$fh_res = fopen($pathfile, 'r');

		curl_setopt($curl, CURLOPT_INFILE, $fh_res);
		curl_setopt($curl, CURLOPT_INFILESIZE, filesize($pathfile));

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/octet-stream', 'Slug: prueba.pdf', 'X-Gestiona-Access-Token: ' . $accessToken));
		$response = curl_exec ($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		fclose($fh_res);

		if ($http_status_code == 200) {
			return true;
		} else if ($http_status_code == 401) {
			die("Error al subir fichero: no tiene autorización");
		} else {
			die("Error al subir fichero: " . $http_status_code . "\n");
		}	
	}

	/*
	 * Añadir un documento a la anotación.
	 */
	function addFileToAnotacion ($accessToken, $anotacion, $uri, $nombreDoc) {
		if (!$anotacion || !$uri || !$nombreDoc)
			return false;

		$anotationDocument = new anotationDocument($nombreDoc, "DIGITAL");
		$anotationDocument->links = array();
		$anotationDocument->links[0]->rel = "content";
		$anotationDocument->links[0]->href = trim($uri);

		$data_string = json_encode($anotationDocument);

		$curl = curl_init($anotacion->links[5]->href);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.gestiona.annotation-document', 'X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 201) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * Finaliza la anotación.
	 */
	function finalizarAnotacion ($accessToken, $anotacion) {
		$curl = curl_init($anotacion->links[6]->href);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Gestiona-Access-Token: ' . $accessToken)); 

		$response = curl_exec($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($http_status_code == 200) {
			return true;
		} else if ($http_status_code == 401) {
			die("Error al finalizar anotación, no tiene autorización");
		} else {
			die("Error al finalizar anotación: " . $result->error_message . "\n");
		}
	}
?>