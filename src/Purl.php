<?php

namespace Mlevent;

class Purl
{
    protected bool    $isSecure   = false;
    protected ?string $request    = null;
    protected ?string $current    = null;
    protected ?string $scheme     = null;
    protected ?string $host       = null;
    protected ?int    $port       = null;
    protected ?string $query      = null;
    protected string  $splitChar  = ',';
    protected array   $params     = [];
    protected ?string $base       = null;
    protected ?string $path       = null;
    protected ?string $fragment   = null;
    protected array   $allowed    = [];
    protected array   $denied     = [];
    protected array   $userParams = [];
    
    /**
     * __construct
     */
    public function __construct()
    {
        $this->isSecure = getenv('HTTPS') && getenv('HTTPS') === 'on';
        $this->host     = getenv('HTTP_HOST');
        $this->request  = getenv('REQUEST_URI');
        $this->current  = $this->getCurrent();

        foreach (parse_url($this->current) as $parseKey => $parseValue) {
            $this->{$parseKey} = $parseValue 
                ? $parseValue 
                : null;
        }

        if (isset($this->query)) {
            $this->parseQuery($this->query);
        }
    }

    /**
     * getCurrent
     *
     * @return string
     */
    public function getCurrent()
    {
        return join('', [($this->isSecure ? 'https' : 'http'), '://', $this->host, $this->request]);
    }
    
    /**
     * parseQuery
     *
     * @param  string $query
     * @return array
     */
    protected function parseQuery(string $query)
    {
        parse_str($this->query, $this->params);
        
        array_walk($this->params, function (&$value, $key) {
            if (!is_array($this->params[$key])) {
                $paramGroup = array_filter(explode($this->splitChar, $this->params[$key]), 'trim');
                if (count($paramGroup)) {
                    $value = $paramGroup;
                }
            }
        });
        return $this->params;
    }
    
    /**
     * base
     *
     * @param  string $base
     * @return string
     */
    public function base(string $base)
    {
        $this->base = !$base ? '/' : $base;
        return $this;
    }
    
    /**
     * path
     *
     * @param mixed $path
     */
    public function path(string $path)
    {
        $this->path = $path !== '/' 
            ? join(['/', ltrim($path, '/')])
            : $path ;

        return $this;
    }
    
    /**
     * params
     *
     * @param mixed $params
     */
    public function params($params)
    {
        if (is_array($params)) {
            array_walk($params, function (&$item, $key) {
                if (!is_array($item)) {
                    $item = (array) $item;
                }
            });
            $this->userParams = $params;
        }
        return $this;
    }
    
    /**
     * fragment
     *
     * @param  string $fragment
     * @return self
     */
    public function fragment(string $fragment)
    {
        $this->fragment = trim($fragment);
        return $this;
    }
    
    /**
     * allow
     *
     * @return self
     */
    public function allow()
    {
        $this->denied = array_keys(array_diff_key($this->params, array_flip(func_get_args())));
        return $this;
    }
    
    /**
     * deny
     *
     * @return self
     */
    public function deny()
    {
        $this->denied = array_keys(array_flip(func_get_args()));
        return $this;
    }
    
    /**
     * push
     *
     * @return string
     */
    public function push()
    {
        if (isset($this->userParams) && isset($this->params)) {

            $params = $this->params;
            
            array_walk($params, function ($item, $key) use (&$params) {
                if (in_array($key, $this->denied)) {
                    unset($params[$key]);
                }
            });

            $this->userParams = array_merge_recursive($params, $this->userParams);

            array_walk($this->userParams, function (&$item) {
                $item = array_unique($item);
            });
        }
        return $this->build();
    }
    
    /**
     * build
     *
     * @return string
     */
    public function build()
    {
        if (sizeof($this->userParams)) {
            $joinParams = array_map(function (&$item) {
                return implode($this->splitChar, $item);
            }, $this->userParams);
        }

        $this->path = !$this->path 
            ? (!$this->base ? $this->path : null) 
            : $this->path;
        
        $this->base = !$this->base 
            ? ($this->scheme.'://'.$this->host) 
            : (($this->base == '/' && $this->path) ? null : $this->base);

        $buildUrl = urldecode(implode(array(
            $this->base,
            $this->path,
            (sizeof($this->userParams) ? '?' : null),
            (sizeof($this->userParams) ? http_build_query($joinParams) : null),
            $this->fragment
        )));

        $this->initBuild();
        
        return $buildUrl;
    }

    /**
     * initBuild
     *
     * @return void
     */
    public function initBuild() 
    {
        $this->base       = null;
        $this->path       = null;
        $this->fragment   = null;
        $this->allowed    = [];
        $this->denied     = [];
        $this->userParams = [];
    }

    /**
     * getPath
     *
     * @param  integer|null $index
     * @return void
     */
    public function getPath(int $index = null)
    {
        if (!is_null($index)) {
            if ($path = explode('/', trim($this->path, '/'))) {
                return isset($path[$index]) ? $path[$index] : null;
            }
        }
        return $this->path;
    }
    
    /**
     * getParams
     *
     * @param  integer|null $index
     * @param  boolean      $isArray
     * @return void
     */
    public function getParams(int $index = null, bool $isArray = false)
    {
        if (!is_null($index)) {
            return isset($this->params[$index]) 
                ? ($isArray ? $this->params[$index] : implode($this->splitChar, $this->params[$index])) 
                : null;
        }
        return $this->params;
    }
    
    /**
     * getAllowedParams
     *
     * @return array
     */
    public function getAllowedParams()
    {
        return array_keys(array_diff_key($this->params, array_flip($this->denied)));
    }
    
    /**
     * getDeniedParams
     *
     * @return array
     */
    public function getDeniedParams()
    {
        return array_keys(array_intersect_key($this->params, array_flip($this->denied)));
    }
    
    /**
     * hasParam
     *
     * @param  string  $param
     * @return boolean
     */
    public function hasParam(string $param)
    {
        return isset($this->params[$param]);
    }
    
    /**
     * hasValue
     *
     * @param  string      $search
     * @param  string|null $param
     * @return boolean
     */
    public function hasValue(string $search, string $param = null)
    {
        if (!isset($search) || !isset($this->params)) {
            return false;
        }
        if (!empty($param)) {
            if (isset($this->params[$param])) {
                return in_array($search, $this->params[$param]);
            }
        } else {
            foreach ($this->params as $item) {
                if(in_array($search, $item)) { 
                    return true;
                }
            }
        }
        return false;
    }
}