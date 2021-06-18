<?php

class Route {
    private $route;
    private $size;
    public $values = [];


    public function __construct($route) {
        $this->route = $route;
        $this->genPattern();
    }


    private function genPattern() {
        $temp = Route::createValuesItems($this->route);
        $this->size = $temp["size"];
        $this->values = $temp["values"];
    }


    public function match($route) {
        $temp = $this->values;
        if(Route::compareStrReg($route, $this->size, $this->route, $temp)) {
            $this->values = $temp;
            return true;
        }
        return false;
    }


    public function contentValues() {
        return sizeof($this->values) > 0;
    }


    public function __toString() {
        return $this->route;
    }


    public function getRoute() {
        return $this->route;
    }


    private static function createValuesItems($str) {
        $items = explode("/", $str);
        $val = [];

        for($item = 0; $item < sizeof($items); $item++) {
            if($items[$item] != "") {
                $data = preg_split("/^:/", $items[$item], null, PREG_SPLIT_OFFSET_CAPTURE);
                if(sizeof($data) > 1) {
                    if(isset($values[$data[1][0]])) {
                        trigger_error("the id '" . $data[1][0] . "' already exists");
                    } else {
                        $val += [$data[1][0] => [
                            "value" => "",
                            "index" => $item
                        ]];
                    }
                }
            }
        }

        return [
            "size" => $item,
            "values" => $val
        ];
    }


    private static function compareStrReg($route, $size, $str, &$values) {
        $items = explode("/", $route);
        if(sizeof($items) != $size) return 0;

        foreach($values as $clave => $valor) {
            $values[$clave]["value"] = $items[$valor["index"]];
            $items[$valor["index"]] = "%";
        }

        $str_explode = explode("/", $str);
        for($item = 0; $item < sizeof($items); $item++) {
            if($str_explode[$item] != $items[$item] && $items[$item] != "%") {
                return 0;
            }
        }

        return 1;
    }
}