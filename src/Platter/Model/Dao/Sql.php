<?php

namespace Platter\Model\Dao;

class Sql extends Base
{

    const QUERY_TYPE_SELECT = 1;

    const QUERY_TYPE_DELETE = 2;

    const QUERY_TYPE_INSERT = 3;

    const QUERY_TYPE_UPDATE = 4;

    /**
     * 默认配置KEY
     * @author tabalt
     * @var sting
     */
    protected $defaultConfigKey = 'DB_CONFIG';

    /**
     * 主键
     * @author tabalt
     * @var sting
     */
    protected $primaryKey;

    /**
     * 字段列表
     * @author tabalt
     * @var array
     */
    protected $fieldList;

    /**
     * SQL记录
     * @author tabalt
     * @var array
     */
    protected $sqlList;

    /**
     * 初始化sql查询的数据
     * @author tabalt
     */
    protected function initQueryDataList()
    {
        $this->queryDataList = array(
            'field' => false, 
            'page' => false, 
            'order' => false, 
            'group' => false, 
            'data' => array(), 
            'where' => array(
                'tpl' => false, 
                'data' => array()
            )
        );
    }

    /**
     * 从数据库中查询字段列表
     * @author tabalt
     * @return array $fieldList 字段列表
     */
    private function queryTableFields()
    {
        $sql = "SHOW COLUMNS FROM " . $this->getTableFullName();
        $result = $this->db->getResult($sql);
        if (! empty($result)) {
            $fieldList = array();
            foreach ($result as $fieldInfo) {
                $fieldList[$fieldInfo['Field']] = array(
                    'name' => $fieldInfo['Field'], 
                    'type' => $fieldInfo['Type'], 
                    // not null is empty, null is yes
                    'notnull' => (bool) ($fieldInfo['Null'] === ''), 
                    'default' => $fieldInfo['Default'], 
                    'primary' => (strtolower($fieldInfo['Key']) == 'pri'), 
                    'autoinc' => (strtolower($fieldInfo['Extra']) == 'auto_increment')
                );
                if (strtolower($fieldInfo['Key']) == 'pri') {
                    $this->primaryKey = $fieldInfo['Field'];
                }
                $fieldList['_fieldlist'][] = $fieldInfo['Field'];
            }
            // 缓存字段信息
            $this->setCacheFieldList($fieldList);
            return $fieldList;
        } else {
            throw new \Exception('table ' . $this->getTableFullName() . '\'s fieldList query error');
        }
    }

    /**
     * 从缓存中读取字段信息
     * @author tabalt
     * @param string $tableName 表名称
     * @return array $fieldList 字段列表
     */
    private function getCacheFieldList($tableName)
    {
        // TODO 从缓存中读取字段信息
        $fieldList = false;
        return $fieldList;
    }

    /**
     * 将字段信息设置到缓存中
     * @author tabalt
     * @param array $fieldList 字段列表
     * @return boolean true/false
     */
    private function setCacheFieldList($fieldList)
    {
        // TODO 将字段信息设置到缓存中
        return false;
    }

    /**
     * 取得表的字段列表
     * @author tabalt
     * @return array $fieldList 字段列表
     */
    private function getTableFields()
    {
        $fieldList = array();
        if (is_debug()) {
            $fieldList = $this->queryTableFields();
        } else {
            $fieldList = $this->getCacheFieldList($this->getTableFullName());
            if (! empty($fieldList)) {
                // 从缓存中返回 并设置主键
                foreach ($fieldList as $fieldInfo) {
                    if ($fieldInfo['primary']) {
                        $this->primaryKey = $fieldInfo['name'];
                    }
                }
            } else {
                $fieldList = $this->queryTableFields();
            }
        }
        return $fieldList;
    }

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
            'charset' => isset($config['charset']) ? $config['charset'] : '', 
            'persistent' => isset($config['persistent']) ? $config['persistent'] : false
        );
    }

    /**
     * 初始化Sql Dao
     */
    protected function initSqlDao()
    {
        // 初始化DB
        try {
            $this->db = new \Platter\Database\Sql($this->config['host'], $this->config['port'], $this->config['name'], $this->config['user'], $this->config['pass'], $this->config['charset'], $this->config['persistent']);
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
        // 取得字段列表
        $this->fieldList = $this->getTableFields();
        $this->initQueryDataList();
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
        
        // 初始化 Sql Dao
        $this->initSqlDao();
    }

    /**
     * 获取表全名
     * @author tabalt
     * @return array $realTableName 表全名
     */
    public function getTableFullName()
    {
        return $this->tableFullName;
    }

    /**
     * 获取主键名称
     * @return $primaryKey 主键
     */
    public function getPk()
    {
        return $this->primaryKey;
    }

    /**
     * 获取最后执行的sql语句
     * @author tabalt
     * @return $sqlList
     */
    public function getLastSql()
    {
        if (! empty($this->sqlList)) {
            return $this->sqlList[count($this->sqlList) - 1];
        } else {
            return false;
        }
    }

    /**
     * 获取字段列表
     * @author tabalt
     * @return array $fieldList
     */
    public function getFieldList()
    {
        if (isset($this->fieldList['_fieldlist'])) {
            return $this->fieldList['_fieldlist'];
        } else {
            return false;
        }
    }

    /**
     * 重写 获取验证和过滤后的数据列表
     * @author tabalt
     * @param array $fieldList 字段列表
     * @param array $data
     */
    public function getDataList($fieldList = array(), $data = array())
    {
        $data = parent::getDataList($fieldList, $data);
        if (isset($data[$this->primaryKey])) {
            unset($data[$this->primaryKey]);
        }
        return $data;
    }

    /**
     * 指定WHERE条件 支持安全过滤
     * @param array $where 条件表达式
     * @return DbModel
     */
    public function where($where, $logic = 'and')
    {
        // 获取logic
        $logic = isset($where['logic']) ? $where['logic'] : $logic;
        unset($where['logic']);
        $where = array();
        if (is_array($where) && ! empty($where)) {
            $tplList = $dataList = array();
            foreach ($where as $field => $value) {
                if (! is_array($value)) {
                    $tplList[$field] = "`{$field}` = :{$field}";
                    $dataList[":{$field}"] = $value;
                } else if (is_array($value)) {
                    $lgc = isset($value['logic']) ? $value['logic'] : 'and';
                    $lgc = strtoupper($lgc);
                    unset($value['logic']);
                    $tmpList = array();
                    $i = 1;
                    foreach ($value as $key => $val) {
                        if (is_array($val)) {
                            $tList = array();
                            $val = array_unique($val);
                            foreach ($val as $k => $v) {
                                $tList[] = ":{$field}_{$i}_{$k}";
                                $dataList[":{$field}_{$i}_{$k}"] = $v;
                            }
                            $tmpList[] = "`{$field}` {$key} (" . implode(',', $tList) . ")";
                        } else {
                            $tmpList[] = "`{$field}` {$key} :{$field}_{$i}";
                            $dataList[":{$field}_{$i}"] = $val;
                        }
                        $i ++;
                    }
                    $tplList[$field] = "( " . implode(" {$lgc} ", $tmpList) . " )";
                }
            }
            $logic = strtoupper($logic);
            $this->queryDataList['where']['tpl'] = implode(" {$logic} ", $tplList);
            $this->queryDataList['where']['data'] = $dataList;
        }
        return $this;
    }

    /**
     * 设置查询字段
     * @author tabalt
     * @param mixed $fieldList
     */
    public function field($fieldList = false)
    {
        if (empty($fieldList)) {
            $fieldList = $this->getFieldList();
        }
        if (is_string($fieldList)) {
            $fieldList = explode(",", $fieldList);
        }
        $this->queryDataList['field'] = $fieldList;
        return $this;
    }

    /**
     * 设置order
     * @author tabalt
     * @param mixed $order
     */
    public function order($order = false)
    {
        $this->queryDataList['order'] = $order;
        return $this;
    }

    /**
     * 设置group
     * @author tabalt
     * @param mixed $group
     */
    public function group($group = false)
    {
        $this->queryDataList['group'] = $group;
        return $this;
    }

    /**
     * 设置page
     * @author tabalt
     * @param mixed $page
     */
    public function page($page = false)
    {
        $this->queryDataList['page'] = $page;
        return $this;
    }

    /**
     * 设置data
     * @author tabalt
     * @param mixed $order
     */
    public function data($data)
    {
        $this->queryDataList['data'] = $data;
        return $this;
    }

    /**
     * 执行sql语句
     * @author tabalt
     * @param string $sql
     * @param array $data
     * @param string $type
     */
    public function query($sql, $data = null, $type = false)
    {
        $this->initQueryDataList();
        $this->sqlList[] = $sql;
        $type = strtolower($type);
        try {
            if ($type == self::QUERY_TYPE_SELECT) {
                $result = $this->db->getResult($sql, $data);
            } else if ($type == self::QUERY_TYPE_DELETE || $type == self::QUERY_TYPE_UPDATE) {
                $result = $this->db->getAffectedRows($sql, $data);
            } else if ($type == self::QUERY_TYPE_INSERT) {
                $result = $this->db->getLastInsertId($sql, $data);
            } else {
                $result = $this->db->execute($sql, $data);
            }
            return $result;
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 查询记录
     * @author tabalt
     */
    public function select()
    {
        $fieldList = $this->queryDataList['field'];
        if (empty($fieldList)) {
            $fieldList = $this->getFieldList();
        }
        foreach ($fieldList as $key => $fieldName) {
            if (false === strpos($fieldName, 'as')) {
                $fieldList[$key] = '`' . $fieldName . '`';
            } else {
                $fieldList[$key] = $fieldName;
            }
        }
        
        $whereData = array();
        $where = '';
        // 获取where条件
        if (! empty($this->queryDataList['where']['tpl'])) {
            $where = 'WHERE ' . $this->queryDataList['where']['tpl'];
            $whereData = $this->queryDataList['where']['data'];
        }
        
        $page = $this->queryDataList['page'];
        if (! empty($page)) {
            $page = 'LIMIT ' . $page;
        }
        $order = $this->queryDataList['order'];
        if (! empty($order)) {
            $order = 'ORDER BY ' . $order;
        }
        $group = $this->queryDataList['group'];
        if (! empty($group)) {
            $group = 'GROUP BY ' . $group;
        }
        $sqlTpl = "SELECT " . implode(',', $fieldList) . " FROM {$this->getTableFullName()} {$where} {$group} {$order} {$page};";
        try {
            return $this->query($sqlTpl, $whereData, 'select');
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 添加记录
     * @author tabalt
     */
    public function add()
    {
        $data = $this->queryDataList['data'];
        $fieldStr = $valueStr = '';
        foreach ($data as $fieldName => $fieldValue) {
            $fieldStr .= '`' . $fieldName . '`,';
            $valueStr .= ':' . $fieldName . ',';
        }
        $sqlTpl = "INSERT INTO " . $this->getTableFullName() . " ( " . rtrim($fieldStr, ',') . " ) VALUES ( " . rtrim($valueStr, ',') . " ); ";
        try {
            return $this->query($sqlTpl, $data, 'insert');
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 编辑记录
     * @author tabalt
     */
    public function edit()
    {
        $whereData = array();
        $where = '';
        // 获取where条件
        if (! empty($this->queryDataList['where']['tpl'])) {
            $where = 'WHERE ' . $this->queryDataList['where']['tpl'];
            $whereData = $this->queryDataList['where']['data'];
        }
        if (empty($where)) {
            $this->setError('更新条件不能为空');
            return false;
        }
        
        $data = $this->queryDataList['data'];
        $editStr = '';
        foreach ($data as $fieldName => $fieldValue) {
            $editStr .= "`{$fieldName}` = :{$fieldName},";
        }
        
        $sqlTpl = "UPDATE " . $this->getTableFullName() . " SET " . rtrim($editStr, ',') . " $where ; ";
        try {
            return $this->query($sqlTpl, array_merge($data, $whereData), 'update');
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
        $whereData = array();
        $where = '';
        // 获取where条件
        if (! empty($this->queryDataList['where']['tpl'])) {
            $where = 'WHERE ' . $this->queryDataList['where']['tpl'];
            $whereData = $this->queryDataList['where']['data'];
        }
        if (empty($where)) {
            $this->setError('删除条件不能为空');
            return false;
        }
        $sqlTpl = "DELETE FROM " . $this->getTableFullName() . " $where ;";
        try {
            return $this->query($sqlTpl, $whereData, 'delete');
        } catch ( \Exception $e ) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * 查询单条记录
     * @author tabalt
     */
    public function find()
    {
        $result = $this - paget(1)->select();
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * 统计数量
     * @author tabalt
     */
    public function count()
    {
        $result = $this->field("count(`{$this->primaryKey}`) as count")->find();
        if (! empty($result) && isset($result['count'])) {
            return $result['count'];
        } else {
            return 0;
        }
    }
}