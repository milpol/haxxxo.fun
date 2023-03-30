<?php

namespace Stooge;

use Exception;
use LogicException;
use RuntimeException;

interface Handler
{
    function handle(Request $request, Response $response);
}

class NullHandler implements Handler
{
    function handle(Request $request, Response $response)
    {
        throw new LogicException('Null pointer.');
    }
}

class PassHandler implements Handler
{
    function handle(Request $request, Response $response)
    {
    }
}

class SetHeaderHandler implements Handler
{
    private $name;
    private $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    function handle(Request $request, Response $response)
    {
        $response->setHeader($this->name, $this->value);
    }
}

class StaticHandler implements Handler
{
    private $statusCode;
    private $entity;

    public function __construct($statusCode = 200, $entity = '')
    {
        $this->statusCode = $statusCode;
        $this->entity = $entity;
    }

    function handle(Request $request, Response $response)
    {
        $response
            ->setStatusCode($this->statusCode)
            ->setEntity($this->entity);
    }
}

class Request
{
    private $time;
    private $scheme;
    private $method;
    private $rootPath = null;
    private $requestUri = null;
    private $headers = array();
    private $queryParameters = array();
    private $pathParameters = array();
    private $sessionParameters = array();
    private $body = '';
    private $stickyHandler;

    public function __construct()
    {
        $this->stickyHandler = new NullHandler();
    }

    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
        return $this;
    }

    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setQueryParameters(array $queryParameters)
    {
        $this->queryParameters = $queryParameters;
        return $this;
    }

    public function setPathParameters(array $pathParameters)
    {
        $this->pathParameters = $pathParameters;
        return $this;
    }

    public function setSessionParameters(array $sessionParameters)
    {
        $this->sessionParameters = $sessionParameters;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setStickyHandler(Handler $stickyHandler)
    {
        $this->stickyHandler = $stickyHandler;
        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function isHeader($name, $value)
    {
        return isset($this->headers[$name]) &&
            strpos($this->headers[$name], $value) !== false;
    }

    public function isJsonContentTypeRequest()
    {
        return $this->isHeader('Content-Type', 'application/json');
    }

    public function getPathParameterAsInt($name, $defaultValue = -1)
    {
        return (int)$this->getPathParameter($name, $defaultValue);
    }

    public function getPathParameter($name, $defaultValue = '')
    {
        return isset($this->pathParameters[$name]) ?
            $this->pathParameters[$name] : $defaultValue;
    }

    public function getQueryParameterAsInt($name, $defaultValue = -1)
    {
        return (int)$this->getQueryParameter($name, $defaultValue);
    }

    public function getQueryParameter($name, $defaultValue = '')
    {
        return isset($this->queryParameters[$name]) ?
            $this->queryParameters[$name] : $defaultValue;
    }

    public function getSessionParameterAsInt($name, $defaultValue = -1)
    {
        return (int)$this->getSessionParameter($name, $defaultValue);
    }

    public function getSessionParameter($name, $defaultValue = '')
    {
        return isset($this->sessionParameters[$name]) ?
            $this->sessionParameters[$name] : $defaultValue;
    }

    public function getRequestPath()
    {
        if ($this->rootPath == null || $this->requestUri == null) {
            throw new RuntimeException();
        }
        $requestPath = str_replace($this->rootPath, '', $this->requestUri);
        $requestPathParts = explode('?', $requestPath);
        return $requestPathParts[0];
    }

    public function isRequestPathStartWith($part)
    {
        return substr($this->getRequestPath(), 0, strlen($part)) == $part;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getBodyAsArray()
    {
        if (empty($this->body)) {
            return array();
        } else {
            return $this->isJsonContentTypeRequest() ?
                json_decode($this->body, true) :
                unserialize($this->body);
        }
    }

    public function getStickyHandler()
    {
        return $this->stickyHandler;
    }
}

class Response
{
    private $statusCode = 200;
    private $headers = array();
    private $entity = '';

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    public function setJsonEntity($entity)
    {
        $this->entity = json_encode($entity);
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}

class Stooge
{
    const VERSION = '0.0.1';

    private $hooks = array(
        'PRE' => array(),
        'POST' => array()
    );
    private $routes = array(
        'OPTIONS' => array(),
        'GET' => array(),
        'POST' => array(),
        'PUT' => array(),
        'PATCH' => array(),
        'DELETE' => array()
    );
    private $notFoundHandler;
    private $serverErrorHandler;

    public function __construct()
    {
        $this->notFoundHandler = new StaticHandler(404);
        $this->serverErrorHandler = new StaticHandler(500);
    }

    public function preHook(Handler $handler)
    {
        $this->hooks['PRE'][] = $handler;
        return $this;
    }

    public function postHook(Handler $handler)
    {
        $this->hooks['POST'][] = $handler;
        return $this;
    }

    public function options($path, Handler $handler)
    {
        $this->setHandler('OPTIONS', $path, $handler);
        return $this;
    }


    public function get($path, Handler $handler)
    {
        $this->setHandler('GET', $path, $handler);
        return $this;
    }

    public function post($path, Handler $handler)
    {
        $this->setHandler('POST', $path, $handler);
        return $this;
    }

    public function put($path, Handler $handler)
    {
        $this->setHandler('PUT', $path, $handler);
        return $this;
    }

    public function patch($path, Handler $handler)
    {
        $this->setHandler('PATCH', $path, $handler);
        return $this;
    }

    public function delete($path, Handler $handler)
    {
        $this->setHandler('DELETE', $path, $handler);
        return $this;
    }

    private function setHandler($method, $path, Handler $handler)
    {
        $this->routes[$method][$path] = $handler;
    }

    public function setNotFoundHandler(Handler $notFoundHandler)
    {
        $this->notFoundHandler = $notFoundHandler;
        return $this;
    }

    public function setServerErrorHandler(Handler $serverErrorHandler)
    {
        $this->serverErrorHandler = $serverErrorHandler;
        return $this;
    }

    private function getRequest()
    {
        $request = new Request();
        return $request
            ->setTime($_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time())
            ->setScheme(isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : '')
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setRootPath($this->getRootPath($_SERVER))
            ->setRequestUri($_SERVER['REQUEST_URI'])
            ->setHeaders($this->getRequestHeaders($_SERVER))
            ->setQueryParameters($_GET)
            ->setSessionParameters(isset($_SESSION) ? $_SESSION : array())
            ->setBody(file_get_contents('php://input'));
    }

    private function getRootPath(array $rawRequest)
    {
        $scriptName = $rawRequest['SCRIPT_NAME'];
        $requestUri = $rawRequest['REQUEST_URI'];
        return (strpos($requestUri, $scriptName) !== false) ?
            $scriptName :
            str_replace('\\', '', dirname($scriptName));
    }

    private function getRequestHeaders(array $rawRequest)
    {
        $headers = array();
        foreach ($rawRequest as $key => $value) {
            if (substr($key, 0, 5) == 'HTTP_') {
                $header = str_replace(' ', '-',
                    ucwords(str_replace('_', ' ',
                        strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        if (isset($rawRequest['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $rawRequest['CONTENT_TYPE'];
        }
        return $headers;
    }

    private function getHandler(Request $request)
    {
        $methodRoutes = $this->routes[$request->getMethod()];
        if (!empty($methodRoutes)) {
            foreach ($methodRoutes as $route => $handler) {
                if ($this->matchRoute($request, $route)) {
                    return ($request->getStickyHandler() instanceof NullHandler) ?
                        $handler :
                        $request->getStickyHandler();
                }
            }
        }
        return new NullHandler();
    }

    private function matchRoute(Request $request, $route)
    {
        $requestPathParts = explode('/', $request->getRequestPath());
        $routeParts = explode('/', $route);
        $pathLevels = sizeof($requestPathParts);
        if ($pathLevels == sizeof($routeParts)) {
            $pathParameters = array();
            for ($i = 0; $i < $pathLevels; ++$i) {
                if ($this->isParameter($routeParts[$i])) {
                    $pathParameters[substr($routeParts[$i], 1, -1)] =
                        $requestPathParts[$i];
                } else {
                    if ($routeParts[$i] != '*' &&
                        strcasecmp($routeParts[$i], $requestPathParts[$i]) != 0) {
                        return false;
                    }
                }
            }
            $request->setPathParameters($pathParameters);
            return true;
        }
        return false;
    }

    private function isParameter($routePart)
    {
        return (substr($routePart, 0, 1) === '{' &&
            substr($routePart, -1) === '}');
    }

    private function respond(Response $response)
    {
        header('X-PHP-Response-Code: ' . $response->getStatusCode(),
            true, $response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header(implode(': ', array($name, $value)));
        }
        echo $response->getEntity();
    }

    public function foolAround()
    {
        $response = new Response();
        $request = $this->getRequest();
        try {
            foreach ($this->hooks['PRE'] as $preHook) {
                $preHook->handle($request, $response);
            }
            $handler = $this->getHandler($request);
            if ($handler instanceof NullHandler) {
                $this->notFoundHandler->handle($request, $response);
            } else {
                $handler->handle($request, $response);
            }
            foreach ($this->hooks['POST'] as $postHook) {
                $postHook->handle($request, $response);
            }
        } catch (Exception $e) {
            error_log('Fatal error: ' . $e->getMessage() . ' [' . $e->getFile() . ':' . $e->getLine() . ']');
            $this->serverErrorHandler->handle($request, $response);
        }
        $this->respond($response);
    }
}
