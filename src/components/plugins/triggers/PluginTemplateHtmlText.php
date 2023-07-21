<?php
namespace deflou\components\plugins\triggers;

use deflou\components\triggers\values\plugins\PluginText;
use deflou\interfaces\triggers\ITemplateHtml;
use extas\components\Replace;
use extas\interfaces\parameters\IParam;

/**
 * In a plugin conf:
 * {
 *  "class": "gosp\\webhooks\\components\\plugins\\triggers\\PluginTemplateHtmlText",
 *  "stage": "deflou.trigger.op.template.html.text",
 *  "params": {
 *      "header": { // PluginTemplateHtmlEvent::PARAM__VIEW_HEADER
 *          "name": "header",
 *          "value": "/path/to/header/view"
 *      },
 *      "items": {// PluginTemplateHtmlEvent::PARAM__VIEW_ITEMS
 *          "name": "items",
 *          "value": "/path/to/items/view"
 *      },
 *      "title": {
 *          "name": "title",
 *          "value": "any text with placeholder @param.title"
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
class PluginTemplateHtmlText extends PluginTemplateHtml 
{
    public const STAGE = self::STAGE__PREFIX . PluginText::NAME;
    public const PARAM__TITLE = 'title';
    public const CONTEXT_PARAM__MASK = 'param';
    
    protected function renderEachItem($templateData, $contextParam, $render, $data): array
    {
        $items = [];
        $titleText = $this->getParameter(static::PARAM__TITLE)->getValue();
        $text = Replace::please()->apply([static::CONTEXT_PARAM__MASK => $contextParam->__toArray()])->to($titleText);
        $curData = [
            ITemplateHtml::FIELD__PARAM => $contextParam,
            IParam::FIELD__NAME => '',
            IParam::FIELD__TITLE => $text,
            IParam::FIELD__DESCRIPTION => $text
        ];
        $this->applyItemData($data, $curData);

        $items[] = $render->render($this->itemViewPath, $curData);

        return $items;
    }
}
