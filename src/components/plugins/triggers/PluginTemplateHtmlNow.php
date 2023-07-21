<?php
namespace deflou\components\plugins\triggers;

use deflou\components\triggers\values\plugins\PluginNow;
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
    public const STAGE = self::STAGE__PREFIX . PluginNow::NAME;
    
    protected function renderEachItem($templateData, $contextParam, $render, $data): array
    {
        $items = [];

        foreach ($templateData as $format) {
            $curData = [
                ITemplateHtml::FIELD__PARAM => $contextParam,
                IParam::FIELD__NAME => $format,
                IParam::FIELD__TITLE => $format,
                IParam::FIELD__DESCRIPTION => 'Пример: ' . date($format)
            ];
            $this->applyItemData($data, $curData);
            $items[] = $render->render($this->itemViewPath, $curData);
        }

        return $items;
    }
}
