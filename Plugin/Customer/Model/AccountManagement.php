<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SelectCo\AssignNewCustomer\Plugin\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use SelectCo\Core\Helper\Data as CoreHelper;

class AccountManagement
{
    const CUSTOMER_GROUP_RULES = 'selectco_anc/general/groups';

    /**
     * @var CoreHelper
     */
    private $coreHelper;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(CoreHelper $coreHelper, CustomerRepositoryInterface $customerRepository)
    {
        $this->coreHelper = $coreHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param AccountManagementInterface $accountManagement
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterActivate(AccountManagementInterface $accountManagement, CustomerInterface $customer): CustomerInterface
    {
        return $this->changeGroup($accountManagement, $customer);
    }

    /**
     * @param AccountManagementInterface $accountManagement
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws InputException
     * @throws InputMismatchException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterActivateById(AccountManagementInterface $accountManagement, CustomerInterface $customer): CustomerInterface
    {
        return $this->changeGroup($accountManagement, $customer);
    }

    /**
     * @param AccountManagementInterface $accountManagement
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws InputMismatchException
     */
    public function changeGroup(AccountManagementInterface $accountManagement, CustomerInterface $customer): CustomerInterface
    {
        $emailAddress = $customer->getEmail();
        $domain = substr(strrchr($emailAddress, "@"), 1);

        $groupRules = $this->coreHelper->getConfigValue(self::CUSTOMER_GROUP_RULES);
        foreach (json_decode($groupRules, true) as $rules) {
            if (array_key_exists('email_domain', $rules) && $domain === $rules['email_domain']) {
                $countryId = $accountManagement->getDefaultBillingAddress($customer->getId())->getCountryId();
                if (array_key_exists('country', $rules) && $countryId === $rules['country']) {
                    if (array_key_exists('customer_group', $rules)) {
                        $customer->setGroupId((int)$rules['customer_group']);
                        $this->customerRepository->save($customer);
                    }
                }
            }
        }
        return $customer;
    }
}
