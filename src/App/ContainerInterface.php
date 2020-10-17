<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Closure;
    use Sourcegr\Framework\App\Container\BindingResolutionException;


    interface ContainerInterface extends \Psr\Container\ContainerInterface
    {

        /**
         * Determine if the given abstract type has been bound.
         *
         * @param string $abstract
         *
         * @return bool
         */
        public function bound($abstract);

              /**
         *  {@inheritdoc}
         */
        public function has($id);

        /**
         * Determine if the given abstract type has been resolved.
         *
         * @param string $abstract
         *
         * @return bool
         */
        public function resolved($abstract);

        /**
         * Determine if a given type is shared.
         *
         * @param string $abstract
         *
         * @return bool
         */
        public function isShared($abstract);

        /**
         * Determine if a given string is an alias.
         *
         * @param string $name
         *
         * @return bool
         */
        public function isAlias($name);

        /**
         * Register a binding with the container.
         *
         * @param string               $abstract
         * @param Closure|string|null $concrete
         * @param bool                 $shared
         *
         * @return void
         */
        public function bind($abstract, $concrete = null, $shared = false);

        /**
         * Determine if the container has a method binding.
         *
         * @param string $method
         *
         * @return bool
         */
        public function hasMethodBinding($method);

        /**
         * Bind a callback to resolve with Container::call.
         *
         * @param array|string $method
         * @param Closure     $callback
         *
         * @return void
         */
        public function bindMethod($method, $callback);

        /**
         * Get the method binding for the given method.
         *
         * @param string $method
         * @param mixed  $instance
         *
         * @return mixed
         */
        public function callMethodBinding($method, $instance);

        /**
         * Add a contextual binding to the container.
         *
         * @param string          $concrete
         * @param string          $abstract
         * @param Closure|string $implementation
         *
         * @return void
         */
        public function addContextualBinding($concrete, $abstract, $implementation);

        /**
         * Register a binding if it hasn't already been registered.
         *
         * @param string               $abstract
         * @param Closure|string|null $concrete
         * @param bool                 $shared
         *
         * @return void
         */
        public function bindIf($abstract, $concrete = null, $shared = false);

        /**
         * Register a shared binding in the container.
         *
         * @param string               $abstract
         * @param Closure|string|null $concrete
         *
         * @return void
         */
        public function singleton($abstract, $concrete = null);

        /**
         * Register a shared binding if it hasn't already been registered.
         *
         * @param string               $abstract
         * @param Closure|string|null $concrete
         *
         * @return void
         */
        public function singletonIf($abstract, $concrete = null);

        /**
         * "Extend" an abstract type in the container.
         *
         * @param string   $abstract
         * @param Closure $closure
         *
         * @return void
         *
         * @throws \InvalidArgumentException
         */
        public function extend($abstract, Closure $closure);

        /**
         * Register an existing instance as shared in the container.
         *
         * @param string $abstract
         * @param mixed  $instance
         *
         * @return mixed
         */
        public function instance($abstract, $instance);

        /**
         * Assign a set of tags to a given binding.
         *
         * @param array|string $abstracts
         * @param array|mixed  ...$tags
         *
         * @return void
         */
        public function tag($abstracts, $tags);

        /**
         * Resolve all of the bindings for a given tag.
         *
         * @param string $tag
         *
         * @return iterable
         */
        public function tagged($tag);

        /**
         * Alias a type to a different name.
         *
         * @param string $abstract
         * @param string $alias
         *
         * @return void
         *
         * @throws \LogicException
         */
        public function alias($abstract, $alias);

        /**
         * Bind a new callback to an abstract's rebind event.
         *
         * @param string   $abstract
         * @param Closure $callback
         *
         * @return mixed
         */
        public function rebinding($abstract, Closure $callback);

        /**
         * Refresh an instance on the given target and method.
         *
         * @param string $abstract
         * @param mixed  $target
         * @param string $method
         *
         * @return mixed
         */
        public function refresh($abstract, $target, $method);

        /**
         * Wrap the given closure such that its dependencies will be injected when executed.
         *
         * @param Closure $callback
         * @param array    $parameters
         *
         * @return Closure
         */
        public function wrap(Closure $callback, array $parameters = []);

        /**
         * Call the given Closure / class@method and inject its dependencies.
         *
         * @param callable|string      $callback
         * @param array<string, mixed> $parameters
         * @param string|null          $defaultMethod
         *
         * @return mixed
         *
         * @throws \InvalidArgumentException
         */
        public function call($callback, array $parameters = [], $defaultMethod = null);

        /**
         * Get a closure to resolve the given type from the container.
         *
         * @param string $abstract
         *
         * @return Closure
         */
        public function factory($abstract);

        /**
         * An alias function name for make().
         *
         * @param string $abstract
         * @param array  $parameters
         *
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        public function makeWith($abstract, array $parameters = []);

        /**
         * Resolve the given type from the container.
         *
         * @param string $abstract
         * @param array  $parameters
         *
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        public function make($abstract, array $parameters = []);

        /**
         *  {@inheritdoc}
         */
        public function get($id);

        /**
         * Instantiate a concrete instance of the given type.
         *
         * @param Closure|string $concrete
         *
         * @return mixed
         *
         * @throws BindingResolutionException
         */
        public function build($concrete);

        /**
         * Register a new resolving callback.
         *
         * @param Closure|string $abstract
         * @param Closure|null   $callback
         *
         * @return void
         */
        public function resolving($abstract, Closure $callback = null);

        /**
         * Register a new after resolving callback for all types.
         *
         * @param Closure|string $abstract
         * @param Closure|null   $callback
         *
         * @return void
         */
        public function afterResolving($abstract, Closure $callback = null);

        /**
         * Get the container's bindings.
         *
         * @return array
         */
        public function getBindings();

        /**
         * Get the alias for an abstract if available.
         *
         * @param string $abstract
         *
         * @return string
         */
        public function getAlias($abstract);

        /**
         * Remove all of the extender callbacks for a given type.
         *
         * @param string $abstract
         *
         * @return void
         */
        public function forgetExtenders($abstract);

        /**
         * Remove a resolved instance from the instance cache.
         *
         * @param string $abstract
         *
         * @return void
         */
        public function forgetInstance($abstract);

        /**
         * Clear all of the instances from the container.
         *
         * @return void
         */
        public function forgetInstances();

        /**
         * Flush the container of all bindings and resolved instances.
         *
         * @return void
         */
        public function flush();

        /**
         * Get the globally available instance of the container.
         *
         * @return static
         */
        public static function getInstance();

        /**
         * Set the shared instance of the container.
         *
         * @param ContainerInterface|null $container
         *
         * @return ContainerInterface|static
         */
        public static function setInstance(ContainerInterface $container = null);

        /**
         * Determine if a given offset exists.
         *
         * @param string $key
         *
         * @return bool
         */
        public function offsetExists($key);

        /**
         * Get the value at a given offset.
         *
         * @param string $key
         *
         * @return mixed
         */
        public function offsetGet($key);

        /**
         * Set the value at a given offset.
         *
         * @param string $key
         * @param mixed  $value
         *
         * @return void
         */
        public function offsetSet($key, $value);

        /**
         * Unset the value at a given offset.
         *
         * @param string $key
         *
         * @return void
         */
        public function offsetUnset($key);

        /**
         * Dynamically access container services.
         *
         * @param string $key
         *
         * @return mixed
         */
        public function __get($key);

        /**
         * Dynamically set container services.
         *
         * @param string $key
         * @param mixed  $value
         *
         * @return void
         */
        public function __set($key, $value);
    }