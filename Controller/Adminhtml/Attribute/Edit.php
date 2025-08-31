<?php

namespace Icecube\EavManager\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Icecube\EavManager\Model\EavManagerFactory;

class Edit extends Action
{
    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $eavManagerFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        EavManagerFactory $eavManagerFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->eavManagerFactory = $eavManagerFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icecube_EavManager::eav_manager_save');
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Icecube_EavManager::icecube_eav_manager')
            ->addBreadcrumb(__('Eav Manager'), __('Eav Manager'))
            ->addBreadcrumb(__('Manage All Attributes'), __('Manage All Attributes'));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->eavManagerFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('Icecube_EavManager', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Attribute') : __('New Attribute'),
            $id ? __('Edit Attribute') : __('New Attribute')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Attribute'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Attribute'));

        return $resultPage;
    }
}
?>
