<?php
/**
 * Connector コネクタメソッド
 */

namespace Example;

class Connector extends \Connector
{
	// コネクタ情報の取得
	public function get_connector_spec()
	{
		return array(
				'screen_name' => 'コネクタ実装例',
				'description' => 'Archelon用コネクタの実装例です。',
			);
	}

	// API情報の取得
	public function get_api_spec()
	{
		return array(
				'hoge' => array(
					'method' => 'GET',
					'path' => 'v1/hoge/{userId}',
					'title' => 'GETメソッド',
					'description' => 'GETメソッドのテスト',
					'parameters' => array(
						'userId' => array(
							'description' => 'ユーザーID',
							'value' => '',
							'required' => false,
							'param_type' => 'path',
							'data_type' => 'integer',
						),
						'key' => array(
							'description' => 'APIキー',
							'value' => '',
							'required' => true,
							'param_type' => 'query',
							'data_type' => 'API_KEY',
						),
					),
					'status_codes' => array(
						200 => array( 'reason' => '成功', ),
						401 => array( 'reason' => '認証に失敗', ),
						403 => array( 'reason' => 'アクセス権限がない', ),
						404 => array( 'reason' => '見つからない', ),
					),
				),
				'fuga' => array(
					'method' => 'POST',
					'path' => 'v1/fuga/{userId}',
					'title' => 'POSTメソッド',
					'description' => 'POSTメソッドのテスト',
					'parameters' => array(
						'userId' => array(
							'description' => 'ユーザーID',
							'value' => '',
							'required' => false,
							'param_type' => 'path',
							'data_type' => 'integer',
						),
						'key' => array(
							'description' => 'APIキー',
							'value' => '',
							'required' => true,
							'param_type' => 'query',
							'data_type' => 'API_KEY',
						),
					),
					'status_codes' => array(
						200 => array( 'reason' => '成功', ),
						401 => array( 'reason' => '認証に失敗', ),
						403 => array( 'reason' => 'アクセス権限がない', ),
						404 => array( 'reason' => '見つからない', ),
					),
				),
			);
	}

	// コネクタ設定フォームの取得
	public function get_config_form()
	{
		\Config::load('example::config.db', 'example', true, true);

		return array(
				'input_text' => array(
					'label'      => 'テキストボックス',
					'validation' => array('required'),
					'form'       => array('type' => 'text'),
					'default'    => \Config::get('example.config.input_text', 'test'),
				),
				'input_password' => array(
					'label'      => 'パスワード入力ボックス',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => \Config::get('example.config.input_password', 'test'),
				),
				'checkbox' => array(
					'label'      => 'チェックボックス',
					'validation' => array(),
					'form'       => array('type' => 'checkbox'),
					'default'    => \Config::get('example.config.input_password', 'on'),
				),
				'select' => array(
					'label'      => 'セレクトボックス',
					'validation' => array('required'),
					'form'       => array('type' => 'select', 'options' => array('1' => '1番目', '2' => '2番目')),
					'default'    => \Config::get('example.config.select', '1'),
				),
			//	'@script' => "", // 追加でスクリプトが必要であれば
			);
	}

	// コネクタ設定の更新
	public function save_config($validation)
	{
		\Config::load('example::config.db', 'example', true, true);

		foreach (array('input_text',
		               'input_password',
		               'checkbox',
		               'select'
		               ) as $name)
		{
			\Config::set('example.config.'.$name, $validation->validated($name));
		}

		return \Config::save('example::config.db', 'example');
	}

	// 登録情報フォームの取得
	public function get_account_form($account_id = null)
	{
		\Config::load('example::config.db', 'example', true, true);

		$update = !is_null($account_id);

		if ($update)
		{
			$user = Model_Example_User::query()
						->where('account_id', $account_id)
						->get_one();
		}

		return array(
				'input_text' => array(
					'label'      => 'テキストボックス',
					'validation' => array('required'),
					'form'       => array('type' => 'text'),
					'default'    => $update ? $user->text : '',
				),
				'input_password' => array(
					'label'      => 'パスワード入力ボックス',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => $update ? \Connector::decrypt($user->password, $user->salt) : '',
				),
				'checkbox' => array(
					'label'      => 'チェックボックス',
					'validation' => array(),
					'form'       => array('type' => 'checkbox'),
					'default'    => !$update || $user->checkbox ? 'on' : '',
				),
				'select' => array(
					'label'      => 'セレクトボックス',
					'validation' => array('required'),
					'form'       => array('type' => 'select', 'options' => array('1' => '1番目', '2' => '2番目')),
					'default'    => $update ? $user->select : '',
				),
			//	'@script' => "", // 追加でスクリプトが必要であれば
			);
	}

	// 登録情報の更新
	public function save_account($validation, $account_id = null)
	{
		\Config::load('example::config.db', 'example', true, true);

		try
		{
			\DB::start_transaction();

			$salt = '';

			if (is_null($account_id))
			{ // 新規
				$user = new Model_Example_User();
				$user->account = new \Model_Account();
			}
			else
			{ // 更新
				$user = Model_Example_User::query()
							->where('account_id', $account_id)
							->related('account') // 関連してアカウントも更新
							->get_one();
			}
			if (!$user || !$user->account)
			{
				return false;
			}

			$user->account->connector_id = $this->connector_id;
			$user->account->description  = serialize(array(
												'テキスト' => $validation->validated('input_text'),
												'チェック' => $validation->validated('checkbox') ? 'true' : 'false',
												'セレクト' => $validation->validated('select'),
											));
			$user->text     = $validation->validated('input_text');
			$user->password = \Connector::encrypt($validation->validated('input_password'), $salt, true);
			$user->checkbox = $validation->validated('checkbox');
			$user->select   = $validation->validated('select');
			$user->salt     = $salt;
			$user->save();

			\DB::commit_transaction();
		}
		catch (\Exception $e)
		{
			\DB::rollback_transaction();

			\Log::error($e->getMessage());

			return false;
		}

		return true;
	}

	// 登録情報の削除
	public function drop_account($account_id)
	{
		\Config::load('example::config.db', 'example', true, true);

		try
		{
			\DB::start_transaction();

			// 削除処理
			$user = Model_Example_User::query()
						->where('account_id', $account_id)
						->related('account') // 関連してアカウントも削除
						->get_one();
			$user->delete();

			\DB::commit_transaction();
		}
		catch (\Exception $e)
		{
			\DB::rollback_transaction();

			\Log::error($e->getMessage());

			return false;
		}

		return true;
	}
}
