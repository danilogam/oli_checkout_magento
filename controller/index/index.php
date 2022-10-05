<?php

namespace Oli\Integration\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->cart = $cart;

        parent::__construct($context);
    }

    public function execute()
    {

        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');

        $product_url = explode(',', $_GET['product']);
        $qty_url = explode(',', $_GET['qty']);


        if (count($product_url) > 0) {


            try {

                foreach ($product_url as $key => $prod) {

                    $params = array();
                    $pId = $prod;
                    $params['qty'] = (!empty($qty_url[$key])) ? $qty_url[$key] : '1';

                    $product_db = $this->product->getIdBySku($pId);


                    if ($product_db !== false) {

                        $this->cart->addProduct($product_db, array('qty', $qty_url[$key]));


                        //var_dump($this->cart);
                        //exit;
                    }
                }

                $this->cart->save();



                $this->messageManager->addSuccess(__('Add to cart successfully.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException($e, __('%1', $e->getMessage()));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('error.'));
            }
        }


        /*cart page*/
        $this->getResponse()->setRedirect($urlInterface->getBaseUrl() . 'checkout/cart');
    }
}
