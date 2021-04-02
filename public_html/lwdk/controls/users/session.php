<?php
function ctrl_users_session($args){
    $instance = new class extends APPControls {
        public $keyid    = "usuario";
        public $database = "usuarios";
        public $keyuser  = "email";
        public $keypass  = "senha";
        public $hash     = "md5";

        function session(){
            if(isset($_SESSION[$this->keyid]) && count($user = $this->database()->query("{$this->database}", "@ID = {$_SESSION[$this->keyid]}")) === 1){
                return $user[0];
            } else {
                return false;
            }
        }

        function connect($keyuser, $keypass){
            switch($this->hash){
                case "md5": $keypass = md5($keypass); break;
            }
            $user = $this->database()->query("{$this->database}", "{$this->keyuser} = {$keyuser} and {$this->keypass} = {$keypass}");
            return count($user) === 1 ? (function($user){
                $_SESSION[$this->keyid] = $user[0]["@ID"];
                return $user[0]["@ID"];
            })($user):false;
        }

        function logout(){
            session_unset();
        }
    };

    $instance->args = $args;

    return $instance;
}
