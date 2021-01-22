<?php
    function ctrl_siteAdmin_ui($args){
        $instance = new class extends APPControls {
            function exec(){
                return print_r($this->args["this"], true);
            }
        };

        $instance->args = $args;

        return $instance;
    }
?>
