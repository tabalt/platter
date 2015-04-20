<?php

namespace Platter\Model\Dao;

class Mongo extends Base
{

    CONST DULICATE_KEY_CODE = 11000;

    /**
     * 默认配置KEY
     * @author tabalt
     * @var sting
     */
    protected $defaultConfigKey = 'MONGO_CONFIG';

    /**
     * 格式化配置信息
     * @see \Platter\Model\Dao\Base::formatConfig()
     */
    protected function formatConfig($config)
    {
        $config = array_change_key_case($config);
        
        return array(
            'host' => isset($config['host']) ? $config['host'] : 'localhost', 
            'port' => isset($config['port']) ? $config['port'] : '27017', 
            'name' => isset($config['name']) ? $config['name'] : '', 
            'user' => isset($config['user']) ? $config['user'] : '', 
            'pass' => isset($config['pass']) ? $config['pass'] : '', 
            'replica_set' => isset($config['replica_set']) ? $config['replica_set'] : false
        );
    }

    /**
     * 初始化查询数据列表
     * @author tabalt
     */
    protected function initQueryDataList()
    {
        $this->queryDataList = array(
            'field' => false, 
            'page' => false, 
            'order' => false, 
            'data' => array(), 
            'where' => array()
        );
    }

    /**
     * 初始化 Mongo Dao
     */
    protected function initMongoDao()
    {
        // 初始化DB
        try {
            $Mongo = new \Platter\Database\Mongo($this->config['host'], $this->config['port'], $this->config['name'], $this->config['user'], $this->config['pass'], $this->config['replica_set']);
            $this->Db = $Mongo->getDb();
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取collction
     */
    protected function getCollection()
    {
        return $this->Db->selectCollection($this->name);
    }

    /**
     * 获取结果状态
     * @param array $result
     */
    protected function getResultStatus($result)
    {
        if ($result['err'] == null) {
            return true;
        } else {
            $this->setError($result['err']);
            return false;
        }
    }

    /**
     * 构造函数
     * @author tabalt
     * @param string $name
     * @param array $configKey
     */
    public function __construct($name = null, $configKey = null)
    {
        parent::__construct($name, $configKey);
        
        // 初始化 Mongo Dao
        $this->initMongoDao();
    }

    /**
     * 设置字段列表
     * @author tabalt
     */
    public function field($fieldList)
    {
        $itemList = array();
        foreach ($fieldList as $field) {
            $itemList[$field] = 1;
        }
        $this->queryDataList['field'] = $itemList;
        return $this;
    }

    /**
     * 设置条件
     * @author tabalt
     */
    public function where($where)
    {
        $this->queryDataList['where'] = $where;
        return $this;
    }

    /**
     * 设置排序
     * @author tabalt
     */
    public function order($order)
    {
        $this->queryDataList['order'] = $order;
        return $this;
    }

    /**
     * 设置分页
     * @author tabalt
     */
    public function page($page)
    {
        $itemList = explode(",", $page);
        $skipCount = isset($itemList[0]) ? intval($itemList[0]) : false;
        $limitCount = isset($itemList[1]) ? intval($itemList[1]) : false;
        
        if (($skipCount !== false) && ($limitCount !== false)) {
            $this->queryDataList['page'] = array(
                'skip' => $skipCount, 
                'limit' => $limitCount
            );
        }
        return $this;
    }

    /**
     * 设置数据
     * @author tabalt
     */
    public function data($data)
    {
        $this->queryDataList['data'] = $data;
        return $this;
    }

    /**
     * 查询记录
     * @author tabalt
     */
    public function select()
    {
        $where = ! empty($this->queryDataList['where']) ? $this->queryDataList['where'] : array();
        $field = ! empty($this->queryDataList['field']) ? $this->queryDataList['field'] : array();
        $order = ! empty($this->queryDataList['order']) ? $this->queryDataList['order'] : false;
        $page = ! empty($this->queryDataList['page']) ? $this->queryDataList['page'] : false;
        
        $result = $this->getCollection()->find($where, $field);
        
        // 排序
        if ($order) {
            $result = $result->sort($order);
        }
        
        // 分页
        if ($page) {
            $result = $result->skip($page['skip'])->limit($page['limit']);
        }
        
        $infoList = array();
        foreach ($result as $info) {
            $infoList[] = $info;
        }
        return $infoList;
    }

    /**
     * 查询单条记录
     * @author tabalt
     */
    public function find()
    {
        $result = $this->select();
        
        return isset($result[0]) ? $result[0] : false;
    }

    /**
     * 通过ID查询单条记录
     * @author tabalt
     */
    public function getById($id)
    {
        $where = array(
            '_id' => $id
        );
        $result = $this->where($where)->find();
        
        return $result;
    }

    /**
     * 添加记录
     * @author tabalt
     */
    public function add()
    {
        if (empty($this->queryDataList['data'])) {
            $this->setError("数据不能为空");
            return false;
        }
        try {
            $options = array(
                'w' => 1
            );
            $result = $this->getCollection()->insert($this->queryDataList['data'], $options);
            return $this->getResultStatus($result);
        } catch ( \Exception $e ) {
            if ($e->getCode() == self::DULICATE_KEY_CODE) {
                $this->setError("存在重复键");
            } else {
                $this->setError($e->getMessage());
            }
            return false;
        }
    }

    /**
     * 编辑记录
     * @author tabalt
     */
    public function edit()
    {
        $where = ! empty($this->queryDataList['where']) ? $this->queryDataList['where'] : array();
        $data = ! empty($this->queryDataList['data']) ? $this->queryDataList['data'] : array();
        
        if (empty($where)) {
            $this->setError("编辑条件不能为空");
            return false;
        }
        
        if (empty($data)) {
            $this->setError("数据不能为空");
            return false;
        }
        
        try {
            $options = array(
                'w' => 1
            );
            $result = $this->getCollection()->update($where, $data, $options);
            return $this->getResultStatus($result);
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除记录
     * @author tabalt
     */
    public function delete()
    {
        $where = ! empty($this->queryDataList['where']) ? $this->queryDataList['where'] : array();
        
        if (empty($where)) {
            $this->setError("不能进行空条件的删除");
            return false;
        }
        
        try {
            $options = array(
                'w' => 1
            );
            $result = $this->getCollection()->remove($where, $options);
            return $this->getResultStatus($result);
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 统计数量
     * @author tabalt
     */
    public function count()
    {
        $where = ! empty($this->queryDataList['where']) ? $this->queryDataList['where'] : array();
        
        $result = $this->getCollection()->find($where)->count();
        return $result;
    }
}