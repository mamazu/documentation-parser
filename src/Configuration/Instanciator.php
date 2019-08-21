<?php
declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

class Instanciator {
    public function createFromArray(array $array): object
    {
        $className = array_shift($array);

        return new $className(...$array);
    }
}