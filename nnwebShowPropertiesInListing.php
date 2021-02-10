<?php

namespace nnwebShowPropertiesInListing;

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Theme\LessDefinition;

class nnwebShowPropertiesInListing extends Plugin {

	public static function getSubscribedEvents() {
		return [
			'Enlight_Bootstrap_AfterInitResource_shopware_storefront.list_product_service' => 'registerListProductService',
			'Enlight_Controller_Action_PostDispatchSecure_Frontend'                        => 'onFrontendPostDispatch',
			'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing'                => 'onFrontendPostDispatch',
			'Enlight_Controller_Action_PreDispatch_Widgets'                                => 'onFrontendPostDispatch',
			'Enlight_Controller_Action_PreDispatch_Widgets_Listing'                        => 'onFrontendPostDispatch',
			'Theme_Compiler_Collect_Plugin_Less'                                           => 'addLessFiles',
		];
	}

	public function activate(ActivateContext $context) {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_FRONTEND);
		parent::activate($context);
	}

	public function deactivate(DeactivateContext $context) {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_FRONTEND);
		parent::deactivate($context);
	}

	public function update(UpdateContext $context) {
		$context->scheduleClearCache(InstallContext::CACHE_LIST_FRONTEND);
		parent::update($context);
	}

	public function registerListProductService() {
		$coreService = Shopware()->Container()->get('shopware_storefront.list_product_service');
		$propertyService = Shopware()->Container()->get('shopware_storefront.property_service');
		$legacyStructConverter = Shopware()->Container()->get('legacy_struct_converter');

		$newService = new ListProductService($coreService, $propertyService, $legacyStructConverter);
		Shopware()->Container()->set('shopware_storefront.list_product_service', $newService);
	}

	public function onFrontendPostDispatch(\Enlight_Controller_ActionEventArgs $args) {
		$this->container->get('template')->addTemplateDir($this->getPath() . '/Resources/views/');
	}

	public function addLessFiles(\Enlight_Event_EventArgs $args) {
		$less = new LessDefinition(
			[], [
			__DIR__ . '/Resources/views/frontend/_public/src/less/all.less',
		], __DIR__
		);

		return new ArrayCollection(
			[
				$less,
			]
		);
	}
}
