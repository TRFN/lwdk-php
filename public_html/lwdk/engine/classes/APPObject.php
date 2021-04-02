<?php
    class APPObject {
        private $dir = null;
        private $parent = null;
        public  $defaultPage = "main";
        public  $defaultUiTemplate = null;
        public  $uiTemplate = false;
        public  $defaultVars = array();

        function rootDir(String $set="empty"){
            if($set == "empty"){
                return $this->dir;
            } else {
                return $this->dir = $set;
            }
        }

        function applyVars(Array $vars){
            $this->defaultVars = array_merge($this->defaultVars, $vars);
        }

        function generateID($base=-1){
            if($base == -1){
                $base = bae64_encode(uniqid());
            }

            return md5(strtolower(preg_replace("/[^A-z0-9]/","",preg_replace("/(&[\s\S]+?;)/","",$base))));
        }

        function uiTemplateDefault($template){
            $path = (new __paths)->get();
            $this->defaultUiTemplate = file_get_contents("{$path->templates}/ui/{$template}.html");
        }

        function uiTemplate($template){
            $path = (new __paths)->get();
            $this->uiTemplate = file_get_contents("{$path->templates}/ui/{$template}.html");
        }

        function control(String $control, Array $args = array()){
            $args["ux"] = $this;
            $args["lwdk"] = $this->parent;
            include_once (new __paths)->get()->controls . "/{$control}.php";
            $control = preg_replace("/\//", "_", $control);
            $control = "ctrl_{$control}";
            return ${"control"}($args);
        }

        function controls(){}

        function url(int $index=-1, String $url = "empty"){
            if($url == "empty"){
                $url = $_SERVER["REQUEST_URI"];
            }
            $url = explode("/", $url);
            array_shift($url);
            return $index == -1 ? $url:(isset($url[$index])?$url[$index]:"");
        }

        function inUrl(String $needle){
            return in_array($needle, $this->url());
        }

        function getPage(){
            // exit($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . ".html");
            if(method_exists($this,"protect")){
                $this->protect();
            }

            $exec = "page_" . $this->url(count($this->url(-1, $this->rootDir()))-1);
            if(!method_exists($this,$exec)){
                $file = false;

                if(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . ".html")){
                    $file = $f;
                } elseif(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . ".htm")){
                    $file = $f;
                } elseif(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . "index.html")){
                    $file = $f;
                } elseif(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . "index.htm")){
                    $file = $f;
                } elseif(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . "/index.html")){
                    $file = $f;
                } elseif(file_exists($f = dirname(dirname(dirname(__DIR__))) . $_SERVER["REQUEST_URI"] . "/index.htm")){
                    $file = $f;
                }

                if($file !== false){
                    readfile($file);
                    exit;
                } else {
                    $exec = "page_{$this->defaultPage}";
                }
            }

            if(method_exists($this,$exec)){
                $uiTemplate = new UITemplate($this);

                $uiTemplate->setTemplate(is_bool($this->uiTemplate) && !$this->uiTemplate ? $this->defaultUiTemplate : $this->uiTemplate);

                $this->{$exec}($uiTemplate);
            }
        }

        function setParent(lwdk $parent){
            $this->parent = $parent;
        }

        function parent(){
            return $this->parent;
        }

        /** FUNCIONALIDADES ADICIONAIS **/

        function entity ($string) {
            $string = str_split($string);

            for($i = 0; $i < count($string); $i++){
                $code = (int)ord($string[$i]);
                if($code > 123)$string[$i] = "&#{$code};";
            }

            return implode("", $string);
        }

        function database(){
            return new __database();
        }

        function unMaskCfg($cfg_index){
            return str_split(decbin(hexdec($cfg_index)));
        }

        function maskCfg($cfg_array){
            return dechex(bindec(implode("", $cfg_array)));
        }
    }
?>
