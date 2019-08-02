<?php
namespace common\components;

use Yii;
use yii\helpers\Url;
use yii\web\UrlManager;

class MyUrlManager extends UrlManager
{
    private $_ruleCache;
    
    /**
     * Creates a URL using the given route and query parameters.
     *
     * You may specify the route as a string, e.g., `site/index`. You may also use an array
     * if you want to specify additional query parameters for the URL being created. The
     * array format must be:
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1&param2=value2
     * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
     * ```
     *
     * If you want to create a URL with an anchor, you can use the array format with a `#` parameter.
     * For example,
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1#name
     * ['site/index', 'param1' => 'value1', '#' => 'name']
     * ```
     *
     * The URL created is a relative one. Use [[createAbsoluteUrl()]] to create an absolute URL.
     *
     * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
     * as an absolute route.
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @return string the created URL
     */
    public function createUrl($params)
    {
        //Yii::info('Creating relative URL');
        $params['domain'] = Yii::$app->params['domain'];
        
        $params = (array) $params;
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#'], $params[$this->routeParam]);
        
        $route = trim($params[0], '/');
        unset($params[0]);
        
        $baseUrl = $this->showScriptName || !$this->enablePrettyUrl ? $this->getScriptUrl() : $this->getBaseUrl();
        
        if ($this->enablePrettyUrl) {
            $cacheKey = $route . '?';
            foreach ($params as $key => $value) {
                if ($value !== null) {
                    $cacheKey .= $key . '&';
                }
            }
            
            $url = $this->getUrlFromCache($cacheKey, $route, $params);
            
            if ($url === false) {
                /* @var $rule UrlRule */
                foreach ($this->rules as $rule) {
                    if (in_array($rule, $this->_ruleCache[$cacheKey], true)) {
                        // avoid redundant calls of `UrlRule::createUrl()` for rules checked in `getUrlFromCache()`
                        // @see https://github.com/yiisoft/yii2/issues/14094
                        continue;
                    }
                    $url = $rule->createUrl($this, $route, $params);
                    if ($this->canBeCached($rule)) {
                        $this->setRuleToCache($cacheKey, $rule);
                    }
                    if ($url !== false) {
                        break;
                    }
                }
            }
            
            if ($url !== false) {
                if (strpos($url, '://') !== false) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 8)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }
                    
                    return $url . $baseUrl . $anchor;
                } elseif (strncmp($url, '//', 2) === 0) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 2)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }
                    
                    if (strpos($url, $params['domain'] . '/') && isset(Yii::$app->params['port'])) {
                        $url = str_replace(Yii::$app->params['domain'], Yii::$app->params['domain'] . ':' . Yii::$app->params['port'], $url);
                    }
                    
                    return $url . $baseUrl . $anchor;
                }
                
                $url = ltrim($url, '/');
                return "$baseUrl/{$url}{$anchor}";
            }
            
            if ($this->suffix !== null) {
                $route .= $this->suffix;
            }
            if (!empty($params) && ($query = http_build_query($params)) !== '') {
                $route .= '?' . $query;
            }
    
            $route = ltrim($route, '/');
            return "$baseUrl/{$route}{$anchor}";
        }
        
        $url = "$baseUrl?{$this->routeParam}=" . urlencode($route);
    
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '&' . $query;
        }
        
        
        
        return $url . $anchor;
    }
    
    public function createAbsoluteUrl($params, $scheme = null)
    {
        Yii::info('Testing absolute');
        $params = (array) $params;
        $url = $this->createUrl($params);
        
        if (strpos($url, '://') === false) {
            $hostInfo = $this->getHostInfo();
            if (strncmp($url, '//', 2) === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }
        
        return Url::ensureScheme($url, $scheme);
    }
    
    /**
     * Get URL from internal cache if exists.
     * @param string $cacheKey generated cache key to store data.
     * @param string $route the route (e.g. `site/index`).
     * @param array $params rule params.
     * @return bool|string the created URL
     * @see createUrl()
     * @since 2.0.8
     */
    protected function getUrlFromCache($cacheKey, $route, $params)
    {
        if (!empty($this->_ruleCache[$cacheKey])) {
            foreach ($this->_ruleCache[$cacheKey] as $rule) {
                /* @var $rule UrlRule */
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    return $url;
                }
            }
        } else {
            $this->_ruleCache[$cacheKey] = [];
        }
        
        return false;
    }
}
