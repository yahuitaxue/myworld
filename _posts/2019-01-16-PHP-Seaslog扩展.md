---
title: Seaslog扩展
author: Yahui
layout: php
---

Seaslog扩展介绍

<pre>
注：可以根据项目模块不同设置不同模块的目录，如果不初始化路径，会使用php.ini默认配置的路径
SeasLog::setBasePath('/log/base_test');
下载地址：http://pecl.php.net/package/SeasLog


seaslog的配置，加载扩展后，可以放在php.ini中

[SeasLog]
;configuration for php SeasLog module
extension = seaslog.so

;Default Log Base Path
seaslog.default_basepath = "/var/log/www"

;Default Logger Path
seaslog.default_logger = "default"

;The DateTime Style.  Default "Y-m-d H:i:s"
seaslog.default_datetime_format = "Y-m-d H:i:s"

;Default Log template. 
;Default "%T | %L | %P | %Q | %t | %M"
seaslog.default_template = "%T | %L | %P | %Q | %t | %M"

;Switch use the logger with type.
;1-Y 0-N(Default)
seaslog.disting_type = 0

;Switch use the logger with hour.
;1-Y 0-N(Default)
seaslog.disting_by_hour = 0

;Switch use the log buffer with memory.
;1-Y 0-N(Default)
seaslog.use_buffer = 0

;The buffer size
seaslog.buffer_size = 100

;Record logger level. 
;0-EMERGENCY 1-ALERT 2-CRITICAL 3-ERROR 4-WARNING 5-NOTICE 6-INFO 7-DEBUG 8-ALL
;Default 8 (All of them).
;
;   Tips: The configuration item has changed since the 1.7.0 version.
;   Before the 1.7.0 version, the smaller the value, the more logs are taken according to the level: 
;   0-all 1-debug 2-info 3-notice 4-warning 5-error 6-critical 7-alert 8-emergency
;   Before the 1.7.0 version, Default 0 (All of them).
seaslog.level = 8

;Automatic Record final error with default logger. 
;1-Y(Default) 0-N
seaslog.trace_error = 1

;Automatic Record exception with default logger. 
;1-Y 0-N(Default)
seaslog.trace_exception = 0

;Switch the Record Log Data Store.     
;1File 2TCP 3UDP (Switch default 1)
seaslog.appender = 1

;If you use  Record TCP or UDP, configure this remote ip.
;Default "127.0.0.1"
seaslog.remote_host = "127.0.0.1"

;If you use Record TCP or UDP, configure this remote port.
;Default 514
seaslog.remote_port = 514

;Trim the \n and \r in log message.
;1-On 0-Off(Default)
seaslog.trim_wrap = 0

;Switch throw SeasLog exception.
;1-On(Default) 0-Off
seaslog.throw_exception = 1

;Switch ignore SeasLog warning.
;1-On(Default) 0-Off
seaslog.ignore_warning = 1


PHP文件可以使用的一些常量与方法

/**
 * @author neeke@php.net
 * Date: 14-1-27 16:47
 */
define('SEASLOG_ALL', 'ALL');
define('SEASLOG_DEBUG', 'DEBUG');
define('SEASLOG_INFO', 'INFO');
define('SEASLOG_NOTICE', 'NOTICE');
define('SEASLOG_WARNING', 'WARNING');
define('SEASLOG_ERROR', 'ERROR');
define('SEASLOG_CRITICAL', 'CRITICAL');
define('SEASLOG_ALERT', 'ALERT');
define('SEASLOG_EMERGENCY', 'EMERGENCY');
define('SEASLOG_DETAIL_ORDER_ASC', 1);
define('SEASLOG_DETAIL_ORDER_DESC', 2);

class SeasLog
{
    public function __construct()
    {
        #SeasLog init
    }

    public function __destruct()
    {
        #SeasLog distroy
    }

    /**
     * Set The basePath
     *
     * @param $basePath
     *
     * @return bool
     */
    static public function setBasePath($basePath)
    {
        return TRUE;
    }

    /**
     * Get The basePath
     *
     * @return string
     */
    static public function getBasePath()
    {
        return 'the base_path';
    }
    
    /**
     * Set The requestID
     * @param string
     * @return bool
     */
    static public function setRequestID($request_id){
        return TRUE;
    }
    /**
     * Get The requestID
     * @return string
     */
    static public function getRequestID(){
        return uniqid();
    }

    /**
     * Set The logger
     * @param $module
     *
     * @return bool
     */
    static public function setLogger($module)
    {
        return TRUE;
    }

    /**
     * Get The lastest logger
     * @return string
     */
    static public function getLastLogger()
    {
        return 'the lastLogger';
    }

    /**
     * Set The DatetimeFormat
     * @param $format
     *
     * @return bool
     */
    static public function setDatetimeFormat($format)
    {
        return TRUE;
    }

    /**
     * Get The DatetimeFormat
     * @return string
     */
    static public function getDatetimeFormat()
    {
        return 'the datetimeFormat';
    }

    /**
     * Count All Types（Or Type）Log Lines
     * @param string $level
     * @param string $log_path
     * @param null   $key_word
     *
     * @return array | long
     */
    static public function analyzerCount($level = 'all', $log_path = '*', $key_word = NULL)
    {
        return array();
    }

    /**
     * Get The Logs As Array
     *
     * @param        $level
     * @param string $log_path
     * @param null   $key_word
     * @param int    $start
     * @param int    $limit
     * @param        $order SEASLOG_DETAIL_ORDER_ASC(Default)  SEASLOG_DETAIL_ORDER_DESC
     *
     * @return array
     */
    static public function analyzerDetail($level = SEASLOG_INFO, $log_path = '*', $key_word = NULL, $start = 1, $limit = 20, $order = SEASLOG_DETAIL_ORDER_ASC)
    {
        return array();
    }

    /**
     * Get The Buffer In Memory As Array
     *
     * @return array
     */
    static public function getBuffer()
    {
        return array();
    }

    /**
     * Flush The Buffer Dump To Writer Appender
     *
     * @return bool
     */
    static public function flushBuffer()
    {
        return TRUE;
    }

    /**
     * Record Debug Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function debug($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_DEBUG
    }

    /**
     * Record Info Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function info($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_INFO
    }

    /**
     * Record Notice Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function notice($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_NOTICE
    }

    /**
     * Record Warning Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function warning($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_WARNING
    }

    /**
     * Record Error Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function error($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_ERROR
    }

    /**
     * Record Critical Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function critical($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_CRITICAL
    }

    /**
     * Record Alert Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function alert($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_ALERT
    }

    /**
     * Record Emergency Log
     *
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function emergency($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_EMERGENCY
    }

    /**
     * The Common Record Log Function
     * @param        $level
     * @param        $message
     * @param array  $content
     * @param string $module
     */
    static public function log($level, $message, array $content = array(), $module = '')
    {

    }
}

注意：
	1、不要使用在虚拟主机中，或者不能自定义环境的情况下使用。
	2、不建议在较大集群服务中使用。

</pre>