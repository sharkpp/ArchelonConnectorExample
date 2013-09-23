<?php

namespace Fuel\Migrations;

class Create_example_users
{
	public function up()
	{
		\DBUtil::create_table('example_users', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'account_id' => array('constraint' => 11, 'type' => 'int'),
			'text' => array('type' => 'text'),
			'salt' => array('type' => 'text'),
			'password' => array('type' => 'text'),
			'checkbox' => array('constraint' => 11, 'type' => 'int'),
			'select' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('example_users');
	}
}