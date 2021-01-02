<?php
class __paths {
    public static function get(){
        $pathRoute = new stdClass;
        $pathRoute->root = dirname(dirname(dirname(__FILE__)));
        $pathRoute->apps = "{$pathRoute->root}/apps";
        $pathRoute->controls = "{$pathRoute->root}/controls";
        $pathRoute->database = "{$pathRoute->root}/database";
        $pathRoute->engine = "{$pathRoute->root}/engine";
        $pathRoute->languages = "{$pathRoute->root}/languages";
        $pathRoute->layouts = "{$pathRoute->root}/layouts";
        $pathRoute->models = "{$pathRoute->root}/models";
        $pathRoute->plugins = "{$pathRoute->root}/plugins";

        return $pathRoute;
    }
}
?>
