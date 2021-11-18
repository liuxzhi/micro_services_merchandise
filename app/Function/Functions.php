<?php
declare(strict_types=1);

use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Utils\ApplicationContext;


if (!function_exists('di')) {
    /**
     * 返回容器，或者注入对象
     *
     * @param null $id
     *
     * @throws TypeError
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return mixed|\Psr\Container\ContainerInterface
     */
    function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get((string)$id);
        }

        return $container;
    }
}

if (!function_exists('readPathFiles')) {
    /**
     * 取出某目录下所有php文件的文件名.
     *
     * @param string $path 文件夹目录
     *
     * @return array 文件名
     */
    function readPathFiles(string $path): array
    {
        $data = [];
        if (!is_dir($path)) {
            return $data;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..', '.DS_Store'])) {
                continue;
            }
            $data[] = preg_replace('/(\w+)\.php/', '$1', $file);
        }

        return $data;
    }
}

/*
 * 模型服务与契约的依赖配置.
 * @param string $path 契约与服务的相对路径
 * @return array 依赖数据
 */
if (!function_exists('serviceMap')) {
    function serviceMap(string $path = 'app'): array
    {
        $services    = readPathFiles(BASE_PATH . '/' . $path . '/Service');
        $spacePrefix = ucfirst($path);

        $dependencies = [];
        foreach ($services as $service) {
            if (strpos($service, "Abstract") === false) {
                $dependencies[$spacePrefix . '\\Contract\\' . $service . 'Interface'] = $spacePrefix . '\\Service\\' . $service;
            }
        }

        return $dependencies;
    }
}


if (!function_exists('format_throwable')) {
    /**
     * @param Throwable $throwable
     *
     * @return string
     * @throws TypeError
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function format_throwable(Throwable $throwable): string
    {
        return di()
            ->get(FormatterInterface::class)
            ->format($throwable);
    }
}


if (!function_exists('cartesian')) {
    /**
     * 计算多个集合的笛卡尔积
     *
     * @param $sets
     *
     * @return array
     */
    function cartesian($sets)
    {
        $result = [];

        if (count($sets) > 1) {
            for ($i = 0, $count = count($sets); $i < $count - 1; ++$i) {
                if ($i == 0) {
                    $result = $sets[$i];
                }
                $tmp = [];
                foreach ($result as $res) {
                    foreach ($sets[$i + 1] as $set) {
                        $tmp[] = $res . ',' . $set;
                    }
                }
                $result = $tmp;
            }
        } else {
            $result = $sets[0];
        }

        return $result;
    }
}

if(!function_exists('apiReturn')) {
    function apiReturn(int $code = 0, string $message = "", array $body = []){
        return [
            'code' => $code,
            'message' => $message,
            'body' => (object)$body
        ];
    }
}














