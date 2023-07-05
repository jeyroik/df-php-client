<?php
namespace deflou\components\triggers;

use deflou\components\triggers\operations\plugins\templates\TemplateContext;
use deflou\interfaces\triggers\ITemplateHtml;
use extas\interfaces\parameters\IParam;

class TemplateHtml extends TemplateContext implements ITemplateHtml
{
    public function __construct(array $config = [])
    {
        if (!isset($config[static::FIELD__PARAMS])) {
            $config[static::FIELD__PARAMS] = [];
        }

        if (isset($config[static::FIELD__RENDER])) {
            $config[static::FIELD__PARAMS][static::FIELD__RENDER] = [
                IParam::FIELD__NAME => static::FIELD__RENDER,
                IParam::FIELD__TITLE => 'Render',
                IParam::FIELD__VALUE => $config[static::FIELD__RENDER]
            ];
        }

        $config[static::FIELD__NAME] = static::NAME;

        parent::__construct($config);
    }
}
