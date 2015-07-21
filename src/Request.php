<?php

namespace Vk;

class Request
{
	public $url;

	public function __construct($method = null, $params = null)
	{
		if (empty($method)) {
			throw new \Exception('Не указан метод запроса');
		}

		if (empty($params) || !is_array($params)) {
			throw new \Exception('Не указаны параметры запроса');
		}

		$this->url = $method;

		if (count($params)) {
			$i = 1;
			foreach ($params as $key => $value) {
				$this->url .= ($i == 1) ? '?' : '&';
				$this->url .= $key.'='.$value;
				$i++;
			}
		}
	}
}
