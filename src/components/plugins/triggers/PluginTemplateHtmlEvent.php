<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\stages\triggers\IStageTriggerOpTemplate;
use deflou\interfaces\triggers\ITemplateHtml;
use deflou\interfaces\triggers\operations\ITriggerOperationPlugin;
use deflou\interfaces\triggers\operations\plugins\templates\ITemplateContext;
use extas\components\plugins\Plugin;

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
class PluginTemplateHtmlEvent extends Plugin implements IStageTriggerOpTemplate
{
    public const PARAM__VIEW_HEADER = 'header';
    public const PARAM__VIEW_ITEM = 'item';
    public const PARAM__VIEW_ITEMS = 'items';

    public const STAGE = IStageTriggerOpTemplate::NAME . 'html.event';

    public function __invoke(array $templateData, ITriggerOperationPlugin $plugin, mixed &$template, ITemplateContext $context): void
    {
        $render = $context->buildParams()->buildOne(ITemplateHtml::FIELD__RENDER)->getValue();
        $result = [
            ITemplateHtml::RESULT__HEADER => '',
            ITemplateHtml::RESULT__ITEMS => ''
        ];

        $header = [
            'name' => $plugin->getName(),
            'title' => $plugin->getTitle(),
            'description' => $plugin->getDescription()
        ];

        $headerViewPath = $this->getParameter(static::PARAM__VIEW_HEADER)->getValue();
        $result[ITemplateHtml::RESULT__HEADER] = $render->render($headerViewPath, $header);

        $itemViewPath = $this->getParameter(static::PARAM__VIEW_ITEM)->getValue();
        $items = [];
        $contextParams = $context->buildParams();
        $contextParam = $contextParams->hasOne(ITemplateHtml::FIELD__PARAM) 
                            ? $contextParams->buildOne(ITemplateHtml::FIELD__PARAM)->getValue() 
                            : false;

        foreach ($templateData as $param) {
            $data = $param->__toArray();
            $data[ITemplateHtml::FIELD__PARAM] = $contextParam;
            $items[] = $render->render($itemViewPath, $data);
        }

        $itemsViewPath = $this->getParameter(static::PARAM__VIEW_ITEMS)->getValue();
        $result[ITemplateHtml::RESULT__ITEMS] = $render->render($itemsViewPath, [
            'items' => implode('', $items),
            'plugin' => $plugin->getName(),
            'param' => $contextParam
        ]);

        $template = $result;
    }
}
