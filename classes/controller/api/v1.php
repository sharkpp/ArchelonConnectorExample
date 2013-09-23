<?php

namespace Example;

class Controller_Api_v1 extends \Controller_Rest
{
	// GETメソッド
	public function get_hoge($id = null)
	{
		\Config::load('dneo::config.db', 'dneo');

		$api_key = \Input::get('key');

		$account = \Model_Account::query()
						->where('api_key', $api_key)
						->where('connector_id', \Connector::get_connector_id())
						->get_one();
		if ($account)
		{
			$user = Model_Example_User::query()
							->where('account_id', $account->id)
							->get_one();
			if ($user)
			{
				return $this->response(
							\Arr::merge(array('param_id' => $id),
										\Arr::filter_keys($user->to_array(), array('salt', 'password'), true))
							);
			}
		}

		return $this->response(array(
									'status' => 403,
									'message' =>  'アクセス権がありません',
								), 403); // 403 Forbidden

	}

	// POSTメソッド
	public function post_fuga($id = null)
	{
		\Config::load('dneo::config.db', 'dneo');

		$api_key = \Input::post('key', \Input::get('key'));

		$account = \Model_Account::query()
						->where('api_key', $api_key)
						->where('connector_id', \Connector::get_connector_id())
						->get_one();
		if ($account)
		{
			$user = Model_Example_User::query()
							->where('account_id', $account->id)
							->get_one();
			if ($user)
			{
				return $this->response(
							\Arr::merge(array('param_id' => $id),
										\Arr::filter_keys($user->to_array(), array('salt', 'password'), true))
							);
			}
		}

		return $this->response(array(
									'status' => 403,
									'message' =>  'アクセス権がありません',
								), 403); // 403 Forbidden

	}
}
