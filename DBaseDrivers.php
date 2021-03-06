<?php

	/**
	* DBaseDrivers
	*
	* @author			Filgy (filgy@sniff.cz)
	* @package			DBaseDumper (Database dumper)
	* @license			GNU/GPL v2
	* @update			26.8.2011 21:51
	*/
	
	abstract class DBaseDriver{
		protected $config = Array();
		protected $handler = NULL;
		protected $resultset = NULL;
		
		public function __construct(Array $config){
			$this->config = $config;
		}
	};
	
	final class DBaseDriverMysql extends DBaseDriver implements DBaseDriverI{
		
		public function __construct(Array $config){
			parent::__construct($config);
			
			if(!extension_loaded("mysql"))
				throw new DBaseDriverException("Can't load mysql extension");
		}
		
		/**
		* Get result - one row
		* @return Array
		*/
		public function singleRow($sql){
			return mysql_fetch_row($this->query($sql, FALSE));
		}
		
		/**
		* Get result - one column
		* @return string
		*/
		public function singleColumn($sql){
			return mysql_result($this->query($sql, FALSE), 0);
		}
		
		/**
		* Execute query
		* @return mysql_resource
		* @throws DBaseDriverException
		*/
		public function query($sql, $store = TRUE){
			var_dump($sql);			
			$result = mysql_query($sql, $this->getConnection());
			
			if(!$result)
				throw new DBaseDriverException("Cannot execute query");

			return ($store)? $this->resultset = $result : $result;
		}
		
		/**
		* Returns next result from resultset
		* @return DBaseRecord, bool
		* @throws DBaseDriverException
		*/
		public function nextResult(){
			if($this->resultset === NULL)
				throw new DBaseDriverException("Invalid resultset");
				
			if(($row = mysql_fetch_array($this->resultset)) === FALSE)
				return FALSE;
			else
				return new DBaseRecord($row);
		}
		
		/**
		* Clear resultset
		*/
		public function clearResult(){
			if($this->resultset !== NULL)
				unset($this->resultset);
			
			$this->resultset = NULL;
		}
		
		/**
		* Return escaped string
		* @return string
		*/
		public function escape($string){
			return mysql_real_escape_string($string, $this->getConnection());
		}
		
		/**
		* Return tables list
		* @return DBaseRecord
		* @throws DBaseDriverException
		*/
		public function showTables($dbName){
			try{
				$query = $this->query("SHOW TABLES FROM `".$this->escape($dbName)."`", FALSE);
				
				$records = new DBaseRecord;
				while($result = mysql_fetch_array($query))
					$records[] = $result[0];
				
				return $records;
			}
			catch(DBaseDriverException $e){
				throw new DBaseDriverException("Undefined database");
			}
		}
		
		/**
		* Return columns list
		* @return DBaseRecord
		* @throws DBaseDriverException
		*/
		public function showColumns($dbName, $tableName){
			try{
				$query = $this->query("SHOW COLUMNS FROM `".$this->escape($dbName)."`.`".$this->escape($tableName)."`", FALSE);
				
				$records = new DBaseRecord;
				while($result = mysql_fetch_assoc($query))
					$records[] = $result;
					
				return $records;
			}
			catch(DBaseDriverException $e){
				throw new DBaseDriverException("Undefined database/table");
			}
		}
		
		/**
		* Return create table
		* @return string
		* @throws DBaseDriverException
		*/
		public function showCreateTable($dbName, $tableName){
			try{
				$result = $this->singleRow("SHOW CREATE TABLE `".$this->escape($dbName)."`.`".$this->escape($tableName)."`");
				
				return $result[1];
			}
			catch(DBaseDriverException $e){
				throw new DBaseDriverException("Undefined database/table");
			}
		}
		
		/**
		* Create singleton handler
		* @return mysql_resource
		* @throws DBaseDriverException
		*/
		private function getConnection(){
			if($this->handler === NULL){
				$this->handler = @mysql_connect($this->config['hostname'], $this->config['username'], $this->config['password']);
					
				if(!@mysql_query("SET NAMES ".((isset($this->config['charset']))? $this->config['charset'] : "utf8"), $this->handler))
					throw new DBaseDriverException("Can't set charset");
			}
				
			if(!$this->handler)
				throw new DBaseDriverException("Can't connect to database server");
				
			return $this->handler;
		}
	};
	
	final class DBaseDriverMysqli extends DBaseDriver implements DBaseDriverI{
		
		public function __construct(Array $config){
			parent::__construct($config);
			
			if(!extension_loaded("mysqli"))
				throw new DBaseDriverException("Can't load mysqli extension");
		}
		
		public function singleRow($sql){
			
		}
		
		public function singleColumn($sql){
			
		}
		
		public function query($sql){
			
		}
		
		public function nextResult(){
		
		}
		
		public function clearResult(){
			
		}
		
		public function escape($string){
			
		}
		
		public function showTables($dbName){
			
		}
		
		public function showColumns($dbName, $tableName){
			
		}
		
		public function showCreateTable($dbName, $tableName){
			
		}
		
		private function getConnection(){
			
		}

	};
