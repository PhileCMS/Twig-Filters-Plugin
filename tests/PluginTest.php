<?php
/**
 * @author PhileCMS
 * @link https://github.com/PhileCMS/phileTwigFilters
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TwigFilters
 */

namespace Phile\Plugin\Phile\TwigFilters\Tests;

use Phile\Core\Config;
use Phile\Test\TestCase;
use Phile\Repository\Page;

class PluginTest extends TestCase
{
    public function testExcerpt()
    {
        $test = function (\Twig_Environment $twig) {
            $template = $twig->createTemplate('{{ content|excerpt }}');
            $content = '<p>foo</p><p>bar</p>';
            $actual = $template->render(array('content' => $content));
            $this->assertSame('foo', $actual);
        };

        $this->runTwigFiltersTest($test);
    }

    public function testLimitWords()
    {
        $config = ['limit_words' => ['limit' => 3]];

        $test = function (\Twig_Environment $twig) {
            $template = $twig->createTemplate('{{ content|limit_words }}');
            $content = 'foo bar baz zap';
            $actual = $template->render(array('content' => $content));
            $this->assertSame('foo bar baz …', $actual);
        };

        $this->runTwigFiltersTest($test, $config);
    }

    public function testSlugify()
    {
        $test = function (\Twig_Environment $twig) {
            $template = $twig->createTemplate('{{ content|slugify }}');
            $content = 'Täst   \@::)) Bar  ';
            $actual = $template->render(array('content' => $content));
            $this->assertSame('tast–bar', $actual);
        };

        $this->runTwigFiltersTest($test);
    }

    public function testShuffleArray()
    {
        $test = function (\Twig_Environment $twig) {
            $template = $twig->createTemplate('{{ content|shuffle|join }}');
            $content = str_split('0123456789');
            $actual = $template->render(array('content' => $content));
            $this->assertNotSame('0123456789', $actual);
            $this->assertRegExp('/[\d]{10}/', $actual);
        };

        $this->runTwigFiltersTest($test);
    }

    public function testShufflePages()
    {
        $test = function (\Twig_Environment $twig) {
            $template = $twig->createTemplate('{{ pages|shuffle|first.title }}');
            $pages = (new Page())->findAll();
            $actual = $template->render(array('pages' => $pages));
            // just check that nothing throws an error, should be Page then
            $this->assertNotEmpty($actual);
        };

        $this->runTwigFiltersTest($test);
    }

    private function runTwigFiltersTest(callable $callback, ?array $config = [])
    {
        $config = new Config([
            'plugins' => [
                'phile\\twigFilters' => $config + ['active' => true]
            ]
        ]);

        $request = $this->createServerRequestFromArray();
        $core = $this->createPhileCore(null, $config);

        $eventTriggered = false;
        $core->addBootstrap(function ($eventBus, $config) use (&$eventTriggered, $callback) {
            $eventBus->register(
                'template_engine_registered',
                function ($name, $data) use (&$eventTriggered, $callback) {
                    $eventTriggered = true;
                    $callback($data['engine']);
                }
            );
        });

        $response = $this->createPhileResponse($core, $request);
        $this->assertTrue($eventTriggered);
    }
}
