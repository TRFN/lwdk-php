<?php
    class __database {
        private $password = "none";

        function __construct($workDir){
            $this->workingDirectory = $workDir;
        }

        public function set(String $file, $key, $value=null){
            $file = "{$this->workingDirectory}/{$file}.data";
            $content = array();
            if(file_exists($file)){
                $content = file_get_contents($file);
                if($this->password != "none"){
                    $content = crypto::unCrypt($content, $this->password);
                }
                $content = unserialize($content);
            }

            if(is_array($key)){
                foreach($key as $keyword=>$value){
                    $content[$keyword] = $value;
                }
            } else {
                $content[$key] = $value;
            }

            $content = serialize($content);
            if($this->password != "none"){
                $content = crypto::unCrypt($content, $this->password);
            }
            file_put_contents($file, $content);
        }

        public function setPassword(String $password){
            $this->password = md5($password);
        }

        public function removePassword(String $password){
            $this->password = "none";
        }
    }
?>
