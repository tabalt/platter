<?php

namespace Platter\Component;

class Validate
{

    /**
     * 错误信息
     * @author tabalt
     * @var string
     */
    private static $errorMessage;

    /**
     * 验证规则中必须设置的项目列表
     * @author tabalt
     * @var array
     */
    private static $requiredItemList = array(
        // 验证的字段
        'field' => '', 
        // 验证字段的名称
        'name' => '', 
        // 是否必须 true 则必须　false非必须
        'require' => false
    );

    /**
     * 验证规则中未设置条目的默认值列表
     * @author tabalt
     * @var array
     */
    private static $defaultItemValueList = array(
        // 默认类型 为空则不做判断
        'type' => false, 
        // 匹配字段 为空则不做判断
        'match' => false, 
        // 长度限制 为空则不做判断
        'limitLength' => false, 
        // 最小长度
        'minLength' => false, 
        // 最大长度 最小最大都不为空才做判断
        'maxLength' => false, 
        // 最小数字大小
        'minNumber' => false,
        // 最大数字大小 最小最大都不为空才做判断
        'maxNumber' => false,
        // 正则表达式 为空则不做判断,
        'regex' => false, 
        // 允许的值列表 需传数组
        'valueLimit' => false
    );

    /**
     * 默认的错误信息列表
     * @author tabalt
     * @var array
     */
    private static $defaultMessageTplList = array(
        'empty' => '{%name}不能为空', 
        'lengthLimit' => '{%name}的长度必须为{%limitLength}', 
        'lengthInterval' => '{%name}的长度必须在{%minLength}和{%maxLength}之间', 
        'numberInterval' => '{%name}的大小必须在{%minNumber}和{%maxNumber}之间', 
        'regex' => '{%name}的格式不正确'
    );
    
    // TODO 完善默认字段类型 url chinese
    /**
     * 默认的字段类型列表
     * @author tabalt
     * @var array
     */
    private static $defaultTypeList = array(
        'qq' => array(
            // 最小长度
            'minLength' => 5, 
            // 最大长度
            'maxLength' => 11, 
            // 正则表达式 为空则不做判断
            'regex' => "/^[1-9][0-9]{4,10}$/"
        ), 
        'email' => array(
            // 最小长度
            'minLength' => 6, 
            // 最大长度
            'maxLength' => 50, 
            // 正则表达式 为空则不做判断
            'regex' => "/^[\da-z]\w*@[\da-z]+\.[\da-z]+(\.[a-z]+)*$/i"
        ), 
        'password' => array(
            // 最小长度
            'minLength' => 6, 
            // 最大长度
            'maxLength' => 16, 
            'regex' => '/^[\w\d\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\-\{\}\|\]\[\:\"\'\;\<\>\?\/\.\,\\\]{6,16}$/'
        ), 
        'number' => array(
            // 正则表达式
            'regex' => "/^[0-9]{1,}$/"
        ), 
        'url' => array(
            // 正则表达式
            'regex' => '/^(http|https):\/\/([a-z\d_-]+\.)+[a-z]{2,}(:[\d]{2,})?(\/[a-z\d_=\/\%\#\&\-\$\.\?]*)?$/i'
        )
    );

    /**
     * 验证规则列表
     * @author tabalt
     * @var array
     */
    private static $ruleList;

    /**
     * 解析消息
     * @author tabalt
     * @param string $messageTpl
     * @param array $rule 验证规则
     * @return string $message
     */
    private static function parseMessage($messageTpl, $rule)
    {
        $searchList = array(
            '{%name}', 
            '{%minLength}', 
            '{%maxLength}', 
            '{%minNumber}', 
            '{%maxNumber}', 
            '{%limitLength}'
        );
        $replaceList = array(
            isset($rule['name']) ? $rule['name'] : '', 
            isset($rule['minLength']) ? $rule['minLength'] : '', 
            isset($rule['maxLength']) ? $rule['maxLength'] : '', 
            isset($rule['minNumber']) ? $rule['minNumber'] : '', 
            isset($rule['maxNumber']) ? $rule['maxNumber'] : '', 
            isset($rule['limitLength']) ? $rule['limitLength'] : ''
        );
        $message = str_replace($searchList, $replaceList, $messageTpl);
        return $message;
    }

    /**
     * 解析验证规则
     * @author tabalt
     * @param array $rule
     * @return boolean true|false
     */
    private static function formatRule($rule)
    {
        // 未定义的项目列表
        $undefinedItemList = array_diff_key(self::$requiredItemList, $rule);
        if (! empty($undefinedItemList)) {
            self::$errorMessage = "{$rule['field']}的验证规则缺少如下项目：" . implode('，', array_keys($undefinedItemList));
            return false;
        }
        return $rule;
    }

    /**
     * 设置验证规则
     * @author tabalt
     * @param array $ruleList
     * @return boolean true|false
     */
    public static function setRuleList($ruleList)
    {
        if (empty($ruleList)) {
            self::$errorMessage = "验证规则不能为空";
            return false;
        }
        // 格式化验证规则
        $formatedRuleList = array();
        foreach ($ruleList as $key => $rule) {
            // 处理规则
            $rule = self::formatRule($rule);
            if (! $rule) {
                return false;
            }
            $formatedRuleList[$rule['field']] = $rule;
        }
        self::$ruleList = $formatedRuleList;
        return true;
    }

    /**
     * 获取错误信息
     * @author tabalt
     * @return string $errorMessage
     */
    public static function getError()
    {
        return self::$errorMessage;
    }

    /**
     * 数据验证
     * @author tabalt
     * @param array $ruleList 验证规则
     * @param array $data 要验证的数据
     * @return boolean true/false
     */
    public static function execute($ruleList = array(), $data = array())
    {
        // 设置验证规则
        if (! self::setRuleList($ruleList)) {
            return false;
        }
        
        // 规则验证
        foreach (self::$ruleList as $rule) {
            // TODO 考虑数据为数组的情况
            $fieldValue = isset($data[$rule['field']]) ? $data[$rule['field']] : false;
            $fieldValue = str_replace("　", '', trim($fieldValue));
            // 值是否为空
            $isValueEmpty = false;
            if ($fieldValue === false || $fieldValue === '') {
                $isValueEmpty = true;
            }
            
            // 是否必须 true 必须 如果为空 报错；false 非必须 如果为空 则continue
            if ($rule['require'] && $isValueEmpty) {
                self::$errorMessage = self::parseMessage(self::$defaultMessageTplList['empty'], $rule);
                return false;
            } else if (! $rule['require'] && $isValueEmpty) {
                continue;
            }
            
            // 验证类型type type不存在则报错 存在以新定义规则为准 合并type预设的规则
            if (isset($rule['type']) && ! empty($rule['type'])) {
                if (! array_key_exists($rule['type'], self::$defaultTypeList)) {
                    self::$errorMessage = "需验证的类型{$rule['type']}不存在";
                    return false;
                }
                // 以新定义规则为准 合并type预设的规则
                $rule = array_merge(self::$defaultTypeList[$rule['type']], $rule);
            }
            // 覆盖默认值列表
            $rule = array_merge(self::$defaultItemValueList, $rule);
            // 验证匹配match
            if ($rule['match']) {
                $matchValue = isset($data[$rule['match']]) ? trim($data[$rule['match']]) : false;
                if (! array_key_exists($rule['match'], self::$ruleList) || $matchValue === false) {
                    self::$errorMessage = "要匹配的字段{$rule['match']}不存在";
                    return false;
                }
                if ($fieldValue !== $matchValue) {
                    self::$errorMessage = "{$rule['name']}和" . self::$ruleList[$rule['match']]['name'] . "不匹配";
                    return false;
                }
            }
            
            // 验证长度限制
            if ($rule['limitLength']) {
                if (\Platter\Component\Helper::stringLength($fieldValue) != $rule['limitLength']) {
                    self::$errorMessage = self::parseMessage(self::$defaultMessageTplList['lengthLimit'], $rule);
                    return false;
                }
            }
            
            // 验证长度区间
            if ($rule['minLength'] && $rule['maxLength']) {
                if (\Platter\Component\Helper::stringLength($fieldValue) < $rule['minLength'] || \Platter\Component\Helper::stringLength($fieldValue) > $rule['maxLength']) {
                    self::$errorMessage = self::parseMessage(self::$defaultMessageTplList['lengthInterval'], $rule);
                    return false;
                }
            }
            
            // 验证大小区间
            if ($rule['minNumber'] && $rule['maxNumber']) {
                if (intval($fieldValue) < $rule['minNumber'] || intval($fieldValue) > $rule['maxNumber']) {
                    self::$errorMessage = self::parseMessage(self::$defaultMessageTplList['numberInterval'], $rule);
                    return false;
                }
            }
            
            // 验证正则
            if ($rule['regex']) {
                if (! preg_match($rule['regex'], $fieldValue)) {
                    self::$errorMessage = self::parseMessage(self::$defaultMessageTplList['regex'], $rule);
                    return false;
                }
            }
            
            // 验证允许的值列表
            if ($rule['valueLimit'] && is_array($rule['valueLimit'])) {
                if (! in_array($fieldValue, $rule['valueLimit'])) {
                    self::$errorMessage = "{$rule['name']}的值不在允许的范围";
                    return false;
                }
            }
        }
        return true;
    }
}