<?php
    class UITemplate {
        private $code = "";
        private $template = "";
        private $parent = null;

        function __construct(APPObject $parent){
            $this->parent = $parent;
        }

        function loadScripts(){
            $this->applyVars(array("LWDK::AJAX"=>__dinamicJS::ajaxCore()));
        }

        function applyVars(array $vars){
            foreach($vars as $key => $value){
                $this->code = explode("{{$key}}", $this->code);
                $this->code = implode($value, $this->code);
                $this->template = explode("{{$key}}", $this->template);
                $this->template = implode($value, $this->template);
            }
        }
        function applyModels(array $load){
            foreach($load as $key => $value){
                $this->code = explode("{{$key}}", $this->code);
                $this->code = implode($l=file_get_contents((new __paths)->get()->models . "/{$value}.html"), $this->code);
                $this->template = explode("{{$key}}", $this->template);
                $this->template = implode($l, $this->template);
            }
        }
        function getCode(){
            return isset($_POST["ajax"])?$this->code:implode($this->code,explode("{PAGE_CONTENT}", $this->template));
        }
        function setCode($code){
            $this->code = file_get_contents((new __paths)->get()->layouts . "/{$code}.html");
            $this->code .= "<script lwdk-addons>document.title=`{TITLE}`;</script>";
        }
        function setTemplate($code){
            $this->template = $code;
        }
    }
?>
