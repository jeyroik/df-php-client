<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\triggers\ITemplateHtml;
use extas\interfaces\parameters\IParam;

/**
 * In a plugin conf:
 * {
 *  "class": "gosp\\webhooks\\components\\plugins\\triggers\\PluginTemplateHtmlNow",
 *  "stage": "deflou.trigger.op.template.html.now",
 *  "params": {
 *      "header": { // PluginTemplateHtmlEvent::PARAM__VIEW_HEADER
 *          "name": "header",
 *          "value": "/path/to/header/view"
 *      },
 *      "item": {// PluginTemplateHtmlEvent::PARAM__VIEW_ITEM
 *          "name": "item",
 *          "value": "/path/to/item/view"
 *      },
 *      "items": {// PluginTemplateHtmlEvent::PARAM__VIEW_ITEMS
 *          "name": "items",
 *          "value": "/path/to/items/view"
 *      }
 *  }
 * }
 * 
 * In a context:
 * {
 *  "name": "html", // deflou\interfaces\triggers\ITemplateHtml::NAME
 *  "params": {
 *      "render": {// deflou\interfaces\triggers\ITemplateHtml::FIELD__RENDER
 *          "name": "render",
 *          "value": <render>
 *      },
 *      "param": {// current operation param object
 *          "name": "param",
 *          "value": <param>
 *      }
 *  }
 * }
 */
class PluginTemplateHtmlNow extends PluginTemplateHtml 
{
    public const STAGE = self::STAGE__PREFIX . 'now';
    protected function renderEachItem($templateData, $contextParam, $render): array
    {
        $items = [];
        $itemViewPath = $this->getParameter(static::PARAM__VIEW_ITEM)->getValue();

        foreach ($templateData as $format) {
            $data = [
                ITemplateHtml::FIELD__PARAM => $contextParam,
                IParam::FIELD__NAME => $format,
                IParam::FIELD__TITLE => $format,
                IParam::FIELD__DESCRIPTION => 'Пример: ' . date($format)
            ];
            $items[] = $render->render($itemViewPath, $data);
        }

        return $items;
    }
}
