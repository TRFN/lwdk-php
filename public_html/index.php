<?php
    include "lwdk/engine/main.php";

    class sitePrincipal extends APPObject {
        function __construct(){
            $this->rootDir("/");
        }
    }

    class siteAdmin extends APPObject {
        function __construct(){
            $this->rootDir("/admin/");
            echo $this->control("siteAdmin/ui",array("nome"=>"tulio"))->exec();
        }
    }

    class bootsys extends lwdk {
        use route;

        function __construct(){
            $this->https();

            $this->addApp(new siteAdmin);
            $this->addApp(new sitePrincipal, true);

            $this->selectApp();
            $this->helloWorld();
        }
    }

    new bootsys;
?>
