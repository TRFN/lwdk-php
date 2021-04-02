<?php
    class application extends APPObject {
        use function_group_sessions, function_group_dates;
        function __construct(){
            # CONFIGURATIONS #
            $this->session_protect();

            $this->rootDir("/");
            // $this->uiTemplateDefault("application");
            header("Content-Type: text/html;charset=utf-8");
        }

        function default_load_page(UITemplate $content, String $layout, Array $vars=array(), Array $models=array()){
            $content->loadScripts();

            $content->setCode($layout);

            $content->applyModels(array("header"));

            $content->loadParentVars();

            $content->applyVars($vars);

            // $content->applyVars(array(
            //     "nivelacesso" => $this->usuario()->nivelacesso
            // ));

            // $content->applyModels(
            //     array_merge(
            //         array(
            //             "menu_lateral" => ("menus/" . trim($this->usuario()->nivelacesso) . (trim($this->usuario()->nivelacesso)=="gestor"?(isset($this->usuario()->criarpesquisas) && $this->usuario()->criarpesquisas=="sim"?"":"-sem-pesquisa"):""))
            //         ), $models
            //     )
            // );

            $content->applyVars(array(
                "extrabody" => "",
                "extrascript" => ""
            ));

            return $content;
        }

        function page_main($content){
            echo $content->getCode();
        }
    }
?>
