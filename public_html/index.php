<?php
    include "lwdk/engine/main.php";

    class sitePrincipal extends APPObject {
        function __construct(){
            # CONFIGURATIONS #

            $this->rootDir("/");
        }

    }

    class siteAdmin extends APPObject {
        function __construct(){
            # CONFIGURATIONS #

            $this->rootDir("/admin/");
        }

        function page_main(){
            echo "success";
        }

        function page_home(){
            echo $this->control("siteAdmin/ui",array("nome"=>"tulio"))->exec();
        }
    }

    class bootsys extends lwdk {
        use route;

        function __construct(){
            $this->https();

            $this->addApp(new siteAdmin, $this);
            $this->addApp(new sitePrincipal, $this, true);

            $this->renderApp();
        }
    }

    new bootsys;
?>
