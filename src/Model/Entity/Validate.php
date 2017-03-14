<?php

namespace Platter\Model\Entity;

class Validate extends Base
{

    /**
     * 验证规则列表
     * @var array
     */
    protected $ruleList;

    /**
     * 格式化实体属性
     */
    protected function getFormatedPropertyInfo($field, $info)
    {
        $info = parent::getFormatedPropertyInfo($field, $info);
        
        if (! isset($info['require'])) {
            $info['require'] = false;
        }
        
        if (! isset($info['filter'])) {
            $info['filter'] = false;
        }
        
        if (! isset($info['default'])) {
            $info['default'] = null;
        }
        
        return $info;
    }

    /**
     * 设置验证规则列表
     * @author tabalt
     * @param array $fieldList 字段列表
     * @param array $data
     */
    protected function setValidateRuleList($fieldList = array())
    {
        if (empty($fieldList) || ! is_array($fieldList)) {
            $fieldList = $this->propertyList;
        }
        
        // 验证规则
        $ruleList = array();
        
        foreach ($fieldList as $field => $info) {
            $rule = isset($this->propertyList[$field]) ? array_merge($this->propertyList[$field], $info) : array();
            
            // 字段别名处理
            $rule['field'] = isset($rule['alias']) ? $rule['alias'] : $field;
            
            $rule['name'] = isset($rule['name']) ? $rule['name'] : $field;
            $rule['require'] = isset($rule['require']) ? $rule['require'] : false;
            $rule['filter'] = isset($rule['filter']) ? $rule['filter'] : false;
            
            $ruleList[$field] = $rule;
        }
        $this->ruleList = $ruleList;
    }

    /**
     * 执行验证操作
     * @author tabalt
     * @param array $fieldList 字段列表
     * @param array $data
     */
    public function check($fieldList = array())
    {
        $this->setValidateRuleList($fieldList);
        
        $dataList = $this->toArray();
        
        if (! \Platter\Component\Validate::execute($this->ruleList, $dataList)) {
            $this->setError(\Platter\Component\Validate::getError());
            return false;
        } else {
            return true;
        }
    }

    /**
     * 将实体转换成数组
     */
    public function toArray()
    {
        $dataList = parent::toArray();
        $validateDataList = array();
        
        foreach ($dataList as $key => $value) {
            
            // 验证中规则不存在的字段直接忽略
            if (! isset($this->ruleList[$key])) {
                continue;
            }
            
            $default = isset($this->ruleList[$key]['default']) ? $this->ruleList[$key]['default'] : null;
            $filter = isset($this->ruleList[$key]['filter']) ? $this->ruleList[$key]['filter'] : false;
            
            // 没有值 时，设为默认值
            $value = !is_null($value) ? $value : $default;
            
            if (! is_null($value)) {
                $validateDataList[$key] = function_exists($filter) ? $filter($value) : $value;
            }
        }
        return $validateDataList;
    }
}
