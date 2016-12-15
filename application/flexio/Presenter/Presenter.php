<?php namespace Flexio\Presenter;

abstract class Presenter {
	/**
	 * @var mixed
	 */
	protected $entity;
	/**
	 * @param $entity
	 */
	function __construct($entity)
	{
		$this->entity = $entity;
	}
	/**
	 * Allow for property-style retrieval
	 *
	 * @param $property
	 * @return mixed
	 */
	public function __get($property)
	{

		if (method_exists($this, $property))
		{
			return call_user_func([$this,$property]);
		}
		$message = '%s does not respond to the "%s" property or method.';
		throw new \Exception(sprintf($message, static::class, $property));
	}
}
