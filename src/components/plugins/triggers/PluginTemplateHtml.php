<?php
namespace deflou\components\plugins\triggers;

use deflou\interfaces\stages\triggers\IStageTriggerOpTemplate;
use deflou\interfaces\triggers\ITemplateHtml;
use deflou\interfaces\triggers\operations\ITriggerOperationPlugin;
use deflou\interfaces\triggers\operations\plugins\templates\ITemplateContext;
use extas\components\parameters\Param;
use extas\components\plugins\Plugin;
use extas\components\Replace;
use extas\components\systems\System;

/**
 * In a plugin conf:
 * {
 *  "stage": "deflou.trigger.op.template.html.<op.plugin.name>",
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
abstract class PluginTemplateHtml extends Plugin implements IStageTriggerOpTemplate
{
    public const PARAM__VIEW_HEADER = 'header';
    public const PARAM__VIEW_ITEM = 'item';
    public const PARAM__VIEW_ITEMS = 'items';
    public const PARAM__DESCRIPTION = 'desc';
    public const PARAM__ACTIVE = 'active';

    public const ACTIVE__YES = 'active';
    public const ACTIVE__NO = '';

    public const VIEW__DEFAULT = '@default';

    public const SYS_OPTION__HEADER = 'trigger.operation.view.header';
    public const SYS_OPTION__ITEM_BADGE = self::VIEW__DEFAULT . '.item.badge';
    public const SYS_OPTION__ITEM_LIST = self::VIEW__DEFAULT . '.item.list';
    public const SYS_OPTION__ITEMS_BADGE = self::VIEW__DEFAULT . '.items.badge';
    public const SYS_OPTION__ITEMS_LIST = self::VIEW__DEFAULT . '.items.list';
    public const STAGE__PREFIX = IStageTriggerOpTemplate::NAME . 'html.';

    protected string $itemViewPath = '';
    public function __invoke(array $templateData, ITriggerOperationPlugin $plugin, mixed &$template, ITemplateContext $context): void
    {
        $render = $context->buildParams()->buildOne(ITemplateHtml::FIELD__RENDER)->getValue();
        $result = [
            ITemplateHtml::RESULT__HEADER => '',
            ITemplateHtml::RESULT__ITEMS => ''
        ];

        $contextParams = $context->buildParams();
        $contextParam = $contextParams->hasOne(ITemplateHtml::FIELD__PARAM) 
                            ? $contextParams->buildOne(ITemplateHtml::FIELD__PARAM)->getValue() 
                            : false;

        $result[ITemplateHtml::RESULT__HEADER] = $this->prepareHeader($plugin, $render, $contextParam);

        $items = $this->prepareEachItem($plugin, $templateData, $contextParam, $render);
        $itemsViewPath = $this->getParameter(static::PARAM__VIEW_ITEMS)->getValue();
        $itemsData = [
            'items' => implode('', $items),
            'param' => $contextParam,
            'plugin' => $plugin
        ];

        if (str_contains($itemsViewPath, static::VIEW__DEFAULT)) {
            $system = new System();
            if ($system->hasOption($itemsViewPath)) {
                $itemsViewPath = $system->getOptionValue($itemsViewPath);
                $itemsData['plugin'] = $this->getParameter($plugin->getName());
                $itemsData['active'] = $this->hasParameter(static::PARAM__ACTIVE) 
                                    ? $this->getParameter(static::PARAM__ACTIVE)->getValue() 
                                    : static::ACTIVE__NO;
            }
        }

        $result[ITemplateHtml::RESULT__ITEMS] = $render->render($itemsViewPath, $itemsData);

        $template = $result;
    }

    protected function prepareHeader(ITriggerOperationPlugin $plugin, $render, $contextParam): string
    {
        $header = [
            'name' => $plugin->getName(),
            'title' => $plugin->getTitle(),
            'description' => $plugin->getDescription(),
            'param' => $contextParam,
            'active' => $this->hasParameter(static::PARAM__ACTIVE) 
                                    ? $this->getParameter(static::PARAM__ACTIVE)->getValue() 
                                    : static::ACTIVE__NO
        ];

        $headerViewPath = $this->getParameter(static::PARAM__VIEW_HEADER)->getValue();

        if ($headerViewPath == static::VIEW__DEFAULT) {
            $system = new System();
            if (!$system->hasOption(static::SYS_OPTION__HEADER)) {
                return '';
            }
            $headerViewPath = $system->getOptionValue(static::SYS_OPTION__HEADER);
        }

        return $render->render($headerViewPath, $header);
    }

    protected function prepareEachItem(ITriggerOperationPlugin $plugin, $templateData, $contextParam, $render): array
    {
        $this->itemViewPath = $this->getParameter(static::PARAM__VIEW_ITEM)->getValue();
        $data = [];

        if (str_contains($this->itemViewPath, static::VIEW__DEFAULT)) {
            $system = new System();
            if ($system->hasOption($this->itemViewPath)) {
                $this->itemViewPath = $system->getOptionValue($this->itemViewPath);
                $data['plugin'] = $this->getParameter($plugin->getName());
            }
        }

        $items = $this->renderEachItem($templateData, $contextParam, $render, $data);

        return $items;
    }

    protected function applyItemData(array &$data, array $item): void
    {
        if (isset($data['plugin'])) {
            $data['plugin'] = $data['plugin']->setValue(
                Replace::please()->apply(['item' => $item])->to($data['plugin']->getValue())
            );
        }
    }

    abstract protected function renderEachItem($templateData, $contextParam, $render, $data): array;
}
