<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\AssignNewCustomer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Rules extends AbstractFieldArray
{
    /**
     * @var CountryColumn
     */
    private $countryRenderer;

    /**
     * @var GroupColumn
     */
    private $groupRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('email_domain', [
            'label' => __('Email Domain'),
            'class' => 'required-entry'
        ]);
        $this->addColumn('country', [
            'label' => __('Country'),
            'class' => 'required-entry',
            'renderer' => $this->getCountryRenderer()
        ]);
        $this->addColumn('customer_group', [
            'label' => __('Customer Groups'),
            'class' => 'required-entry',
            'renderer' => $this->getGroupRenderer()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $country = $row->getCountry();
        if ($country !== null) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)] = 'selected="selected"';
        }

        $groups = $row->getCustomerGroups();
        if ($groups !== null) {
            $options['option_' . $this->getGroupRenderer()->calcOptionHash($groups)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return CountryColumn
     * @throws LocalizedException
     */
    private function getCountryRenderer(): CountryColumn
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                CountryColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }

    /**
     * @return GroupColumn
     * @throws LocalizedException
     */
    private function getGroupRenderer(): GroupColumn
    {
        if (!$this->groupRenderer) {
            $this->groupRenderer = $this->getLayout()->createBlock(
                GroupColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->groupRenderer;
    }
}