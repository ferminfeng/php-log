<?php
declare(strict_types=1);

namespace ferminfeng\log\drivers;


abstract class LogStrategy
{
    /**
     * 写入日志
     *
     * @param string $logName 日志名称
     * @param string|array|object $logContent 日志内容
     * @param string $charList 分隔符
     * @param int $jsonFormatCode json格式化code
     * @param string $time 发生时间
     * @return bool
     */
    abstract public function write(string $logName, $logContent, string $charList, int $jsonFormatCode, string $time): bool;

    /**
     * 关闭句柄
     *
     * @return mixed
     */
    abstract public function close();

    /**
     * 获取句柄状态
     *
     * @return bool
     */
    abstract public function closed(): bool;

    /**
     * 获取日志
     *
     * @param int $size
     * @return mixed
     */
    abstract public function get(int $size);

    /**
     * 获取最终的path路径
     *
     * @param array $config
     * @return string
     * @throws LogException
     */
    protected function getFinalPath(array $config): string
    {
        if (!isset($config['path']) || !is_string($config['path'])) {
            throw new LogException("no path can write");
        }
        $config['path'] = rtrim($config['path'], '/');
        if (isset($config['project'])) {
            $config['path'] .= '/' . $config['project'];
        }
        return $config['path'];
    }

    /**
     * 重置日志名
     * @param string $logName
     * @return array
     */
    protected function resetLogName(string $logName): array
    {
        $path = '';
        if (is_int(strpos($logName, '/'))) {
            $temp = explode('/', $logName);
            $logName = array_pop($temp);
            $temp = array_values(array_filter($temp));
            $path = implode('/', $temp);
        }
        return ['logName' => $logName, 'path' => $path];
    }
}
