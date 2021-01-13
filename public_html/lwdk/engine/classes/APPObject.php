<?php
    class APPObject {
        private $dir = null;

        function rootDir(String $set="empty"){
            if($set == "empty"){
                return $this->dir;
            } else {
                return $this->dir = $set;
            }
        }

        function control($control,$args){
            include_once (new __paths)->get()->controls . "/{$control}.php";
            $control = preg_replace("/\//", "_", $control);
            $control = "ctrl_{$control}";
            return ${"control"}($args);
        }
    }
?>
