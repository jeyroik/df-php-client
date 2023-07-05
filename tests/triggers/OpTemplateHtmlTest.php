<?php

use deflou\components\applications\AppWriter;
use deflou\components\instances\InstanceService;
use deflou\components\plugins\triggers\PluginTemplateHtmlEvent;
use deflou\components\triggers\ETrigger;
use deflou\components\triggers\operations\TriggerOperationService;
use deflou\components\triggers\TemplateHtml;
use deflou\components\triggers\TriggerService;
use deflou\interfaces\stages\triggers\IStageTriggerOpTemplate;
use deflou\interfaces\triggers\events\ITriggerEvent;
use deflou\interfaces\triggers\ITemplateHtml;
use extas\components\plugins\Plugin;
use extas\interfaces\parameters\IParam;
use tests\ExtasTestCase;
use tests\resources\TestRender;

class OpTemplateHtmlTest extends ExtasTestCase
{
    protected array $libsToInstall = [
        'jeyroik/df-triggers' => ['php', 'php'],
        'jeyroik/df-applications' => ['php', 'json']
        //'vendor/lib' => ['php', 'json'] storage ext, extas ext
    ];
    protected bool $isNeedInstallLibsItems = true;
    protected string $testPath = __DIR__;

    public function testEventTemplate()
    {
        $appService = new AppWriter();
        $app = $appService->createAppByConfigPath(__DIR__ . '/../resources/app.json', true);
        
        $iService = new InstanceService();
        $instance = $iService->createInstanceFromApplication($app, 'test-vendor');

        $tService = new TriggerService();
        $trigger = $tService->createTriggerForInstance($instance, 'test-vendor');
        $trigger->setInstanceId(ETrigger::Operation, $instance->getId());
        $trigger->setApplicationId(ETrigger::Operation, $app->getId());
        $tService->triggers()->update($trigger);
        $trigger = $tService->insertEvent($trigger->getId(), [
            ITriggerEvent::FIELD__NAME => 'test_event'
        ]);

        $opService = new TriggerOperationService();
        $opService->plugins()->create(new Plugin([
            Plugin::FIELD__CLASS => PluginTemplateHtmlEvent::class,
            Plugin::FIELD__STAGE => IStageTriggerOpTemplate::NAME . ITemplateHtml::NAME . '.event',
            Plugin::FIELD__PARAMETERS => [
                PluginTemplateHtmlEvent::PARAM__VIEW_HEADER => [
                    IParam::FIELD__NAME => PluginTemplateHtmlEvent::PARAM__VIEW_HEADER,
                    IParam::FIELD__VALUE => __DIR__ . '/../resources/header.php'
                ],
                PluginTemplateHtmlEvent::PARAM__VIEW_ITEM => [
                    IParam::FIELD__NAME => PluginTemplateHtmlEvent::PARAM__VIEW_ITEM,
                    IParam::FIELD__VALUE => __DIR__ . '/../resources/item.php'
                ],
                PluginTemplateHtmlEvent::PARAM__VIEW_ITEMS => [
                    IParam::FIELD__NAME => PluginTemplateHtmlEvent::PARAM__VIEW_ITEMS,
                    IParam::FIELD__VALUE => __DIR__ . '/../resources/items.php'
                ]
            ]
        ]));

        $result = $opService->getPluginsTemplates($instance, $trigger, new TemplateHtml([
            TemplateHtml::FIELD__RENDER => new TestRender()
        ]));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('event', $result);

        $eventResult = $result['event'];
        $this->assertIsArray($eventResult);
        
        $this->assertArrayHasKey(ITemplateHtml::RESULT__HEADER, $eventResult, 'Missed header in a result');
        $this->assertEquals(file_get_contents(__DIR__ . '/../resources/rendered.header.html'), $eventResult['header']);

        $this->assertArrayHasKey(ITemplateHtml::RESULT__ITEMS, $eventResult, 'Missed items in a result');
        $this->assertEquals(file_get_contents(__DIR__ . '/../resources/rendered.items.html'), $eventResult['items']);
    }
}
