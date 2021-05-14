<?php
    class admin_panel extends APPObject {
        use
            function_group_dates,
            admin_users,
            admin_textos,
            admin_produtos,
            admin_clientes,
            admin_social;


        function __construct(){
            # CONFIGURATIONS #
            $this->rootDir("/admin/");
            $this->uiTemplateDefault("admin/application");
            header("Content-Type: text/html;charset=utf-8");
            $this->empresa = "CMM Metais";
        }

        function _tablepage($content,$keyword,$titulos,$__dados,$keyid,$titulo,$db,$txtBtn,$filtro="not",$acoes=true,$layout="admin/tables"){

                $dados = explode(",",$titulos);

                $thead = (("<th style='text-transform: uppercase;'>" . implode("</th><th style='text-transform: uppercase;'>", $dados) . "</th>") . ($acoes?"<th  style='text-transform: uppercase;' style='min-width: 100px;'>a&ccedil;&otilde;es</th>":""));

                $dados = explode(",",$__dados);

                $tbody = "";

                $botao_apagar = (function($id,$keyword,$txtBtn){
                    return '<a href="javascript:;" onclick="Swal.fire({
                                        title: ``,
                                        html: `Voc&ecirc; deseja mesmo apagar o(a) ' . $txtBtn . '?! <br>Essa a&ccedil;&atilde;o &eacute; irrevers&iacute;vel!`,
                                        icon: `warning`,
                                        showCancelButton: true,
                                        confirmButtonColor: `#3085d6`,
                                        cancelButtonColor: `#d33`,
                                        confirmButtonText: `Sim, apagar`,
                                        cancelButtonText: `Cancelar`,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire(
                                                ``,
                                                ` ' . ucfirst($txtBtn) . ' apagado(a) com sucesso!`,
                                                `success`
                                            ).then((result) => {
                                                $.post(`{URLPrefix}/' . $keyword . '/' . $id . '/apagar/`, function(){setTimeout(refresh,500);});
                                            });
                                        }
                                    });" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Apagar"><i class="la la-trash"></i></a>';
                });

                $botao_apagar_desabilitado = '<button class="m-portlet__nav-link btn m-btn m-btn--hover-light  m-btn--icon m-btn--icon-only m-btn--pill" onclick="swal.fire(``,`Desculpe, mas voc&ecirc; n&atilde;o possui privil&eacute;gios para apagar usuarios.`, `error`);" title="Voce nao pode deletar este usuario"><i class="la la-trash"></i></button>';

                $query = $filtro == -1 ? $db : ($filtro == "not" ? parent::database()->getAll($db):parent::database()->query($db,$filtro));

                foreach($query as $_dado){
                    $dado = array();
                    foreach($dados as $campo){
                        if(isset($_dado[$campo]) && !empty($_dado[$campo])){
                            $dado[] = ($_dado[$campo]);
                        } else {
                            $dado[] = "&ndash;";
                        }
                    }

                    if($acoes){
                        if(!isset($_dado[$keyid])){
                            $_dado[$keyid] = "";
                        }
                        $dado[] = '<a href="/'.$keyword.'/' . $_dado[$keyid] . '/" ajax=on class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Editar"><i class="la la-edit"></i></a>' . $botao_apagar($_dado[$keyid],$keyword,$txtBtn);
                    }

                    $tbody .= "<tr><td><span style='display: inline-block;overflow-wrap: break-word;word-wrap: break-word;hyphens: auto;max-width: 400px; text-align: center;'>" . implode("</span></td><td><span style='display: inline-block;overflow-wrap: break-word;word-wrap: break-word;hyphens: auto;max-width: 400px;text-align: center;'>", $dado) . "</span></td></tr>";
                }

                return $this->simple_loader($content, $layout, array(
                    "TITLE"=>$titulo,
                    "thead" => $thead,
                    "tbody" => $tbody,
                    "link-add" => "/{$keyword}/",
                    "text-add" => "Adicionar " . $txtBtn,
                    "extrascript" => ""
                ), array(
                "extrabody" => strlen($keyword)>0?"admin/botao_adicionar":"empty"));
        }

        function _admin_template_($content){
            $content->applyModels(array(
                "menu_lateral" => "admin/menu",
                "header" => "admin/header"
            ));

            $vars = (array(
                "logotipo" => "/images/logo.png",
                "TITLE" => "Painel Admin",
                "empresa" => $this->empresa
            ));

            foreach($this->admin_sessao() as $chave=>$valor){
                $vars["sessao-{$chave}"] = $valor;
            }

            $content->applyVars($vars);

            return $content;
        }

        function page_main($content){
			$content->minify = false;
            // $vars   = array("TITLE" => "Inicio - Painel Admin");
            // $models = array();
			//
            // $layout = "admin/home";
			//
			//
			//
            // echo $this->simple_loader($content, $layout, $vars, $models)->getCode();

			$vendas = parent::control("connect/pagseguro");

			if($this->post() and isset($_POST["changeStatus"]) and is_array($_POST["changeStatus"])){
				$vendas->updNotif($_POST["changeStatus"]["id"], array("{$_POST["changeStatus"]["set"]}" => $_POST["changeStatus"]["status"]));
				exit(json_encode($_POST["changeStatus"]));
			}

			$tabela1 = array();
			$tabela2 = array();

			foreach($vendas->lerNotif() as $notif){
				$cli = parent::database()->query("clientes","id = {$notif["cliente_id"]}", array("nome","tel","email","endereco"));
				$cli = $cli[0];

				$optional_char = "%";

				$notif["cliente_cep"]      = vsprintf("%s%s.%s%s%s-%s%s%s", str_split($notif["cliente_cep"]));
				$notif["cliente_cep"]      = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_cep"]);
				$notif["cliente_endereco"] = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_endereco"]);
				$notif["cliente_numero"]   = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_numero"]);
				$notif["cliente_cidade"]   = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_cidade"]);
				$notif["cliente_estado"]   = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_estado"]);
				$notif["cliente_bairro"]   = preg_replace("/[^A-z0-9]/", "{$optional_char}", $notif["cliente_bairro"]);

				$query = "cep = %{$notif["cliente_cep"]}% and rua = %{$notif["cliente_endereco"]}% and numero = %{$notif["cliente_numero"]}% and bairro = %{$notif["cliente_bairro"]}% and cidade = %{$notif["cliente_cidade"]}% and estado = %{$notif["cliente_estado"]}%";

				$cli["endereco"] = parent::database()->query($cli["endereco"], $query);
				if(count($cli["endereco"])){
					$cli["endereco"] = $cli["endereco"][0];

					$cli["endereco"] = ["{$cli["endereco"]["rua"]} {$cli["endereco"]["numero"]}, {$cli["endereco"]["bairro"]}", "{$cli["endereco"]["cidade"]} ({$cli["endereco"]["estado"]})", $cli["endereco"]["cep"]];

					$cli["endereco"] = "{$cli["endereco"][0]}<br>{$cli["endereco"][1]} - {$cli["endereco"][2]}";
				} else {
					$cli["endereco"] = "Endereço não informado.";
				}

				$cli["entrega"] = $notif["frete"] === "0,00"? "Retirada na Loja":"Encomenda PAC <br><br><b>ENDERE&Ccedil;O:</b><br> {$cli["endereco"]}"; // =]

				$cli["tel"] = preg_replace("/[-]/","",$cli["tel"]);

				$prodstbl2 = "";

				$____dado = "entregue";

				$recebido = '<b>ENTREGUE AO CLIENTE:</b><br><hr><br><input data-switch="true" data-value="'.(isset($notif[$____dado]) ? $notif[$____dado]:"false").'" type="checkbox" onchange="if(!$(this).data(\'notexec\')){bs=$(this).bootstrapSwitch(\'state\'); v = bs?\'true\':\'false\'; $.post(LWDKLocal, {changeStatus: {id: \''.$notif["@ID"].'\', status: v, set:\''.$____dado.'\'}}, function(data){refresh()});}"><br><br><br>';

				$____dado = "nf";

				$recebido .= '<b>NF-e EMITIDA:</b><br><hr><br><input data-switch="true" data-value="'.(isset($notif[$____dado]) ? $notif[$____dado]:"false").'" type="checkbox" onchange="if(!$(this).data(\'notexec\')){bs=$(this).bootstrapSwitch(\'state\'); v = bs?\'true\':\'false\'; $.post(LWDKLocal, {changeStatus: {id: \''.$notif["@ID"].'\', status: v, set:\''.$____dado.'\'}}, function(data){refresh()});}"><br><br><br>';

				$____dado = "enviado";

				$recebido .= '<b>PROD. ENVIADO:</b><br><hr><br><input data-switch="true" data-value="'.(isset($notif[$____dado]) ? $notif[$____dado]:"false").'" type="checkbox" onchange="if(!$(this).data(\'notexec\')){bs=$(this).bootstrapSwitch(\'state\'); v = bs?\'true\':\'false\'; $.post(LWDKLocal, {changeStatus: {id: \''.$notif["@ID"].'\', status: v, set:\''.$____dado.'\'}}, function(data){refresh()});}"><br><br><br>';

				$notif["desconto_ou_adicional"] = substr($notif["desconto_ou_adicional"], 1);

				$pagamento = "<div style='text-align: left;'><b>FORMA DE PAGAMENTO:</b><br>{$notif["forma_pagamento"]["html"]}<br><br><b>STATUS:</b><br> {$notif["status"]}</div><br><hr><br>"."<div style='text-align: left;'><b>VALOR TOTAL:</b><br>R$ {$notif["valor_total"]}<br><br><b>FRETE:</b><br> R$ {$notif["frete"]}<br><br><b>DESCONTO:</b><br> R$ {$notif["desconto_ou_adicional"]}<br><br><b>PARCELADO:</b><br> x{$notif["parcelas_pgto"]}</div>";

				$cliente = "<div style='text-align: left;'><b>CLIENTE:</b> <br>{$cli["nome"]}<br><br><b>TEL:</b><br> {$cli["tel"]}<br><br><b>EMAIL:</b><br> {$cli["email"]}<br><br><b>ENTREGA:</b><br>{$cli["entrega"]}</div>";

				foreach($notif["produtos"] as $prod){
					$sku = parent::database()->query("produtos","id={$prod["id"]}","sku");
					$sku = $sku[0];
					$prodstbl2 .= "<b>{$prod["qtd"]}</b> x ({$prod["nome"]})<br><b>SKU:</b> {$sku}<br><b>PRECO(UNIT):</b> R$ {$prod["valor"]}<br><hr><br>";
					$tabela1[] = array(
						"id" => $notif["@ID"],
						"data" => $notif["data-hora"],
						"produto"=>"<div style='text-align: left;'><b>SKU:</b><br>{$sku}<br><br><b>PRODUTO:</b><br> {$prod["nome"]}<br><br><b>VALOR(UNIT):<br></b> R$ {$prod["valor"]}<br><br><b>QTD:</b> <br>{$prod["qtd"]}</div>",
						"cliente"=>$cliente,
						"pagamento"=>$pagamento,
						"produto_recebido" => $recebido
					);
				}

				$tabela2[] = array(
					"id" => $notif["@ID"],
					"data" => $notif["data-hora"],
					"produto"=>"<div style='text-align: left;'>{$prodstbl2}</div>",
					"cliente"=>$cliente,
					"pagamento"=>$pagamento,
					"produto_recebido" => $recebido
				);

			}

			$layout = "";

			$btnTxt          = "";
            $keyword         = "";
            $db              = $tabela2;
            $titulos         = "Data,Cliente,Produtos,Pagamento,Status Interno";
            $dados           = "data,cliente,produto,pagamento,produto_recebido";
            $keyid           = "id";
            $titulo          = "Vendas Realizadas (por cliente/venda)";

			$layout .= ($this->_tablepage($content,$keyword,$titulos,$dados,$keyid,$titulo,$db,$btnTxt,"not",false)->getCode(true));

			$btnTxt          = "";
            $keyword         = "";
            $db              = $tabela1;
            $titulos         = "Data,Produto,Cliente,Pagamento,Status Interno";
            $dados           = "data,produto,cliente,pagamento,produto_recebido";
            $keyid           = "id";
            $titulo          = "Vendas Realizadas (por produto)";

            $layout .= ($this->_tablepage($content,$keyword,$titulos,$dados,$keyid,$titulo,$db,$btnTxt,"not",false)->getCode(true));

			echo $this->simple_loader($content, "empty", array("html"=>$layout,"TITLE"=>"Vendas"))->getCode();
        }
    }
?>
