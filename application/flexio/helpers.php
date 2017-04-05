<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;


if (! function_exists('round_up')) {
    /**
     * retorna el numero redondeado mayor igual a 5.
     *
     * @param  string  $numero
     * @param  integer   $presicion
     * @return float
     */
    function round_up($numero = 0, $presicion = 2)
    {
        return round($numero, $presicion, PHP_ROUND_HALF_UP);
    }
}



if (! function_exists('round_down')) {
    /**
     * retorna el numero redondeado menor igual a 5.
     *
     * @param  string  $make
     * @param  integer   $presicion
     * @return float
     */
    function round_down($numero = 0, $presicion = 2)
    {
    	return round($numero, $presicion, PHP_ROUND_HALF_DOWN);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $make
     * @param  array   $parameters
     * @return mixed|\Illuminate\Foundation\Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}


if (! function_exists('elixir')) {
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @param  string  $buildDirectory
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    function elixir($file, $buildDirectory = 'build')
    {
        static $manifest;
        static $manifestPath;

        if (is_null($manifest) || $manifestPath !== $buildDirectory) {
            $manifest = json_decode(file_get_contents(public_path($buildDirectory.'/rev-manifest.json')), true);

            $manifestPath = $buildDirectory;
        }

        if (isset($manifest[$file])) {
            return '/'.$buildDirectory.'/'.$manifest[$file];
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if (! function_exists('validator')) {
    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $factory = app(ValidationFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}