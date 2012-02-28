<?php

/**
* Smarty method Clear_Cache
* 
* Empties the cache for a specific template
* 
* @package Smarty
* @subpackage SmartyMethod
* @author Uwe Tews 
*/

/**
* Empty cache for a specific template
* 
* @param object $smarty 
* @param string $template_name template name
* @param string $cache_id cache id
* @param string $compile_id compile id
* @param integer $exp_time expiration time
* @param string $type resource type
* @return integer number of cache files deleted
*/
function clear_cache($smarty, $template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = 'file')
{ 
    // load cache resource
    if (!isset($smarty->cache_resource_objects[$type])) {
        $_cache_resource_class = 'Smarty_Internal_CacheResource_' . $type;
        if (!$smarty->loadPlugin($_cache_resource_class)) {
            throw new Exception("Undefined cache resource type {$type}");
        } 
        $smarty->cache_resource_objects[$type] = new $_cache_resource_class($smarty);
    } 

    return $smarty->cache_resource_objects[$type]->clear($template_name, $cache_id, $compile_id, $exp_time);
} 

?>
