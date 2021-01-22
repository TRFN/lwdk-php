<?php
    trait route {
        private $Applications;
        private $defaultApplication = 0;
        private $app = false;

        function __construct(){
            $this->Applications = array();
        }

        function addApp(APPObject $class, lwdk $parent, bool $default = false){
            $class->setParent($parent);

            $this->Applications[] = $class;

            if($default){
                $this->defaultApplication = count($this->Applications) - 1;
            }
        }

        function selectApp(){
            $this->app = $this->checkDir();
        }

        function url(int $index=-1, String $url = "empty"){
            if($url == "empty"){
                $url = $_SERVER["REQUEST_URI"];
            }
            $url = explode("/", $url);
            array_shift($url);
            return $index == -1 ? $url:$url[$index];
        }

        function checkDir(){
            foreach($this->Applications as $key=>$application){
                $thisApplication = true;
                for($i = 0; $i < count($this->url(-1, $application->rootDir()))-1; $i++){
                    $thisApplication = $thisApplication && ($this->url($i, $application->rootDir()) == $this->url($i));
                }
                if($thisApplication)return $key;
            }

            return $this->defaultApplication;
        }

        function getApp(){
            return $this->Applications[$this->app];
        }
    }
?>
