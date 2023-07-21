<?php
namespace deflou\interfaces\triggers;

use deflou\interfaces\triggers\values\plugins\templates\ITemplateContext;

interface ITemplateHtml extends ITemplateContext
{
    public const NAME = 'html';
    public const FIELD__RENDER = 'render';
    public const FIELD__PARAM = 'param';

    public const RESULT__HEADER = 'header';
    public const RESULT__ITEMS = 'items';
}
