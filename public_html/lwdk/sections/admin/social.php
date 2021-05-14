<?php
    trait admin_social {
        private function ajax_social($key=""){
            if(empty($key)){
                exit("false");
            }
            try{
                header("Content-Type: application/json");
                $this->database()->set("social",$key,$_POST);
            } catch(Exception $e){
                exit("false");
            }
            exit("true");
        }

        function page_contatos($content){
            $content->minify = true;

            $section = "contatos";
            $title   = "Contatos";

            if($this->post())return $this->ajax_social($section);

            $content = $this->simple_loader($content, "admin/contatos", array(
                "TITLE"=>$title,
                "valuesof" => json_encode($this->database()->get("social",$section))
            ));

            echo $content->getCode();
        }

        function page_slidehome($content){
            $content->minify = true;

            $section = "slideshow";

            $this->dropzoneUpload("slideshow", false, "not-resize");

            if(isset($_POST["imgs"])){
                $model = "";

                foreach($_POST["imgs"] as $img){
                    $model .= "
                            <div class='col-3 text-center slide'>
                                <label class='col-12'>Titulo:
                                    <input class='form-control form-control-sm' type=text />
                                </label>

                                <label class='col-12'>Descrição:
                                    <input class='form-control form-control-sm' type=text />
                                </label>

                                <label class='col-12'>URL/Link:
                                    <input class='form-control form-control-sm' type=text />
                                </label>

                                <label class='col-12'>Texto Bot&atilde;o
                                    <input class='form-control form-control-sm' type=text />
                                    <input type=hidden value='{$img}' />
                                </label>

                                <div class='col-12 img' style='background-image:url({$img})'>
                                    <br /><br /><br />
                                </div>
                                <div class='col-12 text-center'>
                                    <button  class='apagar m-btn text-center m-btn--pill btn-outline-danger btn'>
                                        <i class='la las la-trash'></i> Apagar
                                    </button>
                                </div>
                            </div>";
                }

                exit("{$model}");
            }

            if($this->post())return $this->ajax_social($section);

            $content = $this->simple_loader($content, "admin/slideshow", array(
                "valuesof" => json_encode($this->database()->get("social",$section))
            ));

            echo $content->getCode();
        }

        function page_logotipo($content){
            $content->minify = true;

            $section = "logotipo";

            $this->dropzoneUpload("images", false, "not-resize");

            if(isset($_POST["imgs"])){
                $model = "";

                foreach($_POST["imgs"] as $img){
                    $model .= "
                    <div class='col-12 text-center'>
                        <input type=hidden id=img value='{$img}' />
                        <div class='col-12 img' style='background-image:url(/{$img})'>
                            <br /><br /><br />
                        </div>
                        <div class='col-12 text-center'>
                            <button  class='apagar m-btn text-center m-btn--pill btn-outline-danger btn'>
                                <i class='la las la-trash'></i> Apagar
                            </button>
                        </div>
                    </div>";
                }

                exit("{$model}");
            }

            if($this->post())return $this->ajax_social($section);

            $content = $this->simple_loader($content, "admin/logo", array(
                "valuesof" => json_encode($this->database()->get("social",$section))
            ));

            echo $content->getCode();
        }

    }
