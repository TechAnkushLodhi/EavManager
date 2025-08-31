<?php
 
namespace Icecube\EavManager\Controller\Adminhtml\Attribute;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Icecube\EavManager\Model\EavManagerFactory;
use Icecube\EavManager\Model\ResourceModel\EavManager;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Icecube\EavManager\Helper\CommandManager;
use Icecube\EavManager\Helper\Data;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as eavattributeResource;

// use Magento\Eav\Model\Config;
// use Magento\Eav\Model\Entity\Attribute;
// use Magento\Eav\Model\Entity\TypeFactory;

 
 
class Save extends Action
{
    protected $eavManagerFactory;
    protected $resourceModel;
    protected $resultRedirectFactory;
    protected $messageManager;
    private  $customerSetupFactory;
    private  $attributeSetFactory;
    protected $commandManager;
    protected $_helper;
    protected $attribute;
    protected $_eavAttribute;
 
    public function __construct(
        Context $context,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory,
        EavManagerFactory $eavManagerFactory,
        EavManager $resourceModel,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager,
        CommandManager $commandManager,
        Data $helper,
        Attribute $attribute,
        eavattributeResource $eavattributeResource      
    ) {
        parent::__construct($context);
        $this->eavManagerFactory = $eavManagerFactory;
        $this->resourceModel = $resourceModel;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->commandManager = $commandManager;
        $this->attribute = $attribute;
        $this->_eavAttribute = $eavattributeResource;
        $this->_helper = $helper;


    }
 
    public function execute()
    {
            $data = $this->getRequest()->getPostValue();
            // echo '<pre>'; print_r($data); die;
            if (!$data) {
                    $this->messageManager->addErrorMessage(__('No data received.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
             
            // ============================ Attribute Code Validation Start ============================
                $attribute_code = $data['Properties']['attribute_code']; // Attribute code
                if (empty( $attribute_code)) {
                    $this->messageManager->addErrorMessage(__('Attribute code is required.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
                if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',  $attribute_code)) {
                    $this->messageManager->addErrorMessage(__('Attribute code must start with a letter or underscore and can only contain letters, numbers, and underscores.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
        // ============================ Attribute Code Validation End ============================


         // =============================== Frontend Lebels Validation Start  ===============================
           //------------------ Default frontend label start -----------------------
           $DefaultFrontendLable = $data['Properties']['frontend_label']; // Default label
           if($DefaultFrontendLable == ''){
               $this->messageManager->addErrorMessage(__('Default frontend label is required.'));
               return $this->resultRedirectFactory->create()->setPath('*/*/');
           }
           //------------------ Default frontend label End -----------------------


           //---------------------- Frontend labels store wise start  ----------------------- 
           
           if(isset($data['frontend_labels'])) {
                $FrontendLabelsStoreWise = $data['frontend_labels']; // Frontend labels store wise
                $FrontendLabels = array_filter($FrontendLabelsStoreWise, function ($label) {
                    return !empty($label);
                });
                if (empty($FrontendLabels)) {
                    $FrontendLabels = [];
                } 
           }



    

         
        //---------------------- Frontend labels store wise End ---------------------------   
         // =============================== Frontend Lebel Validation End  ===============================

        // =============================== Backend Type Validation Start  ===============================
              // Backend Type Mapping Based on frontend_input
                $backendTypeMapping = [
                    'text'         => 'varchar',
                    'textarea'     => 'text',
                    'texteditor'   => 'text',
                    'date'         => 'datetime',
                    'boolean'      => 'int',
                    'multiselect'  => 'text',
                    'select'       => 'int',
                    'radio'        => 'int',
                    'checkbox'     => 'text',
                    'image'        => 'varchar',
                    'file'         => 'varchar',
                    'video'        => 'varchar',
                    'audio'        => 'varchar',
                    'time_picker'  => 'varchar',
                    'color_picker' => 'varchar'
                ];
                // ** Validate and Set Backend Type **
                $frontendInput = $data['Properties']['frontend_input'] ?? 'text'; // Default text
                $backendType = $backendTypeMapping[$frontendInput] ?? 'varchar';  // Default varchar   
        // =============================== Backend Lebel Validation  End    ===============================

        // =============================== Frontend Input Validation Start  ===============================
            $frontendTypeMapping = [
                'text'         => 'text',
                'textarea'     => 'textarea',
                'texteditor'   => 'textarea',  
                'date'         => 'date',
                'boolean'      => 'boolean',
                'multiselect'  => 'multiselect',
                'select'       => 'select',
                'radio'        => 'select',  
                'checkbox'     => 'multiselect', 
                'file'         => 'file',   
                'audio'        => 'file',    
                'time_picker'  => 'text',  
                'color_picker' => 'text'   
            ];

            // ** Validate and Set Frontend Type **
            $inputType = $frontendTypeMapping[$frontendInput] ?? 'text';  // Default text
        // =============================== Frontend Input Validation  End    ===============================

          // =============================== Used in Grid  Validation Start  ===============================
           $used_in_grid = $data['advanced_attribute_properties']['is_used_in_grid'] ? true : false; // Default 0
        //    $used_in_sales_grid = $data['advanced_attribute_properties']['is_used_in_sales_grid'] ? true:false; // Default 0
          // =============================== Used in Grid  Validation End  ===============================

          // =============================== Sort Order Validation Start  ===============================
            $sortOrder = !empty($data['storefront_properties']['sort_order']) 
            ? (int) $data['storefront_properties']['sort_order'] 
            : 0;
        // =============================== Sort Order Validation End  ===============================


        // =============================== Is_Requird & Is_visible Validation Start  ===============================
           $Is_Requird = $data['advanced_attribute_properties']['is_required'] ? true : false; // Default 0
           $Is_Visible = $data['storefront_properties']['is_visible'] ? true : false; // Default 0

        // ===============================  Is_Requird & Is_visible Validation End  ===============================

        // ================================= Attribute Default Value Validation Start  ===============================
          echo  $data['Properties']['date'];
        $properties = $data['Properties'] ?? [];

        // Validate and format the default value
        $Default_Value = null;

        if (!empty($properties['default_value'])) {
            $Default_Value = $properties['default_value'];
        } elseif (!empty($properties['date'])) {
            try {
                // Try parsing with expected format: 'd/m/Y'
                $date = \DateTime::createFromFormat('d/m/Y', $properties['date']);
        
                if (!$date || \DateTime::getLastErrors()['warning_count'] > 0 || \DateTime::getLastErrors()['error_count'] > 0) {
                    throw new \Exception("Invalid date format.");
                }
        
                // Format the date to 'Y-m-d' as required
                $Default_Value = $date->format('Y-m-d');
        
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The default date is invalid. Please use the format DD/MM/YYYY.')
                );
            }
        } elseif (!empty($properties['textarea'])) {
            $Default_Value = $properties['textarea'];
        } elseif (array_key_exists('yes_no', $properties)) {
            $Default_Value = $properties['yes_no']; // Allows 0
        }
        


        echo '<pre>'; print_r($Default_Value); die;

        // ================================= Attribute Default Value Validation End ===============================
        

        // ================================= Attribute Forms code Validation Start  ===============================
           $FormsCode = $data['storefront_properties']['show_on_forms'] ?? []; // Default Null Array
        // ================================= Attribute Forms code Validation End    ===============================

        // =============================== Attribute Store View Validation Start  ===============================
         $storeView = $data['storefront_properties']['store_view'] ?? []; // Selected Store view


        // ==============================   Attribute Store View Validation End    ===============================

         // =============================== Attribute Customer Group Validation Start  ===============================
         $CustomerGroups = $data['storefront_properties']['customer_group'] ?? []; // Selected Store view

         if(isset($CustomerGroups) && !empty($CustomerGroups)){
            $CustomerGroups = implode(',', $CustomerGroups);
         }
          
         // ==============================   Attribute Customer Group Validation End    ==============================

          try {
              // Get customer setup and entity type
            $customerSetup = $this->customerSetupFactory->create();
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
             // Get the default attribute group
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);


           //=====================================  Attribute update section Start =============================
            //Check if  Updated attribute page
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model = $this->eavManagerFactory->create()->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
                $attributeId =  $this->attribute->load($model['attribute_id']);
                // Load EAV attribute properly using customerSetup
                $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeId->getAttributeCode());
                if (!$attribute || !$attribute->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
                $attribute_code = $attribute->getAttributeCode();
            }
            
            $attributeId = $this->_eavAttribute->getIdByCode(Customer::ENTITY, $attribute_code); // Check if attribute already exists in eav table
            /**
             * Checks whether an attribute already exists in the EAV table 
             * and verifies if it also exists in the custom table.
             *
             * This ensures consistency between the EAV attribute data 
             * and the custom table data.
             */
            if ($attributeId) {
                $customAttribute = $this->eavManagerFactory->create();
                $existingAttribute = $customAttribute->load($attributeId, 'attribute_id');
                if(!$existingAttribute->getId()){
                    $this->messageManager->addErrorMessage(__('The attribute exists in the EAV table but is missing in the icecube_eav_manager table. Please verify and try again.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
            }
           //=====================================  Attribute update section  End =============================
                if ($attributeId && !empty($attributeId)) {
                    $attribute->setData('frontend_label', $DefaultFrontendLable);
                    $attribute->setData('default_value', $Default_Value);
                    $attribute->setData('is_required', $Is_Requird);
                    $attribute->setData('is_visible', $Is_Visible);
                    $attribute->setData('sort_order', $sortOrder);
                    $attribute->setData('is_used_in_grid', $used_in_grid);
                    $attribute->setData('is_visible_in_grid', $used_in_grid);
                    $attribute->setData('is_filterable_in_grid', $used_in_grid);
                    $attribute->setData('is_searchable_in_grid', $used_in_grid);
                   
                } else {
                    // Add the new attribute
                    $customerSetup->addAttribute(
                        Customer::ENTITY,
                        $attribute_code,
                        [
                            'label' => $DefaultFrontendLable,
                            'input' => $inputType,
                            'type' => $backendType,
                            'source' => '',
                            'required' => $Is_Requird,
                            'position' => $sortOrder,
                            'visible' => $Is_Visible,
                            'default' => $Default_Value,
                            'system' => false,
                            'is_used_in_grid' => $used_in_grid,
                            'is_visible_in_grid' => $used_in_grid,
                            'is_filterable_in_grid' => $used_in_grid,
                            'is_searchable_in_grid' => $used_in_grid,
                            'backend' => ''
                        ]
                    );
                }
           
                $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attribute_code);
           
           
                //----------------- Add store wise labels in Attribute Start  ------------------
                   // Update store-wise labels if provided
                    if (isset($data['frontend_labels'])) {
                        if (!empty($FrontendLabels)) {
                            $attribute->setData('store_labels', $FrontendLabels);
                        }
                       
                    }
                    
                    
                // ------------------- Store Form Addded in Attribute Start -------------------
                    $attribute->addData([
                        'used_in_forms' => is_array($FormsCode) ? $FormsCode : []
                    ]);
                

                    $attribute->addData([
                        'attribute_set_id' => $attributeSetId,
                        'attribute_group_id' => $attributeGroupId
                    
                    ]);

                   // Save the attribute
                    $attribute->save();
                     // only worked when we update the attribute
                    if ($id) {
                       if(isset($data['frontend_labels'])) {
                            if(!empty($FrontendLabels)) {
                                $this->_helper->saveStoreLabels($attribute, $FrontendLabels); // Inject Data helper
                            }
                        }
                       
                    }    
                //------- Save in Custom Table ------------
                $customAttribute = $this->eavManagerFactory->create();
                $existingAttribute = $customAttribute->load($attribute->getId(), 'attribute_id');
               //----------------- Check if attribute already exists in custom table ------------------
                if ($existingAttribute->getId()) {
                    $existingAttribute->setData('store_view_id', implode(',', $storeView));
                    $existingAttribute->setData('customer_form_ids', $CustomerGroups);
                    $existingAttribute->setData('updated_at', date('Y-m-d H:i:s'));
                    $this->resourceModel->save($existingAttribute);
                    $this->messageManager->addSuccessMessage(__('Attribute has been updated. Attribute ID: %1', $attribute->getId()));
                    $customAttributeId = $existingAttribute->getId();
                } else {
                   // ----------------- If not, create a new entry in custom table ------------------
                    $customAttribute->setData([
                        'entity_type_id' => $customerEntity->getId(),
                        'attribute_id' => $attribute->getId(),
                        'store_view_id' => implode(',', $storeView),
                        'customer_form_ids' => $CustomerGroups,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $this->resourceModel->save($customAttribute);
                    $this->messageManager->addSuccessMessage(__('Attribute has been created.'));
                    $customAttributeId = $customAttribute->getId();
                }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        // $output = $this->commandManager->executeCommands();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['entity_id' =>$customAttributeId, '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');

    }
}


