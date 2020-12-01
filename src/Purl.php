<?php

    namespace mlevent;

    class Purl
    {
        public $isSecure,
               $request,
               $current,
               $scheme,
               $host,
               $port,
               $path,
               $query,
               $fragment,
               $params,
               $splitChar = ',',
               $build;

        public function initBuild()
        {
            $this->build = [

                'path'     => NULL,
                'params'   => [],
                'deny'     => [],
                'fragment' => NULL
            ];
        }

        public function __construct()
        {
            $this->initBuild();

            $this->isSecure = getenv('HTTPS') && getenv('HTTPS') === 'on';
            $this->host     = getenv('HTTP_HOST');
            $this->request  = getenv('REQUEST_URI');
            $this->current  = join("", array($this->isSecure ? "https" : "http", "://", $this->host, $this->request));

            $parseUrl = parse_url($this->current);

            $this->scheme   = isset($parseUrl['scheme'])   ? $parseUrl['scheme']   : NULL;
            $this->host     = isset($parseUrl['host'])     ? $parseUrl['host']     : NULL;
            $this->port     = isset($parseUrl['port'])     ? $parseUrl['port']     : NULL;
            $this->path     = isset($parseUrl['path'])     ? $parseUrl['path']     : NULL;
            $this->query    = isset($parseUrl['query'])    ? $parseUrl['query']    : NULL;
            $this->fragment = isset($parseUrl['fragment']) ? $parseUrl['fragment'] : NULL;

            if(isset($this->query)) $this->parseQuery($this->query);
        }

        public function parseQuery(String $query)
        {
            parse_str($this->query, $this->params);

            array_walk($this->params, function(&$value, $key){
                
                if(!is_array($this->params[$key]))
                {
                    $paramGroup = array_filter(explode($this->splitChar, $this->params[$key]), 'trim');

                    if(count($paramGroup)) 
                        $value = $paramGroup;
                }
            });
            return $this->params;
        }

        public function path($path)
        {
            $this->build['path'] = ltrim($path, '/');
            return $this;
        }

        public function params($params)
        {
            if(is_array($params)){

                array_walk($params, function(&$item, $key){

                    if(!is_array($item)) $item = (array)$item;
                });

                $this->build['params'] = $params;
            }
            return $this;
        }

        public function fragment($fragment)
        {
            $this->build['fragment'] = trim($fragment);
            return $this;
        }

        public function allow()
        {
            $this->build['deny'] = array_keys(array_diff_key($this->params, array_flip(func_get_args())));
            return $this;
        }

        public function deny()
        {
            $this->build['deny'] = array_keys(array_flip(func_get_args()));
            return $this;
        }

        public function push()
        {
            if(isset($this->build['params']) && isset($this->params))
            {
                $getParams = $this->params;
                
                array_walk($getParams, function($item, $key) use(&$getParams){
                    
                    if(in_array($key, $this->build['deny'])) unset($getParams[$key]);
                });

                $this->build['params'] = array_merge_recursive($getParams, $this->build['params']);

                array_walk($this->build['params'], function(&$item){
                    
                    $item = array_unique($item);
                });
            }
            return $this->build();
        }

        public function build()
        {
            $isParams = count($this->build['params']);

            if($isParams)
            {
                $joinParams = array_map(function(&$item){
                
                    return implode($this->splitChar, $item);
    
                }, $this->build['params']);
            }

            $this->build['path']      = isset($this->build['path']) ? implode(array("/", $this->build['path'])) : $this->path;
            $this->build['seperator'] = ($isParams ? "?" : "");

            $buildUrl = urldecode(implode(array(

                $this->scheme,
                '://',
                $this->host,
                $this->build['path'],
                $this->build['seperator'],
                ($isParams ? http_build_query($joinParams) : NULL),
                $this->build['fragment']
            )));

            $this->initBuild();

            return $buildUrl;
        }

        public function isParam($param)
        {
            return isset($this->params[$param]);
        }

        public function searchValue($search, $param = NULL)
        {
            if(!isset($search) || !isset($this->params)) return false;

            if(!empty($param))
            {
                if(isset($this->params[$param]))
                    return in_array($search, $this->params[$param]);
            
            } else{

                foreach($this->params as $item) if(in_array($search, $item)) return true;
            }
        }

        public function current()
        {
            return $this->current;
        }
    }