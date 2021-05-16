<?php
    trait admin_produtos {
        private function ajax_produtos($id=""){
            try{
                header("Content-Type: application/json");
                if(isset($_POST["cadprod"])){
                    $id = $_POST["cadprod"]["id"];
                    $_POST["cadprod"]["tp"] = "prod";
                    $query = $this->database()->query("produtos", "id = {$id}");
                    if(!count($query)){
                        $this->database()->push("produtos",array($_POST["cadprod"]));
                    } else {
                        $this->database()->setWhere("produtos","id = {$id}",$_POST["cadprod"]);
                    }
                } else {
                    $query = $this->database()->query("produtos", "name = {$id}");
                    if(!count($query)){
                        $this->database()->push("produtos",array(array("content"=>$_POST["data"], "name" => "{$id}")));
                    } else {
                        $this->database()->setWhere("produtos","name = {$id}",array("content"=>$_POST["data"], "name" => "{$id}"));
                    }
                }
            } catch(Exception $e){
                exit("false");
            }
            exit("true");
        }

        function page_produto($content,$listar=false){
            $content->minify = true;
            $db = "produtos";

            if(
                parent::url(2) == "apagar" && (!empty(parent::url(1)) || (string)parent::url(1) == "0") &&
                count(parent::database()->query($db, "tp=prod and id = " . ($query = (string)parent::url(1)))) > 0
            ){
                exit(parent::database()->deleteWhere($db, "tp=prod and id = {$query}"));
            } elseif(parent::url(2) == "apagar"){
                echo parent::url(1);
                exit;
            }

            $this->dropzoneUpload("imgprod", false, 1024,768);

            if(isset($_POST["imgs"])){
                $model = "";

                foreach($_POST["imgs"] as $img){
                    $model .= "<div style='margin: 48px 0px' class='col-lg-4 col-sm-6 col-xs-12'><div class='col-12 row text-center'></div><div class='col-12 img' data-img-url='{$img}' style='background-image:url({$img})'><br /><br /><br /></div><div class='col-12 text-center'><button  class='apagar m-btn text-center m-btn--pill btn-outline-danger btn'><i class='la las la-trash'></i> Apagar</button></div></div>";
                }

                exit("{$model}");
            }

            if($this->post())return $this->ajax_produtos();

            $id = parent::database()->newID($db,"tp = prod");
            $vars = array(
                "acao" => "cadastrado",
                "id" => $id,
                "botao-txt" => "Adicionar o ve&iacute;culo",
                "TITLE" => "Cadastro de veículos",
                "titulo" => "",
                "ativo" => false,
                "imagens" => "[]",
                "valor" => "R$ 0,00",
                "descricao" => "",
                "ano" => "",
                "km" => "",
                "combustivel" => "",
                "cambio" => "",
                "cor" => "",
                "carroceria" => "",
                "itens" => ""
            );

            if(!empty(parent::url(1)) || (string)parent::url(1) == "0" || $listar){
                if(count($query = parent::database()->query($db, "id = " . (string)parent::url(1))) > 0){
                    $vars["TITLE"] = "Modificar veículo";
                    $vars["botao-txt"] = "Salvar modificações";

                    $vars["acao"] = "modificado";

                    foreach($query[0] as $id=>$val){
                        $vars[$id] = is_array($val) ? json_encode($val):$val;
                    }

                    unset($vars[0]);

                } elseif(parent::url(1) == "listar" || $listar){
                    $produtos = parent::database()->query($db, "id > -1");

                    $btnTxt          = "Ve&iacute;culo";
                    $keyword         = "produto";
                    $db              = $produtos;
                    $titulos         = "Titulo,Ano,KM,Cor,Cambio,Combust&iacute;vel,Valor";
                    $dados           = "titulo,ano,km,cor,cambio,combustivel,valor";
                    $keyid           = "id";
                    $titulo          = "Gerir veículos cadastrados";

                    exit($this->_tablepage($content,$keyword,$titulos,$dados,$keyid,$titulo,$db,$btnTxt)->getCode());
                }
            }

            $query = parent::database()->query($db, "name = categorias",array("content"));

            $query2 = parent::database()->query($db, "name = subcategorias",array("content"));

            $vars["categorias"] = "";

            $vars["subcategorias"] = array();

            if(count($query) > 0){
                foreach($query[0]["content"] as $id=>$cat){
                    $vars["categorias"] .= "<option" . ((string)$id==$vars["categoria"]?" selected":"") . " value='{$id}'>{$cat}</option>";
                    if(count($query2) > 0){
                        $query3 = parent::database()->query($query2[0]["content"], "vinculo = {$id}", array("txt"));
                        $vars["subcategorias"][$id] = "";
                        // print_r($query3);
                        foreach($query3 as $idsub => $subcat){
                            $vars["subcategorias"][$id] .= "<option" . ((string)$idsub==$vars["subcategoria"]?" selected":"") . " value='{$idsub}'>{$subcat["txt"]}</option>";
                        }
                    }
                }
            }

            // $tiny = json_decode(parent::control("connect/tinyERP")->categorias(),true);
            //
            // if($vars["categoria"] == "0" && count($query) == 0){
            //     $vars["categoria"] = (string)$tiny["retorno"][0]["id"];
            //     $vars["subcategoria"] = (string)$tiny["retorno"][0]["nodes"][0]["id"];
            // }

            // $foi = array();
            //
            // foreach($tiny["retorno"] as $cat){
            //     if(!in_array($cat["descricao"], $foi)){
            //         $vars["categorias"] .= "<option" . ((string)$cat["id"]==$vars["categoria"]?" selected":"") . " value='{$cat["id"]}'>* {$cat["descricao"]}</option>";
            //         $foi[] = $cat["descricao"];
            //     }
            //     foreach($cat["nodes"] as $subcat){
            //         if(!in_array($subcat["descricao"], $foi)){
            //             if(!isset($vars["subcategorias"][$cat["id"]])){$vars["subcategorias"][$cat["id"]] = "";}
            //             $vars["subcategorias"][$cat["id"]] .= "<option" . ((string)$subcat["id"]==$vars["subcategoria"]?" selected":"") . " value='{$subcat["id"]}'>* {$subcat["descricao"]}</option>";
            //             $foi[] = $subcat["descricao"];
            //         }
            //         foreach($subcat["nodes"] as $subcat2){
            //             if(!in_array($subcat2["descricao"], $foi)){
            //                 $vars["subcategorias"][$cat["id"]] .= "<option" . ((string)$subcat2["id"]==$vars["subcategoria"]?" selected":"") . " value='{$subcat2["id"]}'>* {$subcat2["descricao"]}</option>";
            //                 $foi[] = $subcat2["descricao"];
            //             }
            //         }
            //     }
            // }

            $vars["subcathtml"] = isset($vars["subcategorias"][(int)$vars["categoria"]])?$vars["subcategorias"][(int)$vars["categoria"]]:"";

            $vars["subcategorias"] = preg_replace("/(selected)/","",json_encode($vars["subcategorias"]));

            $content = $this->simple_loader($content, "admin/produto", $vars);

            echo $content->getCode();
        }

        function page_config_menu($content){
            if($this->post())return $this->ajax_produtos("menu");

            $ordens = "";

            $opcoes = array(
                "Home" => "/",
				"Nosso Estoque" => "/nosso_estoque/",
				"A Empresa" => "/empresa/",
				"Como Chegar" => "/como_chegar/",
                "Fale Conosco" => "/fale_conosco/"
            );

            $opcoes_html = "";

            foreach($opcoes as $titulo=>$url){
                $opcoes_html .= "<option value='{$url}'>{$titulo}</option>";
            }

            for($i = 1; $i < 50; $i++){
                $i = $i < 10 ? "0{$i}":(string)$i;
                $ordens .= "<option value='{$i}'>{$i}</option>";
            }

            $query = parent::database()->query("produtos", "name = menu",array("content"));

            if(count($query) < 1){
                $query = [];
            } else {
                $query = $query[0]["content"];
            }

            echo $this->simple_loader($content, "admin/config-menu", array(
                "TITLE" => "Configurar menu da loja virtual",
                "ordens" => $ordens,
                "opcoes_link" => $opcoes_html,
                "menu_data" => json_encode($query)
            ), array("t_opcao"=>"admin/opcao_menuconf"))->getCode();
        }
    }
