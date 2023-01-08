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
    protected array   $args       = [];
    protected ?string $baseUrl    = null;
    protected ?string $path       = null;
    protected ?string $fragment   = null;
    protected array   $allowed    = [];
    protected array   $denied     = [];
    protected array   $userArgs   = [];
    
    /**
     * __construct
     */
    public function __construct()
    {
        $this->isSecure = getenv('HTTPS') && getenv('HTTPS') === 'on';
        $this->host     = getenv('HTTP_HOST');
        $this->request  = getenv('REQUEST_URI');
        $this->current  = $this->getCurrentUrl();

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
     * getCurrentUrl
     *
     * @return string
     */
    public function getCurrentUrl()
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
        parse_str($this->query, $this->args);
        
        array_walk($this->args, function (&$value, $key) {
            if (!is_array($this->args[$key])) {
                $argGroup = array_filter(explode($this->splitChar, $this->args[$key]), 'trim');
                if (count($argGroup)) {
                    $value = $argGroup;
                }
            }
        });
        return $this->args;
    }
    
    /**
     * baseUrl
     *
     * @param  string $baseUrl
     * @return self
     */
    public function baseUrl(string $baseUrl)
    {
        $this->baseUrl = !$baseUrl ? '/' : $baseUrl;
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
            : $path;

        return $this;
    }
    
    /**
     * args
     *
     * @param mixed $args
     */
    public function args(array $args)
    {
        if (is_array($args)) {
            array_walk($args, function (&$item, $key) {
                if (!is_array($item)) {
                    $item = (array) $item;
                }
            });
            $this->userArgs = $args;
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
        $this->denied = array_keys(array_diff_key($this->args, array_flip(func_get_args())));
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
        if (isset($this->userArgs) && isset($this->args)) {

            $args = $this->args;
            
            array_walk($args, function ($item, $key) use (&$args) {
                if (in_array($key, $this->denied)) {
                    unset($args[$key]);
                }
            });

            $this->userArgs = array_merge_recursive($args, $this->userArgs);

            array_walk($this->userArgs, function (&$item) {
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
        if (sizeof($this->userArgs)) {
            $joinArgs = array_map(function (&$item) {
                return implode($this->splitChar, $item);
            }, $this->userArgs);
        }

        $this->path = !$this->path 
            ? (!$this->baseUrl ? $this->path : null) 
            : $this->path;
        
        $this->baseUrl = !$this->baseUrl 
            ? ($this->scheme.'://'.$this->host) 
            : (($this->baseUrl == '/' && $this->path) ? null : $this->baseUrl);

        $buildUrl = urldecode(implode(array(
            $this->baseUrl,
            $this->path,
            (sizeof($this->userArgs) ? '?' : null),
            (sizeof($this->userArgs) ? http_build_query($joinArgs) : null),
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
        $this->baseUrl    = null;
        $this->path       = null;
        $this->fragment   = null;
        $this->allowed    = [];
        $this->denied     = [];
        $this->userArgs = [];
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
     * getArgs
     *
     * @param  integer|null $index
     * @param  boolean      $isArray
     * @return void
     */
    public function getArgs(int $index = null, bool $isArray = false)
    {
        if (!is_null($index)) {
            return isset($this->args[$index]) 
                ? ($isArray ? $this->args[$index] : implode($this->splitChar, $this->args[$index])) 
                : null;
        }
        return $this->args;
    }
    
    /**
     * getAllowedArgs
     *
     * @return array
     */
    public function getAllowedArgs()
    {
        return array_keys(array_diff_key($this->args, array_flip($this->denied)));
    }
    
    /**
     * getDeniedArgs
     *
     * @return array
     */
    public function getDeniedArgs()
    {
        return array_keys(array_intersect_key($this->args, array_flip($this->denied)));
    }
    
    /**
     * hasArg
     *
     * @param  string  $arg
     * @return boolean
     */
    public function hasArg(string $arg)
    {
        return isset($this->args[$arg]);
    }
    
    /**
     * hasValue
     *
     * @param  string      $search
     * @param  string|null $arg
     * @return boolean
     */
    public function hasValue(string $search, string $arg = null)
    {
        if (!isset($search) || !isset($this->args)) {
            return false;
        }
        if (!empty($arg)) {
            if (isset($this->args[$arg])) {
                return in_array($search, $this->args[$arg]);
            }
        } else {
            foreach ($this->args as $item) {
                if(in_array($search, $item)) { 
                    return true;
                }
            }
        }
        return false;
    }
}