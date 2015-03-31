<?php

namespace Platter\Model\Entity;

class Validate extends Base
{

    /**
     * 错误信息
     * @var string
     */
    protected $errorMessage = '';

    /**
     * 格式化实体属性
     */
    protected function getFormatedPropertyInfo($field, $info)
    {
        $info = parent::getFormatedPropertyInfo($field, $info);
        
        if (! isset($info['require'])) {
            $info['require'] = false;
        }
        
        return $info;
    }

    /**
     * 获取验证规则列表
     * @author tabalt
     * @param array $fieldList 字段列表
     * @param array $data
     */
    protected function getValidateRuleList($fieldList = array())
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
            
            $ruleList[$field] = $rule;
        }
        return $ruleList;
    }

    /**
     * 设置错误信息
     * @author tabalt
     * @param string $errorMessage 错误信息
     */
    public function setError($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * 获取错误信息
     * @author tabalt
     * @return string $errorMessage 错误信息
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * 执行验证操作
     * @author tabalt
     * @param array $fieldList 字段列表
     * @param array $data
     */
    public function check($fieldList)
    {
        $ruleList = $this->getValidateRuleList($fieldList);
        $dataList = $this->getDataList();
        
        if (! \Platter\Component\Validate::execute($ruleList, $dataList)) {
            $this->setError(\Platter\Component\Validate::getError());
            return false;
        } else {
            return true;
        }
    }
}
