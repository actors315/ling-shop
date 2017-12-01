<?php
/**
 * Created by PhpStorm.
 * User: huanjin
 * Date: 2017/6/18
 * Time: 16:36
 */

namespace lingyin\base;

use lingyin\base\exception\ExitException;
use lingyin\web\router\Route;
use lingyin\web\View;

/**
 * Class Application
 * @package lingyin\base
 *
 */
abstract class Application extends Module
{

    /**
     * 需要在初始化时被加载的组件
     *
     * @var array
     */
    public $bootstrap = [];

    /**
     * @var array 别名
     */
    public $aliases = [];

    public function __construct($config = [])
    {
        Ling::$app = $this;

        $this->preInit($config);
        Component::__construct($config);
    }

    abstract function handleRequest($request);

    public function run()
    {
        try {

            $this->handleRequest($this->getRequest());

        } catch (ExitException $e) {

            return $e->statusCode;
        }
    }

    public function init()
    {
        parent::init();
        $this->bootstrap();
    }

    /**
     * 初始化前置操作
     *
     * @param $config
     */
    protected function preInit(&$config)
    {

    }

    /**
     * 初始化操作
     */
    protected function bootstrap()
    {

        foreach ($this->aliases as $alias => $path) {
            Ling::setAlias($alias, $path);
        }

        foreach ($this->bootstrap as $class) {
            $component = null;

            if (is_string($class)) {

            }

            if ($component === null) {
                $component = Ling::createObject($class);
            }

            if ($component instanceof BootstrapInterface) {
                $component->bootstrap($this);
            }
        }
    }

    /**
     * 获取请求处理对象
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * 获取响应处理对象
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * 获取uri解板对象
     *
     * @return UriInterface
     */
    public function getUriManager()
    {
        return $this->get('uri');
    }

    /**
     * 获取路由对象
     *
     * @return RouteInterface | Route
     */
    public function getRouter()
    {
        return $this->get('route');
    }

    /**
     * 获取view对象
     *
     * @return View
     */
    public function getView()
    {
        return $this->get('view');
    }

}