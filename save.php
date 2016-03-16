<?php

	/* allow Cross-origin resource sharing (CORS) */
	header("Access-Control-Allow-Origin: *.domain.fr");

	/* save the result in a file */
	file_put_contents("save.txt", $_GET. "\n", FILE_APPEND); 
?>