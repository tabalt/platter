<?php

namespace Platter\Database;

class Mongo
{

    /**
     * Mongo数据库
     * @var \MongoDB
     */
    private $Db;

    /**
     * 构造函数
     * @author tabalt
     */
    public function __construct($host, $port, $dbname, $username, $password, $replicaSet = false, $type = 'mongodb', $readPreference = \MongoClient::RP_SECONDARY_PREFERRED)
    {
        $dsn = "{$type}://{$host}:{$port}";
        $optionList = array(
            'username' => $username, 
            'password' => $password, 
            // 读优先策略，默认从库优先
            'readPreference' => $readPreference
        );
        // 集群号
        if ($replicaSet) {
            $optionList['replicaSet'] = $replicaSet;
        }
        
        $conn = new \MongoClient($dsn, $optionList);
        
        $this->Db = $conn->selectDb($dbname);
    }

    /**
     * 获取Mongo数据库
     * @var \MongoDB
     */
    public function getDb()
    {
        return $this->Db;
    }
}