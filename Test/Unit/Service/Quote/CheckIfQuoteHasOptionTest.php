<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Service\Quote;

use TIG\PostNL\Service\Quote\CheckIfQuoteHasOption;
use Magento\Checkout\Model\Session as CheckoutSession;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Test\TestCase;

class CheckIfQuoteHasOptionTest extends TestCase
{
    public $instanceClass = CheckIfQuoteHasOption::class;

    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            'Extra@Home with quote' => [ProductInfo::OPTION_EXTRAATHOME, true, true],
            'Extra@Home no quote'   => [ProductInfo::OPTION_EXTRAATHOME, false, false]
        ];
    }

    /**
     * @param $productCode
     * @param $hasQuote
     * @param $expected
     *
     * @dataProvider getDataProvider
     */
    public function testGet($productCode, $hasQuote, $expected)
    {
        $quoteMock = false;
        if ($hasQuote) {
            $quoteMock = $this->getMock('\Magento\Quote\Model\Quote', [], [], '', false);
        }

        $checkoutSession = $this->getFakeMock(CheckoutSession::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quoteMock);

        $itemsToOptions = $this->getFakeMock(ItemsToOption::class)->getMock();
        if ($quoteMock) {
            $quoteItemMock = $this->getMock('\Magento\Quote\Model\Quote\Item', [], [], '', false);
            $quoteMockExcpects = $quoteMock->method('getAllItems');
            $quoteMockExcpects->willReturn($quoteItemMock);

            $itemsToOptionsExpects = $itemsToOptions->method('get');
            $itemsToOptionsExpects->with($quoteItemMock);
            $itemsToOptionsExpects->willReturn($productCode);
        }

        $instance = $this->getInstance([
            'checkoutSession' => $checkoutSession,
            'itemsToOption' => $itemsToOptions
        ]);

        $result = $this->invokeArgs('get', [$productCode], $instance);

        $this->assertEquals($expected, $result);
    }

}
