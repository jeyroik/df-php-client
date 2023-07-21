<?php
namespace deflou\components\plugins\triggers;

use deflou\components\triggers\values\plugins\PluginEvent;
use deflou\interfaces\triggers\ITemplateHtml;

/**
 * In a plugin conf:
 * {
 *  "class": "gosp\\webhooks\\components\\plugins\\triggers\\PluginTemplateHtmlEvent",
 *  "stage": "deflou.trigger.op.template.html.event",
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
class PluginTemplateHtmlEvent extends PluginTemplateHtml
{
    public const STAGE = self::STAGE__PREFIX . PluginEvent::NAME;

    protected function renderEachItem($templateData, $contextParam, $render, $data): array
    {
        $items = [];
        
        foreach ($templateData as $param) {
            $curData = $param->__toArray();
            $this->applyItemData($data, $curData);
            $curData[ITemplateHtml::FIELD__PARAM] = $contextParam;
            $items[] = $render->render($this->itemViewPath, $curData);
        }

        return $items;
    }
}
