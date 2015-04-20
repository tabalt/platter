<?php

namespace Platter\Model\Dao;

abstract class Base extends \Platter\Model\Base
{

    /**
     * 默认配置KEY
     * @author tabalt
     * @var sting
     */
    protected $defaultConfigKey;

    /**
     * 配置
     * @author tabalt
     * @var array
     */
    protected $config;

    /**
     * 数据库连接对象
     * @var object
     */
    private $Db;

    /**
     * 查询数据列表
     * @author tabalt
     * @var array
     */
    protected $queryDataList;

    /**
     * 格式化配置信息
     * @param array $config
     */
    abstract protected function formatConfig($config);

    /**
     * 初始化查询数据列表
     * @author tabalt
     */
    abstract protected function initQueryDataList();

    /**
     * 初始化基类Dao
     * @param string $configKey
     * @param string $name
     */
    protected function initBaseDao($configKey)
    {
        if (empty($configKey)) {
            $configKey = $this->defaultConfigKey;
        }
        // 初始化配置
        $config = \Platter\Component\Config::get($configKey);
        $this->config = $this->formatConfig($config);
    }

    /**
     * 构造函数
     * @author tabalt
     * @param array $configKey
     * @param string $name
     */
    public function __construct($name = null, $configKey = null)
    {
        parent::__construct($name);
        
        $this->initBaseDao($configKey);
        
        $this->initQueryDataList();
    }

    /**
     * 获取Dao名
     * @author tabalt
     */
    public function getName()
    {
        return $this->name;
    }

    abstract public function field($fieldList);

    abstract public function where($where);

    abstract public function order($order);

    abstract public function data($data);

    abstract public function select();

    abstract public function add();

    abstract public function edit();

    abstract public function delete();

    abstract public function find();

    abstract public function count();
}