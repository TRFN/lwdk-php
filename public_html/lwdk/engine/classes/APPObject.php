<?php
    class APPObject {
        private $dir = null;
        private $parent = null;
        public  $defaultPage = "main";

        function rootDir(String $set="empty"){
            if($set == "empty"){
                return $this->dir;
            } else {
                return $this->dir = $set;
            }
        }

        function control(String $control, Array $args = array()){
            $args["this"] = $this;
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
            return $index == -1 ? $url:$url[$index];
        }

        function getPage(){
            $exec = "page_" . $this->url(count($this->url(-1, $this->rootDir()))-1);

            if(!method_exists($this,$exec)){
                $exec = "page_{$this->defaultPage}";
            }

            if(method_exists($this,$exec)){
                $this->{$exec}();
            }
        }

        function setParent(lwdk $parent){
            $this->parent = $parent;
        }
    }
?>
