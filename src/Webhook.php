<?php

namespace VK;

class Webhook
{
	private $hooks;

	public function __construct()
	{
		$this->hooks = [];
	}

	private function getCallbacks($name)
	{
		return isset($this->hooks[$name]) ? $this->hooks[$name] : [];
	}

	public function on($name, $callback)
	{
		if (!is_callable($callback, true)) {
			throw new \InvalidArgumentException(sprintf('Invalid callback: %s.', print_r($callback, true)));
		}

		if (!is_array($name)) {
		    $name = [$name];
		}

		foreach ($name as $event) {
		    $this->hooks[$event][] = $callback;
		}
	}

	public function listen()
	{
		$data = json_decode(file_get_contents('php://input'), true);

		if (empty($data)) {
			return false;
		}

		$event = $data['type'];
		$object = $data['object'];
		$id = $data['group_id'];

		foreach($this->getCallbacks($event) as $callback) {
			call_user_func($callback, $object, $id);
		}
	}
}
