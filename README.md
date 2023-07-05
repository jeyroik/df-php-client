![PHP Composer](https://github.com/jeyroik/df-php-client/workflows/PHP%20Composer/badge.svg?branch=master)
![codecov.io](https://codecov.io/gh/jeyroik/df-php-client/coverage.svg?branch=master)

[![Latest Stable Version](https://poser.pugx.org/jeyroik/df-php-client/v)](//packagist.org/packages/jeyroik/df-php-client)
[![Total Downloads](https://poser.pugx.org/jeyroik/df-php-client/downloads)](//packagist.org/packages/jeyroik/df-php-client)
[![Dependents](https://poser.pugx.org/jeyroik/df-php-client/dependents)](//packagist.org/packages/jeyroik/df-php-client)


# df-php-client

DeFlou PHP client helpers

# usage

in `extas.json`

```json
{
    "plugins": [
        {
            "class": "deflou\\components\\plugins\\triggers\\PluginTemplateHtmlEvent",
            "stage": "deflou.trigger.op.template.html.event",
            "parameters": {
                "header": {
                    "name": "header",
                    "value": "path/to/view"
                },
                "item": {
                    "name": "item",
                    "value": "path/to/view"
                },
                "items": {
                    "name": "items",
                    "value": "path/to/view"
                }
            }
        }
    ]
}
```