<?php
declare(strict_types=1);

namespace Core;

// construit les objets et injecte leurs dépendances via la réflexion PHP
class Container
{
    private array $bindings = [];

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function make(string $class): object
    {
        if (isset($this->bindings[$class])) {
            return $this->bindings[$class]($this);  // binding explicite (voir public/index.php)
        }

        return $this->resolve($class);  // sinon construction automatique par réflexion
    }

    private function resolve(string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type === null || $type->isBuiltin()) {
                throw new \RuntimeException("Paramètre non résoluble : {$param->getName()}");
            }
            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
