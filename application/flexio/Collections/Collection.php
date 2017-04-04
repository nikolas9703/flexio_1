<?php

namespace Flexio\Collections;

class Collection
{
    protected $scope;

    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    public function __get($property)
	{
        if (method_exists($this, $property)){
			return call_user_func([$this,$property]);
		}
		$message = '%s does not respond to the "%s" property or method.';
		throw new \Exception(sprintf($message, static::class, $property));
	}
}
