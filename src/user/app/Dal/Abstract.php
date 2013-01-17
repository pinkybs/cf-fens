<?php
require_once(LIB_DIR."/Zend/Registry.php");

class Dal_Abstract
{
    /**
     * db config
     * @var array
     */
    protected $_config;

    /**
     * db read adapter
     * @var Zend_Db_Abstract
     */
    protected $_rdb;

    /**
     * db write adapter
     * @var Zend_Db_Abstract
     */
    protected $_wdb;

	private function _getDbConfig()
	{
		
		$config = Zend_Registry::isRegistered('dbConfig') ? Zend_Registry::get('dbConfig') : null;
		if (empty($config)) {
			$dsn = MYSQL_DSN;
			$user = MYSQL_USER;
			$password = MYSQL_PASS;
			
			try {
			    $config = new PDO($dsn, $user, $password);
			    $config->query("SET NAMES utf8");
			    Zend_Registry::set('dbConfig', $config);
			} catch (PDOException $e) {
			    echo 'Connection failed: ' . $e->getMessage();
			}
		}
		return $config;
	}
	
    /**
     * init the variables
     *
     * @param array $config ( config info )
     */
    public function __construct($config = null)
    {
        if (is_null($config)) {
            $config = $this->_getDbConfig();
        }

        $this->_config = $config;
        $this->_rdb = $config;
        $this->_wdb = $config;
    }

    public function getReader()
    {
        return $this->_rdb;
    }

    public function getWriter()
    {
        return $this->_wdb;
    }

    public function getConfig()
    {
        return $this->_config;
    }

}