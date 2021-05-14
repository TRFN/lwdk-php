<?php
    trait admin_textos {
        private function ajax_textos($id=""){
            try{
                header("Content-Type: application/json");
                $id = md5(empty($id)?parent::url(1):$id);
                $query = $this->database()->query("textos", "name = {$id}");
                if(!count($query)){
                    $this->database()->push("textos",array(array("txt"=>$_POST["data"], "name" => "{$id}")));
                } else {
                    $this->database()->setWhere("textos","name = {$id}",array("txt"=>$_POST["data"], "name" => "{$id}"));
                }
            } catch(Exception $e){
                exit("false");
            }
            exit("true");
        }

        function page_textos($content){
            if($this->post())return $this->ajax_textos();

            switch(parent::url(1)){
                case "politica-de-privacidade":
                    $vars = array(
                        "nomecampo" => "Pol&iacute;ticas de Privacidade"
                    );
                break;
                case "garantia-dos-produtos":
                    $vars = array(
                        "nomecampo" => "Garantia dos Produtos"
                    );
                break;
                case "trocas-e-devolucoes":
                    $vars = array(
                        "nomecampo" => "Trocas de Devolu&ccedil;&otilde;es"
                    );
                break;
                case "como-comprar":
                    $vars = array(
                        "nomecampo" => "Como Comprar (Instru&ccedil;&otilde;es ao Cliente)"
                    );
                break;
            }

            $vars["id_form"] = parent::url(1);

            $id = md5($vars["id_form"]);
            $query = $this->database()->query("textos", "name = {$id}", array("txt"));

            if(count($query)){
                $vars["valuesof"] = json_encode($query[0]["txt"]);
            } else {
                $vars["valuesof"] = json_encode(array("<p><br></p>","<p><br></p>"));
            }

            $content = $this->simple_loader($content, "admin/textos", $vars);
            echo $content->getCode();
        }

        function page_texto_rotativo($content){
            if($this->post())return $this->ajax_textos("texto_rotativo");

            $vars = array();

            $id = md5("texto_rotativo");

            $query = parent::database()->query("textos", "name = {$id}",array("txt"));

            $vars["valuesof"] = json_encode($query[0]["txt"]);

            $content = $this->simple_loader($content, "admin/texto-rotativo", $vars);
            echo $content->getCode();
        }
    }
