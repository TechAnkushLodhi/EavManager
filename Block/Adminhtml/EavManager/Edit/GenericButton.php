<?php
/**
 * Copyright © Icecube Digital All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Icecube\EavManager\Block\Adminhtml\EavManager\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Icecube\EavManager\Api\EavManagerRepositoryInterface;

class GenericButton
{
    protected $context;
   
    protected $EavManagerRepositoryInterface;
    
    public function __construct(
        Context $context,
        EavManagerRepositoryInterface $EavManagerRepositoryInterface
    ) {
        $this->context = $context;
        $this->EavManagerRepositoryInterface = $EavManagerRepositoryInterface;
    }

    public function getId()
    {
        try {
            return $this->EavManagerRepositoryInterface->getById(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
?>
