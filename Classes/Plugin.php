<?php
/**
 * @author PhileCMS
 * @link https://github.com/PhileCMS/phileTwigFilters
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TwigFilters
 */

namespace Phile\Plugin\Phile\TwigFilters;

use Phile\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    protected $events = ['template_engine_registered' => 'templateEngineRegistered'];

    public function templateEngineRegistered($eventData)
    {
        foreach ($this->settings as $function) {
            if (empty($function['_callback'])) {
                continue;
            }
            ($function['_callback'])($eventData['engine'], $function);
        }
    }
}
