<?php

class UserModel_DB
{
	private static $instance;
	public static function getInstance()
	{
		if(is_null(self::$instance)){
			self::$instance = ModelDB::created(self::class, function($table){
				$table->int('id')->nullAble();
				$table->int('id')->autoIncrement();
				$table->int('id')->defaultValue(5);
				$table->int('id', 55)->primaryKey();

				$table->varchar('email');
				$table->date('birthDay');
				$table->varchar('externalId');
			});
		}

		return self::$instance;
	}
}