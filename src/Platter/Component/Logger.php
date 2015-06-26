<?php

namespace Platter\Component;

class Logger
{

    private static $logPath = './log/';

    /**
     * 日志文件名模板
     * @author tabalt
     * @var string
     */
    private static $fileTpl = '%type.log';

    /**
     * 日志内容模板
     * @author tabalt
     * @var string
     */
    private static $contentTpl = '%d{Y-m-d H:i:s} [%type] %content in %file at %line%n';

    /**
     * 日志类型列表
     * @author tabalt
     * @var array
     */
    private static $typeList = array(
        'trace', 
        'debug', 
        'info', 
        'warning', 
        'error', 
        'notice', 
        'fatal', 
        'sql', 
        'api', 
        'cache', 
        'queue', 
        'exception', 
        'framework'
    );

    /**
     * 解析模板
     * @author tabalt
     * @param string $type
     * @param string $template
     * @return $template
     */
    private static function parseTemplate($type, $template)
    {
        $search = array(
            '%type', 
            '%n'
        );
        $replace = array(
            $type, 
            "\n"
        );
        $template = str_replace($search, $replace, $template);
        // 时间处理
        $replace_callback = function ($matches)
        {
            return date($matches[1]);
        };
        $template = preg_replace_callback('/%d{(.*)}/', $replace_callback, $template);
        return $template;
    }

    /**
     * 设置配置
     * @author tabalt
     * @param array $config
     * @return void
     */
    public static function setConfig($logPath, $fileTpl = '%type.log', $contentTpl = '%d{Y-m-d H:i:s} [%type] %content in %file at %line%n')
    {
        self::$logPath = $logPath;
        self::$fileTpl = $fileTpl;
        self::$contentTpl = $contentTpl;
    }

    /**
     * 写入日志
     * @author tabalt
     * @param string $type
     * @param string $content
     * @return void
     */
    public static function write($type, $content, $self = false)
    {
        // 过滤日志类型
        if (! in_array(strtolower($type), self::$typeList)) {
            $type = str_replace('.', '', strip_tags($type));
        }
        // 获取back trace
        $debugBacktraceList = debug_backtrace();
        // 验证是否为类内调用
        if ($self === true && isset($debugBacktraceList[1])) {
            // 如果是类内调用, 取下标为1的元素
            $debugBacktrace = $debugBacktraceList[1];
        } else {
            // 如果非类内调用, 取下标为0的元素
            $debugBacktrace = $debugBacktraceList[0];
        }
        // 替换日志内容
        $content = str_replace('%content', $content, self::parseTemplate(strtoupper($type), self::$contentTpl));
        $search = array(
            '%file', 
            '%line'
        );
        $replace = array(
            $debugBacktrace['file'], 
            $debugBacktrace['line']
        );
        $content = str_replace($search, $replace, $content);
        $filePath = self::parseTemplate($type, self::$logPath . self::$fileTpl);
        $dirName = dirname($filePath);
        if (! is_dir($dirName)) {
            mkdir($dirName);
        }
        // 写入文件
        \Platter\Component\File::asyncWrite($filePath, $content, 'a');
    }

    /**
     * 写入trace类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function trace($content)
    {
        self::write('trace', $content, true);
    }

    /**
     * 写入debug类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function debug($content)
    {
        self::write('debug', $content, true);
    }

    /**
     * 写入info类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function info($content)
    {
        self::write('info', $content, true);
    }

    /**
     * 写入warning类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function warning($content)
    {
        self::write('warning', $content, true);
    }

    /**
     * 写入error类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function error($content)
    {
        self::write('error', $content, true);
    }

    /**
     * 写入notice类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function notice($content)
    {
        self::write('notice', $content, true);
    }

    /**
     * 写入fatal类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function fatal($content)
    {
        self::write('fatal', $content, true);
    }

    /**
     * 写入sql类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function sql($content)
    {
        self::write('sql', $content, true);
    }

    /**
     * 写入cache类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function cache($content)
    {
        self::write('cache', $content, true);
    }

    /**
     * 写入queue类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function queue($content)
    {
        self::write('queue', $content, true);
    }

    /**
     * 写入exception类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function exception($content)
    {
        self::write('exception', $content, true);
    }

    /**
     * 写入api类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function api($content)
    {
        self::write('api', $content, true);
    }

    /**
     * 写入framework类型日志
     * @author tabalt
     * @see Logger::write
     * @param string $content
     * @return void
     */
    public static function framework($content)
    {
        self::write('framework', $content, true);
    }
}