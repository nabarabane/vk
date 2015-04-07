<?php

namespace Vk;

class Handler
{
	public $access_token;
	public $result;

	public function __construct()
	{
		$file = __DIR__.'/../access_token.key';
		if (!file_exists($file)) {
			throw new Exception('Отсутсвует файл с ключом');
		}

		$this->access_token = trim(file_get_contents($file));
	}

	public function request(Request $request)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.vk.com/method/'.$request->url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		curl_close($ch);

		if ($error) {
			throw new Exception($error);
		}

		$this->result = json_decode($result);

		if (!empty($this->result->error)) {
			throw $this->catchError();
		}
	}

	private function catchError()
	{
		return new Exception('Code: ('.$this->result->error->error_code.') '.$this->result->error->error_msg);
	}
}
