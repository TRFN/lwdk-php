<?php
    class APPControls {
        function database(){
            return isset($this->args) && isset($this->args["ux"]) && is_object($this->args["ux"]->parent()) ? $this->args["ux"]->parent()->database:(new __database);
        }

        function loadPlugin(String $plugin){
            require_once dirname(dirname(dirname(__FILE__))) . "/plugins/" . preg_replace("/\@/","/",$plugin) . ".php";
            $plugin = @end(explode("@", $plugin));
            return new $plugin;
        }
    }
?>
