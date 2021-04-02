<?php
    class UITemplate {
        private $code = "";
        private $template = "";
        private $parent = null;
        private $object = null;
        public  $scriptAddOns = "";
        public  $minify = true;

        function __construct(APPObject $parent){
            $this->parent = $parent;
        }

        function loadScripts(){
            $this->applyVars(array("LWDK::AJAX"=>__dinamicJS::ajaxCore()));
            $this->applyVars(array("LWDK::JSINIT"=>__dinamicJS::initScripts()));
        }

        function uiTemplate(String $set){
            $this->parent->uiTemplate($set);
            $this->reset();
        }

        function reset(){
            $this->setTemplate(is_bool($this->parent->uiTemplate) && !$this->parent->uiTemplate ? $this->parent->defaultUiTemplate : $this->parent->uiTemplate);
        }

        function minifyCode($code){
            if($this->minify === false){
                return $code;
            }

            $Search = array(
                '/(\n|^)(\x20+|\t)/',
                '/(\n|^)\/\/(.*?)(\n|$)/',
                '/\n/',
                '/\<\!--.*?-->/',
                '/(\x20+|\t)/', # Delete multispace (Without \n)
                '/\>\s+\</', # strip whitespaces between tags
                '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
                '/=\s+(\"|\')/'); # strip whitespaces between = "'

               $Replace = array(
                "\n",
                "\n",
                " ",
                "",
                " ",
                "><",
                "$1>",
                "=$1");

            return preg_replace($Search,$Replace,$code);

            // return $code;
        }

        function applyVars(Array $vars){
            foreach($vars as $key => $value){
                if(!is_array($value)){
                    $this->code = explode("{{$key}}", $this->code);
                    $this->code = implode($value, $this->code);
                    $this->template = explode("{{$key}}", $this->template);
                    $this->template = implode($value, $this->template);
                }
            }
        }

        function loadParentVars(){
            $this->applyVars($this->parent->defaultVars);
        }

        function applyModels(Array $load){
            foreach($load as $key => $value){
                if(!preg_match("/[^0-9]/",(string)$key)){
                    $key = $value;
                }
                $this->code = explode("{{$key}}", $this->code);
                $this->code = implode($l=file_get_contents($this->parent->parent()->path->models . "/{$value}.html"), $this->code);
                $this->template = explode("{{$key}}", $this->template);
                $this->template = implode($l, $this->template);
            }
        }
        function getCode(){
            return $this->minifyCode(isset($_REQUEST["ajax"])?$this->code:implode($this->code,explode("{PAGE_CONTENT}", $this->template)));
        }
        function setCode($code){
            $this->code = file_get_contents($this->parent->parent()->path->layouts . "/{$code}.html");
            $this->code .= "<script lwdk-addons>document.addEventListener('DOMContentLoaded',fn=function(){{$this->scriptAddOns};document.title=`{TITLE} | Agencia Hetsi - Painel Administrativo`.toUpperCase();$('#m_aside_left_close_btn').click()});try{fn();}catch(e){}</script>";
        }
        function setTemplate($code){
            $this->template = $code;
        }
    }
?>
