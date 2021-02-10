<?php

namespace nnwebShowPropertiesInListing;

use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\PropertyServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Components\Compatibility\LegacyStructConverter;

class ListProductService implements ListProductServiceInterface {
	/**
	 *
	 * @var ListProductServiceInterface
	 */
	private $service;
	
	/**
	 *
	 * @var PropertyServiceInterface
	 */
	private $propertyService;
	
    /**
	 *
     * @var LegacyStructConverter
     */
    private $legacyStructConverter;

	/**
	 *
	 * @param ListProductServiceInterface $service
	 * @param PropertyServiceInterface    $propertyService
	 * @param LegacyStructConverter       $legacyStructConverter
	 */
	public function __construct(ListProductServiceInterface $service, PropertyServiceInterface $propertyService, LegacyStructConverter $legacyStructConverter) {
		$this->service = $service;
		$this->propertyService = $propertyService;
        $this->legacyStructConverter = $legacyStructConverter;
	}

	/**
	 * @inheritdoc
	 */
	public function getList(array $numbers, Struct\ProductContextInterface $context) {
		$products = $this->service->getList($numbers, $context);
		$propertieSets = $this->propertyService->getList($products, $context);
		$legacyProps = $this->convertPropertyStructs($propertieSets);
		
		/**@var $product Struct\ListProduct*/
		foreach ($products as $product) {
			$productId = $product->getNumber();
			if (!isset($legacyProps[$productId])) {
				continue;
			}
			
			$attribute = new Struct\Attribute([
				'property' => $legacyProps[$productId]
			]);

			$product->addAttribute('nnwebShowPropertiesInListing', $attribute);
		}

		return $products;
	}

	private function convertPropertyStructs($propertySets) {
		// convert property set Structs to legacy Array format
		$legacyProps = [];
		foreach ($propertySets as $ordernumber => $propertySet) {
			$legacyProps[$ordernumber] = $this->legacyStructConverter->convertPropertySetStruct($propertySet);
		}
		return $legacyProps;
	}

	/**
	 * @inheritdoc
	 */
	public function get($number, Struct\ProductContextInterface $context) {
		$products = $this->getList([
			$number 
		], $context);
		return array_shift($products);
	}

}
