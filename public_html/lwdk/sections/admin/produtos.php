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

        private function tiny_produto(String $id){
            $tiny = parent::control("connect/tinyERP");
            $obj = $this->tiny_produtos($id,0);
            $prod = (!empty($obj) && is_object($obj) && ((int)($obj->retorno->status_processamento) == 3)) ? $obj->retorno->produto:false;
            if($prod !== false){
                $estoque = json_decode($tiny->estoque($prod->id));
                if((int)$estoque->retorno->status_processamento == 3){
                    $estoque = $estoque->retorno->produto;
                    $imgs = array();
                    foreach($prod->imagens_externas as $img){
                        $imgs[] = array(
                            "url" => $img["imagem_externa"]["url"],
                            "legend" => ""
                        );
                    }
                    if(!empty($prod->categoria)){
                        // var_dump($prod->categoria);
                    } else {
                        $categoria = "0";
                        $subcategoria = "0";
                    }
                    return array(
                        "id" => $prod->id,
                        "nome" => $prod->nome,
                        "categoria" => $categoria,
                        "subcategoria" => $subcategoria,
                        "sku" => "{$prod->codigo}",
                        "ativo" => false,
                        "lancamento" => false,
                        "destaque" => false,
                        "promocao" => false,
                        "imagens" => $imgs,
                        "valor" => "R$ " . number_format((int)$prod->preco,2,",","."),
                        "valor-a-vista" => "R$ " . number_format((int)$prod->preco_promocional,2,",","."),
                        "parcelas-sem-juros" => "1",
                        "descricao-longa" => "{$prod->descricao_complementar}",
                        "descricao-curta" => "{$prod->obs}",
                        "largura" => "{$prod->larguraEmbalagem}",
                        "altura" => "{$prod->alturaEmbalagem}",
                        "comprimento" => "{$prod->comprimentoEmbalagem}",
                        "peso_liq" => "{$prod->peso_liquido}",
                        "peso_bruto" => "{$prod->peso_bruto}",
                        "quantidade_estoque" => "{$estoque->saldo}",
                        "unidade" => "{$estoque->unidade}"
                    );
                }
            }
            return false;
        }

        private function tiny_produtos($id=-1,int $pagina=1){
            $tiny = parent::control("connect/tinyERP");
            if($id==-1){
                $produtos = array();
                $pagina = max(min((int)json_decode($tiny->produto())->retorno->numero_paginas,$pagina),1);

                $produtos_group = json_decode($tiny->produto(-1, $pagina),true);
                if((int)$produtos_group["retorno"]["status_processamento"] == 3){
                    foreach($produtos_group["retorno"]["produtos"] as $prod){
                        $prod = (object)$prod["produto"];
                        $produtos[] = array(
                            "id" => $prod->id,
                            "sku" => $prod->codigo,
                            "nome" => $prod->nome,
                            "valor" => "R$ " . number_format((int)$prod->preco,2,",",".")
                        );
                    }
                }

                return $produtos;
            } else {
                return json_decode($tiny->produto($id), $pagina>0);
            }
            return false;
        }

        private function tiny_paginas(){
            return (int)json_decode(parent::control("connect/tinyERP")->produto())->retorno->numero_paginas;
        }

        // function page_tiny(){
        //     if(!$this->post()){
        //         header("Location: " . $this->rootDir());
        //     }
        //
        //     switch($_POST["fn"]){
        //         case "cat":
        //         $tiny = json_decode(parent::control("connect/tinyERP")->categorias(),true);
        //         $cats = array();
        //         foreach($tiny["retorno"] as $cat){
        //             $cats[] = array("{$cat["id"]}" => $cat["descricao"]);
        //         }
        //
        //             header("Content-Type: application/json");
        //             exit(json_encode($cats));
        //         break;
        //         case "subcat":
        //             $tiny = json_decode(parent::control("connect/tinyERP")->categorias(),true);
        //             $cats = array();
        //             foreach($tiny["retorno"] as $cat){
        //                 foreach($cat["nodes"] as $subcat){
        //                     !isset($cats[$cat["id"]]) && ($cats[$cat["id"]] = array());
        //                     $cats[$cat["id"]][] = array("{$subcat["id"]}" => $subcat["descricao"]);
        //                 }
        //             }
        //
        //             header("Content-Type: application/json");
        //             exit(json_encode($cats));
        //         break;
        //     }
        // }

		function page_cupons($content,$get=false){
            if($this->post())return $this->ajax_produtos("cupons_desc");

            $vars = array("TITLE" => "Cupons de Desconto da Loja");

            $query = parent::database()->query("produtos", "name = cupons_desc",array("content"));

            if(count($query) < 1){
                $cats = array();
            } else {
                $cats = $query[0]["content"];
            }

            if($get){
                return $cats;
            }

            $vars["valuesof"] = json_encode($cats);

            $content = $this->simple_loader($content, "admin/cupons", $vars);
            echo $content->getCode();
        }

		function page_categorias($content,$get=false){
            if($this->post())return $this->ajax_produtos("categorias");

            $vars = array("TITLE" => "Categorias");

            $query = parent::database()->query("produtos", "name = categorias",array("content"));

            if(count($query) < 1){
                $cats = array();
            } else {
                $cats = $query[0]["content"];
            }

            if($get){
                return $cats;
            }

            $vars["valuesof"] = json_encode($cats);

            $content = $this->simple_loader($content, "admin/categorias", $vars);
            echo $content->getCode();
        }

        function page_sub_categorias($content,$get=false){
            if($this->post())return $this->ajax_produtos("subcategorias");

            $vars = array("TITLE" => "Sub-Categorias");

            $query = parent::database()->query("produtos", "name = subcategorias",array("content"));

            if($get)return $query[0]["content"];

            if(count($query)>0)$vars["valuesof"] = json_encode($query[0]["content"]);
            else $vars["valuesof"] = "[]";

            $query = parent::database()->query("produtos", "name = categorias",array("content"));

            $vars["opcoes"] = "";

            if(count($query)>0){

                foreach($query[0]["content"] as $id=>$cat){
                    $vars["opcoes"] .= "<option value='{$id}'>{$cat}</option>";
                }

            }
            //
            // $tiny = json_decode(parent::control("connect/tinyERP")->categorias(),true);
            // foreach($tiny["retorno"] as $cat){
            //     $vars["opcoes"] .= "<option value='{$cat["id"]}'>{$cat["descricao"]}</option>";
            // }

            $content = $this->simple_loader($content, "admin/subcategorias", $vars);
            echo $content->getCode();
        }

        function page_produto($content){
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

            $this->dropzoneUpload("imgprod");

            if(isset($_POST["imgs"])){
                $model = "";

                foreach($_POST["imgs"] as $img){
                    $model .= "<div style='margin: 48px 0px' class='col-lg-4 col-sm-6 col-xs-12'><div class='col-12 row text-center'><label class='col-12'>Cor:</label><div class='col-6 offset-3'><input class=form-control type=text /></div></div><div class='col-12 img' style='background-image:url({$img})'><br /><br /><br /></div><div class='col-12 text-center'><button  class='apagar m-btn text-center m-btn--pill btn-outline-danger btn'><i class='la las la-trash'></i> Apagar</button></div></div>";
                }

                exit("{$model}");
            }

            if($this->post())return $this->ajax_produtos();

            $id = parent::database()->newID($db,"tp = prod");
            $vars = array(
                "acao" => "cadastrado",
                "id" => $id,
                "botao-txt" => "Criar novo produto",
                "TITLE" => "Adicionar Produto",
                "nome" => "",
                "categoria" => "0",
                "subcategoria" => "0",
                "ativo" => true,
                "lancamento" => false,
                "destaque" => false,
                "promocao" => false,
                "imagens" => "[]",
                "valor" => "R$ 0,00",
                "valor-a-vista" => "R$ 0,00",
                "parcelas-sem-juros" => "3",
                "descricao-longa" => "",
                "descricao-curta" => "",
                "largura" => "1",
                "altura" => "1",
                "comprimento" => "1",
                "peso_liq" => "1",
                "peso_bruto" => "1",
                "quantidade_estoque" => "1",
                "sku" => ""
            );

            if(!empty(parent::url(1)) || (string)parent::url(1) == "0"){
                if(count($query = parent::database()->query($db, "id = " . (string)parent::url(1))) > 0){
                    $vars["TITLE"] = "Modificar Produto";
                    $vars["botao-txt"] = "Salvar o que foi modificado";
                    $vars["acao"] = "modificado";

                    foreach($query[0] as $id=>$val){
                        $vars[$id] = is_array($val) ? json_encode($val):$val;
                    }

                    unset($vars[0]);

                } elseif(parent::url(1) == "listar"){
                    $produtos = parent::database()->query($db, "id > -1");

                    // var_dump($produtos);
                    // exit;

                    // $tiny_products = $this->tiny_produtos(-1,2);
                    // $produto = $this->tiny_produto($tiny_products[0]["id"]);
                    // header("Content-Type: text/plain");
                    // print_r($produto);
                    // exit;

                    $btnTxt          = "Produto";
                    $keyword         = "produto";
                    $db              = $produtos;
                    $titulos         = "Nome,Id,SKU";
                    $dados           = "nome,id,sku";
                    $keyid           = "id";
                    $titulo          = "Gerir Produtos da Loja Virtual";

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
                "Fale Conosco" => "/fale-conosco/"
            );

            // $tiny = json_decode(parent::control("connect/tinyERP")->categorias(),true);
            $sep = "/";

            $categorias_ecommerce = $this->page_categorias("",true);
            $subcategorias_ecommerce = $this->page_sub_categorias("",true);

            foreach($categorias_ecommerce as $ind=>$opt){
                $opcoes[ucfirst($opt)] = "/"  . (string)$ind . $sep . $this->slug($opt) . "/";
                foreach(parent::database()->query($subcategorias_ecommerce,"vinculo={$ind}") as $ind2=>$opt2){
                    $opcoes[ucfirst($opt) . " / " . ucfirst($opt2["txt"])] = "/"  . (string)$ind . $sep . $this->slug($opt) . "/"  . (string)$ind2 . $sep . $this->slug($opt2["txt"]) . "/";
                }
            }

            foreach($this->page_categorias(false,true) as $ind=>$opt){
                $opcoes[$opt] = "/"  . (string)$ind . $sep . $this->slug($opt) . "/";
            }

            // foreach($tiny["retorno"] as $cat){
            //     $opcoes[ucfirst($cat["descricao"])] = "/"  . $cat["id"] . $sep . $this->slug($cat["descricao"]) . "/";
            //     foreach($cat["nodes"] as $subcat){
            //         $opcoes[ucfirst($cat["descricao"]) . " / " . ucfirst($subcat["descricao"])] = "/"  . $cat["id"] . $sep . $this->slug($cat["descricao"]) . "/"  . $subcat["id"] . $sep . $this->slug($subcat["descricao"]) . "/";
            //         foreach($subcat["nodes"] as $subcat2){
            //             $opcoes[ucfirst($cat["descricao"]) . " / " . ucfirst($subcat2["descricao"])] = "/"  . $cat["id"] . $sep . $this->slug($cat["descricao"]) . "/"  . $subcat2["id"] . $sep . $this->slug($subcat2["descricao"]) . "/";
            //         }
            //     }
            // }

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

        function page_importar_produtos_tiny_erp($content){
            if(isset($_REQUEST["getProd"])){
                header("Content-Type: application/json");
                exit(json_encode($this->tiny_produto($_REQUEST["getProd"])));
            }

            if($this->post())return $this->ajax_produtos();

            if(!isset($_SESSION["tiny"]) || (abs(strtotime( date("H:i") )-strtotime( $_SESSION["tinyTime"] ))/60%60)>=5){
                $produtos = array();
                for($i = 1; $i <= $this->tiny_paginas(); $i++){
                    $produtos = array_merge($this->tiny_produtos(-1,$i),$produtos);
                }

                $produtos = array_values($produtos);

                $_SESSION["tiny"] = json_encode($produtos);
                $_SESSION["tinyTime"] = date("H:i");
            } else {
                $produtos = json_decode($_SESSION["tiny"], true);
            }

            foreach(array_keys($produtos) as $chave){
                $query = parent::database()->query("produtos", "tp=prod and id={$produtos[$chave]["id"]}");

                if(count($query) > 0){
                    unset($produtos[$chave]);
                } else {
                    $produtos[$chave]["importar"] = "<a href='javascript:;' data-id='{$produtos[$chave]["id"]}' onclick='
                        $.post(\"{myurl}\", {getProd: \"{$produtos[$chave]["id"]}\"}, function(data){
                            $.post(\"{myurl}\", {cadprod: data}, function(success){
                                if(success===true){
                                    successRequest(refresh, \"O produto foi incluido com sucesso!\");
                                } else {
                                    errorRequest(refresh);
                                }
                            });
                        });
                    ' class='botao-importar-produto-tinyerp btn m-btn--hover-accent btn-info m-btn'><i class='la la-save'></i>&nbsp;Importar para Loja</a>";
                }
            }

            $produtos = array_values($produtos);

            $btnTxt          = "";
            $keyword         = "";
            $db              = $produtos;
            $titulos         = "Nome,SKU,Importar";
            $dados           = "nome,sku,importar";
            $keyid           = "id";
            $titulo          = "Importar Produtos da Plataforma Integrada TinyERP";

            exit($this->_tablepage($content,$keyword,$titulos,$dados,$keyid,$titulo,$db,$btnTxt,"not",false,"admin/table_tiny")->getCode());
        }
    }
