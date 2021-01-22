<?php
    include "lwdk/engine/main.php";

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
