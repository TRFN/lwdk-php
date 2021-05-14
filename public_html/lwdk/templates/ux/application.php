<?php
    class application extends APPObject {
        function __construct(){
            # CONFIGURATIONS #
            $this->rootDir("/");
        }

        function page_main($content){
            echo "<pre>";
			var_dump($content);
        }
    }
?>
