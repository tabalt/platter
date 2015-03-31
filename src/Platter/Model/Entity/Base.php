<?php

namespace Platter\Model\Entity;

abstract class Base
{

    /**
     * 实体属性列表
     * @var array
     */
    protected $propertyList = array();

    /**
     * 格式化实体属性
     */
    protected function getFormatedPropertyInfo($field, $info)
    {
        if (! isset($info['name'])) {
            $info['name'] = $field;
        }
        
        if (! isset($info['value'])) {
            $info['value'] = null;
        }
        return $info;
    }

    /**
     * 格式化实体属性列表
     */
    protected function initPropertyList($data)
    {
        if (is_array($this->propertyList) && ! empty($this->propertyList)) {
            foreach ($this->propertyList as $field => $info) {
                $fieldValue = isset($data[$field]) ? $data[$field] : null;
                $info = $this->getFormatedPropertyInfo($field, $info);
                
                if ($fieldValue !== null) {
                    $info['value'] = $fieldValue;
                }
                $this->propertyList[$field] = $info;
            }
        }
    }
    
    /**
     * 构造函数
     */
    final public function __construct($data)
    {
        // 格式化实体属性列表
        $this->initPropertyList($data);
    }

    /**
     * 获取实体属性的值
     * @param string $field
     * @return multitype: NULL
     */
    public function __get($field)
    {
        if ($this->have($field)) {
            return $this->propertyList[$field]['value'];
        }
        return null;
    }

    /**
     * 设置实体属性的值
     * @param string $field
     * @param string $value
     */
    public function __set($field, $value)
    {
        if ($this->have($field)) {
            $this->propertyList[$field]['value'] = $value;
        }
    }

    /**
     * 判断实体是否存在某个属性
     * @param string $field
     * @return boolean
     */
    public function have($field)
    {
        return isset($this->propertyList[$field]) ? true : false;
    }

    /**
     * 获取实体属性的字段列表
     */
    public function getFieldList()
    {
        return array_keys($this->propertyList);
    }

    /**
     * 获取实体属性的字段列表
     */
    public function getDataList()
    {
        $dataList = array();
        foreach ($this->propertyList as $field => $info) {
            $dataList[$field] = $info['value'];
        }
        return $dataList;
    }
}
