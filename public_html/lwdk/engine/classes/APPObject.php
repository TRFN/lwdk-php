<?php
    class APPObject {
        private $dir = null;
        private $parent = null;
        public  $defaultPage = "main";
        private  $defaultUiTemplate = null;
        private  $uiTemplate = false;

        function rootDir(String $set="empty"){
            if($set == "empty"){
                return $this->dir;
            } else {
                return $this->dir = $set;
            }
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
            $args["this"] = $this;
            $args["parent"] = $this->parent;
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
                $uiTemplate = new UITemplate($this);

                $uiTemplate->setTemplate(is_bool($this->uiTemplate) && !$this->uiTemplate ? $this->defaultUiTemplate : $this->uiTemplate);

                $this->{$exec}($uiTemplate);
            }
        }

        function setParent(lwdk $parent){
            $this->parent = $parent;
        }
    }
?>
