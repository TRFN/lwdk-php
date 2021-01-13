<?php
    function ctrl_siteAdmin_ui($args){
        $instance = new class extends APPControls {
            function exec(){
                return $this->foobar() . $this->args["nome"];
            }
        };

        $instance->args = $args;

        return $instance;
    }
?>
