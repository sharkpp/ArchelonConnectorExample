<?php

namespace Example;

class Model_Example_User extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'account_id',
		'text',
		'salt',
		'password',
		'checkbox',
		'select',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => true,
		),
	);

	protected static $_belongs_to = array(
		'account' => array(
			'key_from' => 'account_id',
			'model_to' => '\Model_Account',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => true,
		)
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'example_users';

}
