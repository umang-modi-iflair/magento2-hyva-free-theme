<?php
namespace Iflair\SizeChart\Block\Adminhtml\SizeChart\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Store\Model\System\Store;
use Iflair\SizeChart\Model\ResourceModel\SizeChart\CollectionFactory as SizeUnitCollectionFactory;
use Iflair\SizeChart\Model\ResourceModel\Measurement\CollectionFactory as MeasurementCollectionFactory;

class Main extends Generic implements TabInterface
{
    protected $_systemStore;
    protected $sizeUnitCollectionFactory;
    protected $measurementCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Store $systemStore,
        SizeUnitCollectionFactory $sizeUnitCollectionFactory,
        MeasurementCollectionFactory $measurementCollectionFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->sizeUnitCollectionFactory = $sizeUnitCollectionFactory;
        $this->measurementCollectionFactory = $measurementCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('sizechart_data');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($model && $model->getId()) {
            $fieldset->addField(
                'template_id',
                'hidden',
                ['name' => 'template_id']
            );
        }

        $fieldset->addField(
            'template_name',
            'text',
            [
                'name'     => 'template_name',
                'label'    => __('Template Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'template_code',
            'text',
            [
                'name'     => 'template_code',
                'label'    => __('Template Code'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name'   => 'status',
                'label'  => __('Status'),
                'values' => [
                    ['value' => 1, 'label' => __('Enabled')],
                    ['value' => 0, 'label' => __('Disabled')],
                ]
            ]
        );

        /* ---------- Size Units ---------- */
        $sizeUnitOptions = [];
        $sizeUnitCollection = $this->sizeUnitCollectionFactory->create();

        foreach ($sizeUnitCollection as $unit) {
            $sizeUnitOptions[] = [
                'value' => $unit->getSizeUnit(),
                'label' => $unit->getSizeUnit(),
            ];
        }

        $fieldset->addField(
            'size_unit',
            'checkboxes',
            [
                'name'     => 'size_unit[]',
                'label'    => __('Size Units'),
                'values'   => $sizeUnitOptions,
                'required' => true,
            ]
        );

        /* ---------- Measurements ---------- */
        $measurementOptions = [];
        $measurementCollection = $this->measurementCollectionFactory->create();

        foreach ($measurementCollection as $measurement) {
            $measurementOptions[] = [
                'value' => $measurement->getMeasurements(),
                'label' => $measurement->getMeasurements(),
            ];
        }

        $fieldset->addField(
            'measurement',
            'checkboxes',
            [
                'name'   => 'measurement[]',
                'label'  => __('Measurements'),
                'values' => $measurementOptions,
            ]
        );

            $fieldset->addField(
                'generate_template',
                'note',
                [
                    'label' => '',
                    'text'  => '
                    <button type="button" class="action-primary" id="generate-template">
                        <span>' . __('Generate Template') . '</span>
                    </button>

                    <script>
                        require([
                            "jquery",
                            "Magento_Ui/js/modal/alert"
                        ], function ($, alert) {

                            $("#generate-template").on("click", function () {

                                var sizeChecked = $("input[name=\'size_unit[]\']:checked").length;
                                var measurementChecked = $("input[name=\'measurement[]\']:checked").length;

                                if (sizeChecked === 0 || measurementChecked === 0) {
                                    alert({
                                        title: "Required Checkboxes",
                                        content: "Need to check at least one checkbox from Size Units and Body Measurements."
                                    });
                                    return false;
                                }

                                // SHOW PREVIEW TABLE
                                $("#template-preview").fadeIn();
                            });
                        });
                    </script>
                    '
                ]
            );

            $fieldset->addField(
            'template_preview',
            'note',
            [
                'label' => '',
                'text'  => '
                <div id="template-preview" style="display:none; margin-top:20px; border:2px solid #514943; padding:15px; width:300px;">
                    
                    <table class="admin__table-secondary" style="background-color:514943; text-align:center;">
                        <thead>
                            <tr style="background:#3b3b3b; color:white;">
                                <th>SIZE</th>
                                <th>SHOULDER</th>
                                <th>LENGTH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>S</td>
                                <td><input type="text" style="width:40px"/></td>
                                <td><input type="text" style="width:40px"/></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                '
            ]
        );


        if ($model) {
            $data = $model->getData();

            if (!empty($data['size_unit'])) {
                $data['size_unit'] = explode(',', $data['size_unit']);
            }

            if (!empty($data['measurement'])) {
                $data['measurement'] = explode(',', $data['measurement']);
            }

            $form->setValues($data);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('General');
    }

    public function getTabTitle()
    {
        return __('General');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
