<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2017/6/17
 * Time: 19:23
 */
namespace lingyin\base;

use lingyin\base\exception\InvalidConfigException;
use lingyin\base\exception\InvalidParamException;
use lingyin\di\Container;
use lingyin\helpers\ArrayHelper;

class Ling
{
    /**
     * @var Application
     */
    public static $app;

    /**
     * @var array 别名
     */
    public static $aliases = ['@lingyin' => __DIR__ . '/..'];

    /**
     * @var Container
     */
    public static $container = null;


    /**
     * 实例化对象
     *
     * @param string|array|callable $type
     * @param array $params
     * @return mixed
     * @throws InvalidConfigException
     */
    public static function createObject($type, array $params = [])
    {
        if (is_string($type)) {
            return static::getContainer()->get($type, $params);
        } elseif (is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return static::getContainer()->get($class, $params, $type);
        } elseif (is_callable($type, true)) {
            return static::getContainer()->invoke($type, $params);
        } elseif (is_array($type)) {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        }

        throw new InvalidConfigException('Unsupported configuration type: ' . gettype($type));
    }

    /**
     * 配置对象属性
     *
     * @param object $object
     * @param array $properties 需配置的属性,格式为[[key=>value']]
     * @return object
     */
    public static function setProperties($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * 获取依赖注入容器
     *
     * @return Container|null
     */
    public static function getContainer()
    {
        if (static::$container == null) {
            static::$container = new Container();
        }

        return static::$container;
    }

    /**
     * 设置别名
     *
     * @param $alias
     * @param $path string | null 完整路径名或为null表示删除别名
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
            }
        } elseif (isset(self::$aliases[$alias])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    /**
     * 将路径别名转换为实际路径
     *
     * @param string $alias 路径别名
     * @param boolean $throwException 是否抛出异常
     * @return string
     * @throws InvalidParamException
     */
    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $path . substr($alias, strlen($name));
                    }
                }
            }
        }

        if ($throwException === false) {
            return false;
        }

        throw new InvalidParamException("Invalid path alias: {$alias}");
    }

    /**
     * 加载配置文件
     *
     * 加载配置文件，环境优先
     *
     * @param $configFile
     * @param null $env
     * @return array
     * @throws InvalidConfigException
     */
    public static function loadConfig($configFile, $env = null)
    {
        if (!is_file($configFile)) {
            throw new InvalidConfigException(sprintf('配置文件"%s"不存在', $configFile));
        } else {
            $config = require($configFile);
        }

        if (null === $env) {
            $env = 'product';
        }

        // @root/config/config.<env>.php
        $file = substr_replace($configFile, $env . '.', -3, 0);
        if (is_file($file)) {
            $config = ArrayHelper::merge($config, require($file));
        }

        return $config;
    }

    /**
     * 自动加载
     *
     * @param $className
     */
    public static function autoload($className)
    {
        if (strpos($className, '\\') === false) {
            return;
        }
        $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
        if ($classFile === false || !is_file($classFile)) {
            return;
        }
        $classFile = realpath($classFile);
        include($classFile);
    }
}