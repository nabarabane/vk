<?php

namespace VK;

class Handler
{
	public $access_token;
	public $result;

	public function __construct()
	{
		$file = __DIR__.'/../config/access_token.key';
		if (!file_exists($file)) {
			throw new \Exception('Отсутсвует файл с ключом');
		}

		$this->access_token = trim(file_get_contents($file));
	}

	public function request($method = null, $params = null)
	{
		if (empty($method)) {
			throw new \Exception('Не указан метод запроса');
		}

		if (empty($params) || !is_array($params)) {
			throw new \Exception('Не указаны параметры запроса');
		}

		$url = $method;

		if (count($params)) {
			$i = 1;
			foreach ($params as $key => $value) {
				$url .= ($i == 1) ? '?' : '&';
				$url .= $key.'='.$value;
				$i++;
			}
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/'.$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		curl_close($ch);

		if ($error) {
			throw new \Exception($error);
		}

		$this->result = json_decode($result);

		if (!empty($this->result->error)) {
			throw new \Exception('Code: ('.$this->result->error->error_code.') '.$this->result->error->error_msg);
		}

		$this->result = $this->result->response;

		return $this;
	}
}
