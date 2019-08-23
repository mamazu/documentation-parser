<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

class Instanciator {
    /**
     * @return object
     */
    public static function createFromArray(array $array)
    {
        $className = array_shift($array);

        return new $className(...$array);
    }
}