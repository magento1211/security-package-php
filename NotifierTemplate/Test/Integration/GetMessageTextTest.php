<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierTemplate\Model\GetMessageText;
use PHPUnit\Framework\TestCase;

class GetMessageTextTest extends TestCase
{
    /**
     * @var GetMessageText
     */
    private $getMessageText;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->getMessageText = Bootstrap::getObjectManager()->get(GetMessageText::class);
    }

    /**
     * Test template generation
     */
    public function testShouldGetTemplateText(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(
            'TEST',
            $this->getMessageText->execute('', '_default', [
                '_title' => 'TEST',
            ])
        );
    }
}
