# Twig Functions

[![Build Status](https://travis-ci.org/PhileCMS/phileTwigFilters.svg?branch=master)](https://travis-ci.org/PhileCMS/phileTwigFilters)

Adds helpfull Twig functions to [Phile](https://github.com/PhileCMS/Phile) and easily allows you to create new ones. [Project home](https://github.com/PhileCMS/phileTwigFilters).

## Installation

```bash
composer require phile/twig-functions
```

## Activation

```php
$config['plugins']['phile\\twigFunctions'] = [
    'active' => true
];
```

## Usage

This plugin includes some predefined Twig-filter and allows you to easily add your own.

### Define a New Custom Filter

See the existing filters in config.php for how to add your own filter.

### excerpt

Grabs the first paragraph of the content string.

```twig
{{ content|excerpt }}
```

### limit_words

Similar to `excert` but limits on number of words. Use Twig's `striptags` to remove HTML-tags.

```twig
{{ page.content|striptags|limit_words }}
```

### shuffle

Shuffles an array. For example show a shuffled lists of pages:

```twig
<ul class="posts">
  {% for page in pages|shuffle %}
    <li><a href="{{ page.url }}">{{ page.title }}</a></li>
  {% endfor %}
</ul>
```

### slugify

This new Twig filter allows you to slugify a string. This is useful for making safe URLs, HTML-safe class/id names, or just cleaning up general strings.

```twig
<!-- becomes "this–is–an–strange–string" -->
{{ "This Is ____an STRÄNGE      string" | slugify }}
```