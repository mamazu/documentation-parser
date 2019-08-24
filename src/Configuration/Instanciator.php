<?php

declare(strict_types=1);

namespace Mamazu\DocumentationParser\Configuration;

class Instanciator
{
    /**
     * @return object
     */
    public static function createFromArray(array $array)
    {
        $className = array_shift($array);
        foreach ($array as &$parameter) {
            if (is_array($parameter)) {
                $parameter = Instanciator::createFromArray($parameter);
            }
        }

        return new $className(...$array);
    }
}
