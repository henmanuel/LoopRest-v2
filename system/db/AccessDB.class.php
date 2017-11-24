<?php

/**
 * Class AccessDB
 *
 * @author   Mario Henmanuel Vargas Ugalde <hemma.hvu@gmail.com>
 */
class AccessDB extends DB
{
	private $db;
	private $host;
	private $user;
	private $password;
	private $conectionDB;
	
	protected function connectionDB()
	{
		$this->host = 'localhost';
		$this->user = 'root';
		$this->password = '';
		$this->db = 'looprest';
		
		$this->conectionDB = new MysqlDB($this->host, $this->user, $this->password, $this->db);
	}
	
	/**
	 * Request data to system
	 *
	 * @param $object
	 * @param $registry
	 * @param bool $print
	 * @return bool|array
	 */
	protected function requestSystemData($object, $registry)
	{
		$this->connectionDB();
		$db = $this->conectionDB;
		$result = $db->search($object, $registry);
		
		$db->close();
		return $result;
	}
	
	/**
	 * Insert data in system
	 *
	 * @param $object
	 * @param $data
	 * @return array|bool
	 */
	protected function insertSystemData($object, $data){
		$this->connectionDB();
		$db = $this->conectionDB;
		return $db->insert($object, $data);
	}
}