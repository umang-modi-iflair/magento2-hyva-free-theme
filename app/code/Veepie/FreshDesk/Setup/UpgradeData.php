<?php

namespace Veepie\FreshDesk\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var EavSetupFactory
     */
    public $eavSetupFactory;

    /**
     * Constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addFreshdeskCompanyId($setup);
            $this->addFreshdeskContactId($setup);
            $this->addEnableMyTickets($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function addFreshdeskCompanyId($setup)
    {
        $customerSetup = $this->customerSetupFactory->create([
            'setup' => $setup
        ]);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'freshdesk_company_id'
        );

        $customerSetup->addAttribute('customer', 'freshdesk_company_id', [
            'label' => 'Freshdesk Company ID',
            'frontend' => '',
            'required' => 0,
            'source' => '',
            'visible' => 1, // <-- important, to display the attribute in customer edit
            'input' => 'text',
            'type' => 'varchar',
            'system' => 0, // <-- important, to have the value be saved
            'position' => 1000,
            'sort_order' => 10000,
            "unique" => false
        ]);
        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $setup
        ]);
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute($typeId, 'freshdesk_company_id');

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple($customerSetup->getSetup()
                ->getTable('customer_form_attribute'), [
                'form_code' => 'adminhtml_customer',
                'attribute_id' => $attribute['attribute_id']
            ]);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function addFreshdeskContactId($setup)
    {
        $customerSetup = $this->customerSetupFactory->create([
            'setup' => $setup
        ]);
        $customerSetup->removeAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'freshdesk_contact_id'
        );

        $customerSetup->addAttribute('customer', 'freshdesk_contact_id', [
            'label' => 'Freshdesk Contact ID',
            'frontend' => '',
            'required' => 0,
            'source' => '',
            'visible' => 0, // <-- important, to display the attribute in customer edit
            'input' => 'text',
            'type' => 'varchar',
            'system' => 0, // <-- important, to have the value be saved
            'position' => 1000,
            'sort_order' => 10000,
            "unique" => false
        ]);
        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $setup
        ]);
        $typeId = $eavSetup->getEntityTypeId('customer');

        $attribute = $eavSetup->getAttribute($typeId, 'freshdesk_contact_id');

        $customerSetup->getSetup()
            ->getConnection()
            ->insertMultiple($customerSetup->getSetup()
                ->getTable('customer_form_attribute'), [
                'form_code' => 'adminhtml_customer',
                'attribute_id' => $attribute['attribute_id']
            ]);
    }

     /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function addEnableMyTickets($setup)
    {
        //create customer attribute enable my ticket
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        //delete existing attribute
        $customerSetup->removeAttribute(Customer::ENTITY, 'enable_my_ticket');

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'enable_my_ticket', [
            'type' => 'int',
            'label' => 'Enable My Tickets',
            'input' => 'boolean',
            'required' => 0,
            'visible' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'user_defined' => false,
            'is_user_defined' => false,
            'sort_order' => 10000,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'position' => 1000,
            'default' => 0,
            'system' => 0,
        ]);

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'enable_my_ticket')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                    'adminhtml_customer'
                ],
            ]);

        $attribute->save();
    }
}
