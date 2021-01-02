<?php
    class lwdk {
        function __construct(){
            $this->setup();
        }

        private function setup(){
            foreach(glob("lwdk/engine/classes/*.php") as $file){
                require $file;
            }

            $this->path = __paths::get();

            ini_set('memory_limit', '-1');
            date_default_timezone_set('America/Sao_Paulo');
            session_start();
        }

        private function https(bool $state=false){
            $https = [
                ($state && $_SERVER['SERVER_ADDR'] != "127.0.0.1"),
                ((! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
                (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
                (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443'))
            ];

            if($https[0] && !$https[1]){
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit;
            } elseif(!$https[0] && $https[1]){
                header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit;
            } else {
                return $https[0] == $https[1];
            }
        }
    }
?>
