<?php

/** @see Zend_Cache */
require_once 'Zend/Cache.php';

/** @see Zend_Config_Xml */
require_once 'Zend/Config/Xml.php';

/**
 * config logic's Operation
 * get config
 * 
 * @package    Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/28    HCH
 */
class Bll_Config
{

    /**
     * get college config xml
     *
     * @param string $xml
     * @param string $prefix
     *  college hostname
     * @return xml
     */
    public static function get($xml, $prefix = null)
    {
        // set a backend Name(eg. 'File' or 'Sqlite'...)
        $backendName = 'Memcached';
        
        // set a frontend Name(eg. 'Core', 'Output', 'Page'...)
        $frontendName = 'File';
        
        // set frontend array
        $frontendOptions = array('automatic_serialization' => true, 'master_file' => $xml);
        
        // set backend array
        $backendOptions = array('servers' => array('host' => '127.0.0.1', 'port' => 11211, 'persistent' => true));
        
        // create cache object
        $cache = Zend_Cache::factory($frontendName, $backendName, $frontendOptions, $backendOptions);
        
        if ($prefix === null) {
            $prefix = 'linno';
        }
        
        $key = md5($prefix . '_' . $xml);
        
        if (!$config = $cache->load($key)) {
            $config = new Zend_Config_Xml($xml, null);
            $cache->save($config, $key);
        }
        
        return $config;
    }
}