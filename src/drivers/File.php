<?php
declare(strict_types=1);

namespace ferminfeng\log\drivers;


class File extends LogStrategy
{
    /**
     * @var string 日志主路径
     */
    protected $path;

    /**
     * File constructor
     *
     * @param array $config
     * @throws LogException
     */
    public function __construct(array $config)
    {
        $realPath = $this->getFinalPath($config);
        $this->path = $this->createDir($realPath) . '/';
    }

    /**
     * 创建文件目录
     *
     * @param string $path
     * @return string
     * @throws LogException
     */
    private function createDir(string $path): string
    {
        $code = 2;
        if (is_dir($path)) {
            $code = 0;
        } else {
            $result = @mkdir($path, 0777, true);
            if (!$result) {
                $error = error_get_last();
                $message = $error['message'] ?? '';
                if (strpos($message, 'Permission denied')) {
                    $code = 1;
                } elseif (strpos($message, 'File exists')) {
                    $code = 0;
                }
            } elseif (is_dir($path)) {
                $code = 0;
            }
        }

        if (0 === $code) {
            return $path;
        } elseif (1 === $code) {
            throw new LogException("目录没有创建权限");
        } else {
            throw new LogException("目录创建失败，请检查!");
        }
    }

    /**
     * 写入文本日志
     *
     * @param string $logName
     * @param $logContent
     * @param string $charList
     * @param int $jsonFormatCode
     * @param string $time
     * @return bool
     * @throws LogException
     */
    public function write(string $logName, $logContent, string $charList, int $jsonFormatCode, string $time): bool
    {
        $logContent = is_array($logContent) ? json_encode($logContent, $jsonFormatCode) : $logContent;
        if (is_array($logContent)) {
            $logContent = json_encode($logContent, $jsonFormatCode);
        } elseif (is_object($logContent)) {
            $logContent = print_r($logContent, true);
        }
        $finalPath = $this->path . date('Y-m-d') . '/';
        $newNameArray = $this->resetLogName($logName);
        if (!empty($newNameArray['path'])) {
            $filePath = $this->createDir($finalPath . $newNameArray['path']) . '/' . $newNameArray['logName'];
        } else {
            $filePath = $this->createDir($finalPath) . $newNameArray['logName'];
        }

        return error_log($time . '   ' . $logContent . $charList, 3, $filePath);
    }

    /**
     * 获取存储的日志
     *
     * @param int $size
     * @return mixed
     * @throws LogException
     */
    public function get(int $size)
    {
        throw new LogException('不支持');
    }

    /**
     * 文本类型的永不关闭
     *
     * @return bool
     */
    public function closed(): bool
    {
        return false;
    }

    /**
     * 关闭文件连接此处无需实现
     *
     * @return mixed|void
     */
    public function close()
    {

    }
}
