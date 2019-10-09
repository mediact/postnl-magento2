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
namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use TIG\PostNL\Config\CheckoutConfiguration\IsShippingOptionsActive;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Quote\CheckIfQuoteHasOption;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Test\TestCase;

class IsShippingOptionsActiveTest extends TestCase
{
    public $instanceClass = IsShippingOptionsActive::class;

    /**
     * @var ShippingOptions|MockObject
     */
    private $shippingOptions;

    /**
     * @var AccountConfiguration|MockObject
     */
    private $accountConfiguration;

    public function setUp()
    {
        parent::setUp();

        $this->shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $this->accountConfiguration = $this->getFakeMock(AccountConfiguration::class)->getMock();
    }

    public function getValueProvider()
    {
        return [
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 0 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => false,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 0 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => false,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => true
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => true
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => true
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 0 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'backordered',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 0 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => false,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => false,
                 'expected'              => true
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 0 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => false,
                 'canBackorder'          => true,
                 'expected'              => true
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 0 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => false,
                 'expected'              => false
                ],
            'shippingOptionsActive = 1 | hasValidApiSettings = 1 | stockOptions = 1 | productsInStock = 1 | productsExtraAtHome = 1 | canBackorder = 1 | ' =>
                ['shippingOptionsActive' => true,
                 'hasValidApiSettings'   => true,
                 'stockOptions'          => 'in_stock',
                 'productsInStock'       => true,
                 'productsExtraAtHome'   => true,
                 'canBackorder'          => true,
                 'expected'              => false
                ]
        ];
    }

    /**
     * @param $shippingOptionsActive
     * @param $hasValidApiSettings
     * @param $stockOptions
     * @param $productsInStock
     * @param $isExtraAtHome
     * @param $expected
     * @param $canBackorder
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue(
        $shippingOptionsActive,
        $hasValidApiSettings,
        $stockOptions,
        $productsInStock,
        $isExtraAtHome,
        $canBackorder,
        $expected
    ) {
        $quoteItemsAreInStock = $this
            ->getFakeMock(\TIG\PostNL\Service\Quote\CheckIfQuoteItemsAreInStock::class)
            ->getMock();

        $getValueExpects = $quoteItemsAreInStock->method('getValue');
        $getValueExpects->willReturn($productsInStock);

        $quoteItemCanBackorder = $this
            ->getFakeMock(\TIG\PostNL\Service\Quote\CheckIfQuoteItemsCanBackorder::class)
            ->getMock();

        $getBackorderValueExpects = $quoteItemCanBackorder->method('getValue');
        $getBackorderValueExpects->willReturn($canBackorder);

        $quoteHasOption = $this->getFakeMock(CheckIfQuoteHasOption::class)->getMock();

        $extraAtHomeGetValueExpects = $quoteHasOption->method('get');
        $extraAtHomeGetValueExpects->with(ProductInfo::OPTION_EXTRAATHOME);
        $extraAtHomeGetValueExpects->willReturn($isExtraAtHome);

        /** @var IsShippingOptionsActive $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $this->shippingOptions,
            'accountConfiguration' => $this->accountConfiguration,
            'quoteItemsAreInStock' => $quoteItemsAreInStock,
            'quoteHasOption' => $quoteHasOption,
            'quoteItemsCanBackorder' => $quoteItemCanBackorder
        ]);

        $this->mockShippingOptionsMethod('isShippingoptionsActive', $shippingOptionsActive);
        $this->mockAccountConfigurationMethod('getCustomerCode', $hasValidApiSettings);
        $this->mockAccountConfigurationMethod('getCustomerNumber', $hasValidApiSettings);
        $this->mockAccountConfigurationMethod('getApiKey', $hasValidApiSettings);
        $this->getShippingStockoptions($stockOptions);
        $this->assertEquals($expected, $instance->getValue());
    }

    public function hasEnteredApiDataProvider()
    {
        return [
            'without customer code customer number and api key' => [
                'customerCode' => null,
                'customerNumber' => null,
                'apiKey' => null,
                'expected' => false,
            ],
            'with customer code, without customer number and api key' => [
                'customerCode' => '12345',
                'customerNumber' => null,
                'apiKey' => null,
                'expected' => false,
            ],
            'with customer code and customer number, without api key' => [
                'customerCode' => '12345',
                'customerNumber' => '12345',
                'apiKey' => null,
                'expected' => false,
            ],
            'with customer code, customer number and api key' => [
                'customerCode' => '12345',
                'customerNumber' => '12345',
                'apiKey' => '12345',
                'expected' => true,
            ],
        ];
    }

    /**
     * @param $customerCode
     * @param $customerNumber
     * @param $apiKey
     * @param $expected
     *
     * @dataProvider hasEnteredApiDataProvider
     */
    public function testHasEnteredApiDataProvider($customerCode, $customerNumber, $apiKey, $expected)
    {
        /** @var IsShippingOptionsActive $instance */
        $instance = $this->getInstance([
            'accountConfiguration' => $this->accountConfiguration,
        ]);

        $this->mockAccountConfigurationMethod('getCustomerCode', $customerCode);
        $this->mockAccountConfigurationMethod('getCustomerNumber', $customerNumber);
        $this->mockAccountConfigurationMethod('getApiKey', $apiKey);

        $result = $this->invoke('hasValidApiSettings', $instance);

        $this->assertSame($expected, $result);
    }

    /**
     * @param $value
     */
    private function mockShippingOptionsMethod($method, $value)
    {
        $expects = $this->shippingOptions->method($method);
        $expects->willReturn($value);
    }

    /**
     * @param $value
     */
    private function mockAccountConfigurationMethod($method, $value)
    {
        $expects = $this->accountConfiguration->method($method);
        $expects->willReturn($value);
    }

    /**
     * @param $value
     */
    private function getShippingStockoptions($value)
    {
        $expects = $this->shippingOptions->method('getShippingStockoptions');
        $expects->willReturn($value);
    }
}
