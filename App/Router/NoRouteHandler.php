<?php
namespace Hgati\PagenotfoundRedirect\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
	protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

	public function process(\Magento\Framework\App\RequestInterface $request)
	{
        $storeCode = $this->storeManager->getStore()->getCode(); // Get the current store code
		$requestValue = ltrim($request->getPathInfo(), '/');
		$requestValue = preg_replace("/^$storeCode\/(.+)/i", '$1', $requestValue);
		if(empty($requestValue)) return false;

		$array = explode('/', $requestValue);
		$reversedArray = array_reverse($array);
		$searchQueryArray = array_map(function($item) {
			return str_replace(['-', '.'], ' ', $item);
		}, $reversedArray);
		if(empty($searchQueryArray)) return false;

		$searchQuery = implode(' ', $searchQueryArray);
		$request->setParam('q', $searchQuery);

		$request->setModuleName('catalogsearch')->setControllerName('result')->setActionName('index');
		return true;
	}
}