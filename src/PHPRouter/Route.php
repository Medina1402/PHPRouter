<?php

namespace PHPRouter;

/**
 * Path available to register to main router (static or dynamic)
 *
 * @example Path Static: /a/b/c
 * @example Path Dynamic: /a/:b/:c -> dynamic values is "b" and "c"
 *
 * @author Abraham Medina Carrillo <https://github.com/medina1402>
 */
 class Route
{
     /**
      * URL registered for the path
      * @var string
      */
    private string $path;

     /**
      * Path size, separated for the "/" character
      * @var int
      */
    private int $size;

     /**
      * Keys and values for dynamic path
      * @var array
      */
    private array $values;

     /**
      * Register a route and keys if it is a dynamic route
      * @param string $path
      */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->createPathValues();
    }

     /**
      * Provide all dynamic values that contain the registered route
      * @return array
      */
     public function getValues(): array
     {
         return $this->values;
     }

     /**
      * Verify that the registered route contains dynamic values
      * @return bool
      */
     public function contentValues(): bool
     {
         return sizeof($this->values) > 0;
     }

     /**
      * Current path
      * @return string
      */
     public function getPath(): string
     {
         return $this->path;
     }

     /**
      * Current path
      * @return string
      */
     public function __toString(): string
     {
         return $this->path;
     }

     /**
      * Compare if the current route is equal to the registered route
      * @param string $path
      * @return bool
      */
    public function match(string $path): bool
    {
        return $this->compareStrRegex($path);
    }

     /**
      * Extract values from dynamic route
      * @return void
      */
    private function createPathValues(): void
    {
        $items = explode("/", $this->path);
        $this->values = [];
        $this->size = sizeof($items);

        for ($item = 0; $item < $this->size; $item++) {
            if ($items[$item] != '') {
                $data = preg_split("/^:/", $items[$item], null, PREG_SPLIT_OFFSET_CAPTURE);
                if(sizeof($data) > 1) {
                    if(isset($values[$data[1][0]])) trigger_error("the id '" . $data[1][0] . "' already exists");
                    else $this->values += [$data[1][0] => ["value" => "", "index" => $item]];
                }
            }
        }
    }

     /**
      * Compare current path with path registered in method, extracted values for keys (dynamic)
      * @param string $path_compare
      * @return bool
      */
    private function compareStrRegex(string $path_compare): bool
    {
        $items = explode("/", $path_compare);
        if (sizeof($items) != $this->size) return false;

        foreach($this->values as $clave => $valor) {
            $this->values[$clave]["value"] = $items[$valor["index"]];
            $items[$valor["index"]] = "%";
        }

        $path_explode = explode("/", $this->path);
        for($item = 0; $item < sizeof($items); $item++) {
            if($path_explode[$item] != $items[$item] && $items[$item] != "%") {
                return false;
            }
        }

        return true;
    }

 }