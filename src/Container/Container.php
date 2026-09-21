<?php

namespace App\Container;

class Container
{
    // Maps a class/interface name to a clousure that knows how to built it
    private array $bindings = [];

    // Cache of already-built object, so we don't need to build the thing twice
    private array $instances = [];

    public function bind(string $abstract, \Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function get(string $abstract): object
    {
        // Return the cached instance if we already built one
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // If we have an explicit binding (e.g. interface -> concrete class), use it to build the object
        if (isset($this->bindings[$abstract])) {
            $object = ($this->bindings[$abstract])($this);
            $this->instances[$abstract] = $object;
            return $object;
        }

        // Otherwise, try to build it automatically via reflection
        $object = $this->autoResolve($abstract);
        $this->instances[$abstract] = $object;
        return $object;
    }

    private function autoResolve(string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        // No constructor means no dependencies - just create it directly
        if ($constructor === null) {
            return new $class();
        }

        // Get the parameters for the constructor
        $parameters = $constructor->getParameters();
        $dependencies = [];

        // Resolve each parameter
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                throw new \RuntimeException(
                    "Cannot auto-resolve parameter '{$parameter->getName()}' in {$class} - no type hint or buil-in type given"
                );
            }

            // Recursively resolve each constructor dependency
            $dependencies[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
