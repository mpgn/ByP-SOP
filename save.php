<?php

    /* 
        allow Cross-origin resource sharing (CORS) 
        not needed if you use image.src from javascript
        (img is not compatible with SOP)
    */
    // header("Access-Control-Allow-Origin: *");

    /* save the result in a file */
    file_put_contents("save.txt", json_encode($_GET) . "\n", FILE_APPEND); 
?>