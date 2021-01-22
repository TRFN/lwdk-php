<?php
    class siteAdmin extends APPObject {
        function __construct(){
            # CONFIGURATIONS #

            $this->rootDir("/admin/");
            $this->uiTemplateDefault("siteTemplate");
        }

        function page_main($content){


            $vars = array();
            $vars["string_teste"] = "Tulio <a href='/admin/dandara' ajax>Go to Danda</a>";
            $vars["TITLE"] = "Tulio Page's";

            $models = array();
            $models["btn"] = "site/button";

            $content->loadScripts();
            $content->setCode("site/home");
            $content->applyModels($models);
            $content->applyVars($vars);

            echo $content->getCode();
        }

        function page_dandara($content){
            $vars = array();
            $vars["string_teste"] = "Dandara <a href='/admin/' ajax>Go to Tulio</a>";
            $vars["TITLE"] = "Dandara Page's";

            $models = array();
            $models["btn"] = "site/button";

            $content->loadScripts();
            $content->setCode("site/home");
            $content->applyModels($models);
            $content->applyVars($vars);

            echo $content->getCode();
        }
    }
?>
