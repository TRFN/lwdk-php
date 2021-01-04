<?php
    /*
    DOCUMENTACAO:

        Esta classe pertence ao escopo do LWDK - Light Weight Development Kit (PHP).

        Uso: $ctx->database->{funcao}();

        $ctx->database->set(@[string]{file}, @[string|array]{key}, [string]{value}) -> Define um valor direto em um banco de dados.

        ...TERMINAR DE ESCREVER PARA NAO ESQUECER DPS...
        KKKKKKKKKKKKKKKKKK
    */

    class __database {
        private $password = "none";

        function __construct($ctx){
            $this->context = $ctx;
        }

        private function like(String $needle, String $haystack, String $options = ""){
            return !!preg_match( "/^" . str_replace( '%', '(.*?)', trim($needle) ) .  "$/{$options}", trim($haystack) );
        }

        private function getCurrentDateTime(){
            return array(array(date("d"),date("m"),date("Y")),array(date("H"),date("i"),date("s")));
        }

        private function readQuery(String $query){
            $conditions = preg_split('/ (and|\&\&) /', $query);

            foreach($conditions as $key1=>$value1){
                $conditions[$key1] = preg_split('/ (or|\|\|) /', $value1);
                foreach($conditions[$key1] as $key2=>$value2){
                    if($this->like("%\!\=%", $conditions[$key1][$key2])){
                        $conditions[$key1][$key2] = array(0, array_map('trim', explode("!=", $value2)));
                    } elseif($this->like("%\=%", $conditions[$key1][$key2])){
                        $conditions[$key1][$key2] = array(1, array_map('trim', explode("=", $value2)));
                    } elseif($this->like("%\>%", $conditions[$key1][$key2])){
                        $conditions[$key1][$key2] = array(2, array_map('trim', explode(">", $value2)));
                    } elseif($this->like("%\<%", $conditions[$key1][$key2])){
                        $conditions[$key1][$key2] = array(3, array_map('trim', explode("<", $value2)));
                    }
                }
            }

            return $conditions;
        }

        public function path($file){
            return "{$this->context->path->database}/{$file}.data";
        }

        public function set(String $file, $key, $value=null){
            $content = $this->get($file);
            $file = $this->path($file);

            if(is_array($key)){
                foreach($key as $keyword=>$value){
                    $content[$keyword] = $value;
                }
            } else {
                $content[$key] = $value;
            }

            $content = serialize($content);
            if($this->password != "none"){
                $content = crypto::crypt($content, $this->password);
            }
            file_put_contents($file, $content);
        }

        public function get(String $file, $key="*"){
            $_file = $this->path($file);
            $content = array();
            if(file_exists($_file)){
                $content = file_get_contents($_file);
                if($this->password != "none"){
                    $content = crypto::unCrypt($content, $this->password);
                }
                $content = @unserialize($content);
            }

            if($content == false){
                $this->context->message("<strong>WARNING</strong>: The database \"<big>{$file}.data</big>\" is damanged or the password is wrong! Please verify.");
                return array();
            }

            if(is_array($key)){
                $result = array();
                foreach($key as $keyword){
                    $result[$keyword] = $content[$keyword];
                }
            } elseif($key == "*") {
                $result = $content;
            } else {
                $result = $content[$key];
            }

            foreach($result as $key=>$value){
                if($value == -1){
                    unset($result[$key]);
                } else {
                    $result[$key]["@ID"] = $key;
                }
            }

            return $result;
        }

        public function push(String $file, $key, $value=null){
            $content = $this->get($file);
            $file = $this->path($file);

            if(is_array($key)){
                foreach($key as $keyword=>$value){
                    if(is_array($value)){
                        $value["@CREATED"] = $this->getCurrentDateTime();
                    }
                    $content[] = $value;
                }
            } else {
                if($value == null){
                    if(is_array($key)){
                        $key["@CREATED"] = $this->getCurrentDateTime();
                    }
                    $content[] = $key;
                } else {
                    if(is_array($value)){
                        $value["@CREATED"] = $this->getCurrentDateTime();
                    }
                    $content[$key] = $value;
                }
            }

            $content = serialize($content);
            if($this->password != "none"){
                $content = crypto::crypt($content, $this->password);
            }
            file_put_contents($file, $content);
        }

        public function query($file, String $query, $keys = "*", bool $ignoreCase = true){
            if(is_array($file)){
                $content = $file;
            } else {
                $content = $this->get($file);
            }

            // var_dump($content);

            $results = [];

            $queryTanslate = $this->readQuery($query);

            foreach($content as $key=>$value){
                if(is_array($content[$key])){

                    $content[$key]["@ID"] = $key;

                    $findGlobal = true;
                    // print_r($queryTanslate);
                    foreach($queryTanslate as $andKeyword){
                        $find = false;
                        foreach($andKeyword as $orKeyword){
                            switch($orKeyword[0]){
                                case 0:
                                    $find = ($find || (isset($content[$key][$orKeyword[1][0]]) && !$this->like($orKeyword[1][1],$content[$key][$orKeyword[1][0]],($ignoreCase?"i":""))));
                                break;
                                case 1:
                                    // var_dump("{$content[$key][$orKeyword[1][0]]}-{$orKeyword[1][1]}") . "\n";
                                    $find = ($find || (isset($content[$key][$orKeyword[1][0]]) && $this->like($orKeyword[1][1],$content[$key][$orKeyword[1][0]],($ignoreCase?"i":""))));
                                break;
                                case 2:
                                    $find = ($find || (isset($content[$key][$orKeyword[1][0]]) && ((int)$orKeyword[1][1]<(int)$content[$key][$orKeyword[1][0]])));
                                break;
                                case 3:
                                    $find = ($find || (isset($content[$key][$orKeyword[1][0]]) && ((int)$orKeyword[1][1]>(int)$content[$key][$orKeyword[1][0]])));
                                break;
                            }
                        }

                        // echo "\nEND-OF-OR:" . print_r($find, true) . "\n";
                        $findGlobal = $findGlobal && $find;
                    }

                    // echo "\nEND-OF-AND:" . print_r($findGlobal,true) . "\n";

                    if($findGlobal){
                        if(is_array($keys)){
                            foreach($keys as $keyword){
                                $result[$keyword] = $content[$key][$keyword];
                            }
                        } elseif($keys == "*") {
                            $result = $content[$key];
                            // echo "GETS: {$key} \n";
                        } else {
                            $result = $content[$key][$keys];
                        }

                        $results[] = $result;
                    }
                }
            }
            return $results;
        }

        public function setWhere(String $file, String $by, $key, $value=null){
            $content = $this->get($file);
            $ids = $this->query($file, $by, array("@ID"));
            $file = $this->path($file);

            // var_dump($ids);

            foreach($ids as $id){
                $id = $id["@ID"];
                if(is_array($key)){
                    // var_dump($content[$id]);
                    foreach($key as $keyword => $value){
                        $content[$id][$keyword] = $value;
                        // var_dump($content[$id][$keyword]);
                        // var_dump($keyword);
                    }
                } else {
                    $content[$id][$keys] = $value;
                }
                $content[$id]["@MODIFIED"] = $this->getCurrentDateTime();
            }

            $content = serialize($content);
            if($this->password != "none"){
                $content = crypto::crypt($content, $this->password);
            }
            return file_put_contents($file, $content);
        }

        public function deleteWhere(String $file, String $by){
            $content = $this->get($file);
            $ids = $this->query($file, $by, array("@ID"));

            if(count($ids) < 1)return false;

            $file = $this->path($file);

            foreach($ids as $id){
                $content[$id["@ID"]] = -1;
            }

            $content = serialize($content);
            if($this->password != "none"){
                $content = crypto::crypt($content, $this->password);
            }
            return file_put_contents($file, $content);
        }

        public function setPassword(String $password){
            $this->password = md5($password);
        }

        public function removePassword(){
            $this->password = "none";
        }

        public function protectOnPassword(String $file, String $password){
            $this->removePassword();
            $content = $this->get($file);
            if(count($content) == 0)return false;
            // var_dump($content);
            // exit;
            $content = serialize($content);
            // var_dump($content);
            // exit;
            $this->setPassword($password);
            $content = crypto::crypt($content, $this->password);
            // var_dump($content);
            // exit;
            file_put_contents($this->path($file), $content);
        }

        public function unProtectPassword(String $file, String $password){
            $this->setPassword($password);
            $content = $this->get($file);
            // var_dump($content);
            if(count($content) == 0)return false;
            $content = serialize($content);
            // var_dump($content);
            file_put_contents($this->path($file), $content);
            $this->removePassword();
        }

        public function changePassword(String $file, String $oldPassword, String $password){
            $this->setPassword($oldPassword);
            $content = $this->get($file);
            if(count($content) == 0)return false;
            $content = serialize($content);
            $this->setPassword($password);
            $content = crypto::crypt($content, $this->password);
            file_put_contents($this->path($file), $content);
        }

    }
?>
