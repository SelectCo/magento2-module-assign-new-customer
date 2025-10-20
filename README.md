# SelectCo Assign New Customer

Automatically assigns new customers to a specific customer group based on simple rules that match their email domain and billing country at the moment their account is activated.

This module adds a small configuration UI in Magento Admin where you can define multiple rules. When a new account is activated (either via email confirmation or programmatically), the module evaluates the rules and, on match, updates the customer's group.

## Features
- Define multiple assignment rules in Admin
- Rule conditions: Email domain AND Country
- Assign to any existing Magento customer group
- Runs on customer activation (afterActivate / afterActivateById plugin)

## Requirements
- Magento 2 (tested with 2.3.5)
- PHP compatible with your Magento version
- Dependencies:
  - select-co/module-core ^1.0

## Installation
You can install this module either via Composer or by placing it in app/code.

### Composer (preferred)
1. Require the package:
   - `composer require select-co/module-assign-new-customer`
2. Enable and set up the module:
   - `bin/magento module:enable SelectCo_AssignNewCustomer`
   - `bin/magento setup:upgrade`
   - In production mode: `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy -f`

### Manual installation (app/code)
1. Copy this directory to `app/code/SelectCo/AssignNewCustomer`.
2. Run:
   - `bin/magento module:enable SelectCo_AssignNewCustomer`
   - `bin/magento setup:upgrade`
   - In production mode: `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy -f`

## Configuration
- Navigate to: Stores > Configuration > SelectCo Settings > Assign Customer Group Config
- Settings:
  - Enabled: Yes/No
  - Customer Group Rules: Grid where each row defines a rule consisting of:
    - Email Domain: e.g., `example.com` (no leading @)
    - Country: Select a country code to match against the customer's default billing address country
    - Customer Groups: The target group to assign when the rule matches

Rule logic:
- On account activation, the module extracts the domain from the customer's email (text after the `@`).
- It loads the customer's default billing address to read the country.
- For each configured rule, if BOTH the email domain and the country match, the customer's group is updated to the rule's selected group.

Notes:
- Matching is exact and case-sensitive as configured; enter domains in the correct form (e.g., `example.co.uk`).
- The country used for matching is taken from the customer's default billing address. Ensure a default billing address exists by the time the account is activated for rules to work as expected.

## How it works (internals)
- A plugin on `Magento\Customer\Api\AccountManagementInterface` runs after `activate` and `activateById`.
- Config path used for rules: `selectco_anc/general/groups` (stored JSON from the system config grid).
- On match, the plugin sets `CustomerInterface::setGroupId()` and saves the customer via `CustomerRepositoryInterface`.

## Troubleshooting
- No group change after activation:
  - Confirm the module is Enabled and rules are saved
  - Verify the email domain typed in the rule exactly matches the customer email domain
  - Ensure the customer has a default billing address with the expected country before activation
  - Check Magento logs for any LocalizedException/MailException errors
- Multiple rules match: The module iterates rules in order returned by config; first matching rule that sets a group will take effect. Avoid overlapping rules.

## License
MIT. See LICENSE.

## Support
If you have a feature request or spotted a bug or a technical problem, create a GitHub issue.