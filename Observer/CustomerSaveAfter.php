<?php
namespace Burst\Chronos\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerSaveAfter implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    private $logger;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Psr\Log\LoggerInterface $logger,
        \Burst\Chronos\Helper\ChronosApi $chronosApi,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->chronosApi = $chronosApi;
        $this->_objectManager = $objectManager;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->chronosApi->token  != false) {
            try {
                $event = $observer->getEvent();
                $customer = $event->getCustomer();
                $external_id = $customer->getId();
                $email = $customer->getEmail();
                $firstname = $customer->getFirstName();
                $lastname = $customer->getLastName();
                $company= 1;
                $source= 1;
                $json_data =json_encode($customer->getData());
                $data = [
                    'external_id'=>$external_id,
                    'email'=>$email,
                    'firstname'=>$firstname,
                    "lastname"=> $lastname,
                    'company'=>$company,
                    'source'=>$source,
                    'json_data'=>$json_data,
                ];
                $final_json_data= \json_encode($data,true);
                // $this->chronosApi->createOrUpdateCustomer($external_id, $final_json_data);
            } catch (xception $e) {
                $this->logger->addInfo('Chronos CustomerSaveAfter Main', ["Error"=>$e->getMessage()]);
            }
        }     
    }
}