<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Pullリソース
 *
 * <pre>
 *
 * Example
 * </pre>
 * <code>
 * {resource uri='Entry' params=$entryParams tepmplate="resource/entry"}
 * </code>
 *
 * @param Smarty &$smarty
 *
 * @return mixed $resource
 */
function smarty_function_resource(
    $params,
    /* @noinspection PhpUnusedParameterInspection */
    &$smarty
) {
    $config = (array) BEAR::loadValues($params['params']) + [
        'method' => 'read',
        'uri' => $params['uri'],
        'values' => [],
        'options' => []
    ];
    if (isset($params['template'])) {
        $config['options']['template'] = $params['template'];
    }
    if (isset($params['cache_life'])) {
        $config['options']['cache']['life'] = $params['cache_life'];
    }
    if (isset($params['cache_key'])) {
        $config['options']['cache']['key'] = $params['cache_key'];
    }
    $app = BEAR::get('app');
    $string = BEAR::factory(
        'BEAR_Ro_Prototype',
        [
            'request' => $config,
            'path' => $app['BEAR_View']['path']
        ]
    )->request()->toString();

    return $string;
}
