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

namespace TIG\PostNL\Service\Handler;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Model\ShipmentBarcodeFactory;
use TIG\PostNL\Webservices\Endpoints\Barcode as BarcodeEndpoint;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;
use \Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;

// @codingStandardsIgnoreFile
class BarcodeHandler
{
    /**
     * @var BarcodeEndpoint
     */
    private $barcodeEndpoint;

    /**
     * @var CollectionFactory
     */
    private $shipmentBarcodeCollectionFactory;

    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ProductOptionsConfiguration
     */
    private $productOptionsConfiguration;

    /**
     * @var string
     */
    private $countryId;

    /**
     * $var null|int
     */
    private $storeId;

    /**
     * @param BarcodeEndpoint             $barcodeEndpoint
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentBarcodeFactory      $shipmentBarcodeFactory
     * @param CollectionFactory           $shipmentBarcodeCollectionFactory
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     */
    public function __construct(
        BarcodeEndpoint $barcodeEndpoint,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        CollectionFactory $shipmentBarcodeCollectionFactory,
        ProductOptionsConfiguration $productOptionsConfiguration
    ) {
        $this->barcodeEndpoint = $barcodeEndpoint;
        $this->shipmentBarcodeCollectionFactory = $shipmentBarcodeCollectionFactory;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->productOptionsConfiguration = $productOptionsConfiguration;
    }

    /**
     * @param $magentoShipmentId
     * @param $countryId
     */
    public function prepareShipment($magentoShipmentId, $countryId)
    {
        $this->countryId = $countryId;
        $shipment = $this->shipmentRepository->getByShipmentId($magentoShipmentId);

        if (!$shipment || $shipment->getMainBarcode() !== null || $shipment->getConfirmedAt() !== null) {
            return;
        }

        $magentoShipment = $shipment->getShipment();
        $this->storeId = $magentoShipment->getStoreId();

        $mainBarcode = $this->generate($shipment);
        $shipment->setMainBarcode($mainBarcode);
        $this->shipmentRepository->save($shipment);

        if ($shipment->getParcelCount() > 1) {
            $this->addBarcodes($shipment, $mainBarcode);
        }
    }

    /**
     * Generate and save a new barcode for the just saved shipment
     *
     * @param ShipmentInterface $shipment
     * @param                   $mainBarcode
     *
     * @throws \Exception
     */
    public function addBarcodes(ShipmentInterface $shipment, $mainBarcode)
    {
        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentBarcode\Collection $barcodeModelCollection */
        $barcodeModelCollection = $this->shipmentBarcodeCollectionFactory->create();
        $barcodeModelCollection->load();

        /**
         * The first item is the main barcode
         */
        $barcodeModelCollection->addItem($this->createBarcode($shipment->getId(), 1, $mainBarcode));

        $parcelCount = $shipment->getParcelCount();
        for ($count = 2; $count <= $parcelCount; $count++) {
            $barcodeModelCollection->addItem(
                $this->createBarcode($shipment->getId(), $count, $this->generate($shipment))
            );
        }

        $barcodeModelCollection->save();
    }

    /**
     * CIF call to generate a new barcode
     *
     * @param ShipmentInterface $shipment
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generate(ShipmentInterface $shipment)
    {
        $magentoShipment = $shipment->getShipment();

        $this->barcodeEndpoint->setCountryId($this->countryId);
        $this->barcodeEndpoint->setStoreId($magentoShipment->getStoreId());
        $this->setTypeByProductCode($shipment->getProductCode());
        $response = $this->barcodeEndpoint->call();

        if (!is_object($response) || !isset($response->Barcode)) {
            // Should throw an exception otherwise the postnl flow will break.
            throw new LocalizedException(
                __('Invalid GenerateBarcode response: %1', var_export($response, true))
            );
        }

        return (string) $response->Barcode;
    }

    /**
     * @param $code
     */
    private function setTypeByProductCode($code)
    {
        if ($this->productOptionsConfiguration->checkProductByFlags($code, 'group', 'priority_options')) {
            $this->barcodeEndpoint->setType('PEPS');
            
            return;
        }

        $this->barcodeEndpoint->setType('');
    }

    /**
     * @param $shipmentId
     * @param $count
     * @param $barcode
     *
     * @return ShipmentBarcode
     */
    private function createBarcode($shipmentId, $count, $barcode)
    {
        /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
        $barcodeModel = $this->shipmentBarcodeFactory->create();
        $barcodeModel->setParentId($shipmentId);
        $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);
        $barcodeModel->setNumber($count);
        $barcodeModel->setValue($barcode);

        return $barcodeModel;
    }
}
