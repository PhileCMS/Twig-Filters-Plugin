<?php
/**
 * @author PhileCMS
 * @link https://github.com/PhileCMS/phileTwigFilters
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TwigFilters
 */

use Phile\Repository\PageCollection;

$config = [];

/**
 * Excerpt: grab the first paragraph and remove all the html code
 */
$config['excerpt']['_callback'] = function (\Twig_Environment $environment, array $config) {
    $filter = new \Twig_SimpleFilter('excerpt', function ($string) {
        return strip_tags(substr($string, 0, strpos($string, '</p>') + 4));
    });
    $environment->addFilter($filter);
};

/**
 * limit words function -- very rough limit due to HTML input
 *
 * If you want to remove the HTML markup when using the limit_words
 * filter, you can use the striptags Twig filter:
 *
 * {{ page.content|striptags|limit_words }}
 */
$config['limit_words']['limit'] = 58;
$config['limit_words']['_callback'] = function (\Twig_Environment $environment, array $config) {
    $filter = new \Twig_SimpleFilter('limit_words', function ($string) use ($config) {
        $limit = $config['limit'];
        $string = trim($string);
        $words = str_word_count($string, 2);
        $nbwords = count($words);
        if ($limit < $nbwords) {
            $pos = array_keys($words);
            $string = substr($string, 0, $pos[$limit]) . '…';
        }
        return $string;
    });
    $environment->addFilter($filter);
};

/**
 * Slugify filter
 *
 * {{ current_page.title | slugify }}
 */
$config['slugify']['delimiter'] = '–';
$config['slugify']['_callback'] = function (\Twig_Environment $environment, array $config) {
    $filter = new \Twig_Filter('slugify', function ($string) use ($config) {
        // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
        if (!extension_loaded('iconv')) {
            throw new PluginException('iconv module not loaded', 0);
        }
        // Save the old locale and set the new locale to UTF-8
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace('/[\/_|+ -]+/', $config['delimiter'], $clean);
        $clean = trim($clean, $config['delimiter']);
        // Revert back to the old locale
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    });
    $environment->addFilter($filter);
};

/**
 * Shuffle pages
 */
$config['shuffle']['_callback'] = function (\Twig_Environment $environment, array $config) {
    $filter = new \Twig_SimpleFilter('shuffle', function ($array) use ($config) {
        if ($array instanceof PageCollection) {
            $array = $array->toArray();
        }
        $keys = array_keys($array);
        shuffle($keys);
        $shuffled = array_combine($keys, $array);
        ksort($shuffled);
        return $shuffled;
    });
    $environment->addFilter($filter);
};

return $config;
