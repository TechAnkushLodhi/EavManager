<?php
namespace Icecube\EavManager\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Icecube\EavManager\Api\EavManagerRepositoryInterface as EavManagerRepository;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    protected $eavManagerRepository;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        EavManagerRepository $eavManagerRepository,
        JsonFactory $jsonFactory,
    ) {
        parent::__construct($context);
        $this->eavManagerRepository = $eavManagerRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $attributeItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($attributeItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($attributeItems) as $Id) {
            $attribute = $this->eavManagerRepository->getById($Id);
            try {
                $attributeData = $attributeItems[$Id];
                $extendedAttributeData = $attribute->getData();
                $this->setAttributeData($attribute, $extendedAttributeData, $attributeData);
                $this->eavManagerRepository->save($attribute);

                $this->messageManager->addSuccess(__('Attribute ID: %1 was successfully saved', $Id));

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $error = true;
                $messages[] = $this->getErrorWithAttributeId($attribute, $e->getMessage());
                $this->messageManager->addErrorMessage($e->getMessage()); 
            } catch (\RuntimeException $e) {
                $error = true;
                $messages[] = $this->getErrorWithAttributeId($attribute, $e->getMessage());
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $error = true;
                $messages[] = $this->getErrorWithAttributeId(
                    $attribute,
                    __('Something went wrong while saving the attribute.')
                );
            }
        }
        
        return $resultJson->setData([
            'success' => !$error, 
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function getErrorWithAttributeId(\Icecube\EavManager\Api\Data\EavManagerInterface $attribute, $errorText)
    {
        return '[ID: ' . $attribute->getId() . '] ' . $errorText;
    }

    public function setAttributeData(\Icecube\EavManager\Model\EavManager $attribute, array $extendedAttributeData, array $attributeData)
    {
        $attribute->setData(array_merge($attribute->getData(), $extendedAttributeData, $attributeData));
        return $this;
    }
}

?>
