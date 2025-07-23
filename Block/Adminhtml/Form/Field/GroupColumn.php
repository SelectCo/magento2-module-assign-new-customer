<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\AssignNewCustomer\Block\Adminhtml\Form\Field;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class GroupColumn extends Select
{
    /**
     * @var CollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * Activation constructor.
     *
     * @param Context $context
     * @param CollectionFactory $groupCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $groupCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        return $this->groupCollectionFactory->create()->loadData()->toOptionArray();
    }
}
