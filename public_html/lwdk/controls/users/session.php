<?php

/*
    VER: 1.2
    LAST-UPDATE: 24/04/2021
*/

function ctrl_users_session($args){
    $instance = new class extends APPControls {
        public $keyid    = "usuario";
        public $database = "usuarios";
        public $keyuser  = "email";
        public $keypass  = "senha";
        public $hash     = "md5";
        public $mainkey  = "@ID";

        function session(){
            // print_r("{$this->mainkey} = {$_SESSION[$this->keyid]}");
            if(isset($_SESSION[$this->keyid])){
                if(is_string($this->database) && count($user = $this->database()->query("{$this->database}", "{$this->mainkey} = {$_SESSION[$this->keyid]}")) === 1){
                    return (Object)$user[0];
                } elseif(is_array($this->database)) {
                    foreach($this->database as $db){
                        if(count($user = $this->database()->query("{$db}", "{$this->mainkey} = {$_SESSION[$this->keyid]}")) === 1){
                            return (Object)$user[0];
                        }
                    }
                }
            }

            return false;
        }

        function connect($keyuser, $keypass){
            switch($this->hash){
                case "md5": $keypass = md5($keypass); break;
            }

            $user = array();

            if(is_string($this->database)){
                $user = $this->database()->query("{$this->database}", "{$this->keyuser} = {$keyuser} and {$this->keypass} = {$keypass}");
            } elseif(is_array($this->database)) {
                foreach($this->database as $db){
                    $user = $this->database()->query("{$db}", "{$this->keyuser} = {$keyuser} and {$this->keypass} = {$keypass}");
                    if(count($user) === 1){
                        break;
                    }
                }
            }

            $result = count($user) === 1 ? (function($user,$mainkey){
                $_SESSION[$this->keyid] = $user[0][$mainkey];
                return $user[0][$mainkey];
            })($user,$this->mainkey):false;


            // print_r($_SESSION);
            // exit(print_r($result));

            return $result;
        }

        function logout(){
            session_unset();
        }
    };

    $instance->args = $args;

    return $instance;
}
