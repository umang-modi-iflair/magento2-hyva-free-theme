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
            ['legend' => __('General Information'), 'class'  => 'fieldset-wide']
        );

        if ($model && $model->getId()) {
            $fieldset->addField('template_id', 'hidden', ['name' => 'template_id']);
        }

        $fieldset->addField('template_name', 'text', [
            'name' => 'template_name',
            'label' => __('Template Name'),
            'required' => true
        ]);

        $fieldset->addField('template_code', 'text', [
            'name' => 'template_code',
            'label' => __('Template Code'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'values' => [
                ['value' => 1, 'label' => __('Enabled')],
                ['value' => 0, 'label' => __('Disabled')],
            ]
        ]);

        /* ---------- Size Units Checkboxes ---------- */
        $sizeUnitOptions = [];
        foreach ($this->sizeUnitCollectionFactory->create() as $unit) {
            $sizeUnitOptions[] = ['value' => $unit->getSizeUnit(), 'label' => $unit->getSizeUnit()];
        }

        $fieldset->addField('size_unit', 'checkboxes', [
            'name' => 'size_unit[]',
            'label' => __('Size Units'),
            'values' => $sizeUnitOptions,
            'required' => true
        ]);

        /* ---------- Measurements Checkboxes ---------- */
        $measurementOptions = [];
        foreach ($this->measurementCollectionFactory->create() as $measurement) {
            $measurementOptions[] = ['value' => $measurement->getMeasurements(), 'label' => $measurement->getMeasurements()];
        }

        $fieldset->addField('measurement', 'checkboxes', [
            'name' => 'measurement[]',
            'label' => __('Measurements'),
            'values' => $measurementOptions,
        ]);

        /* ---------- Hidden Data Field ---------- */
        $fieldset->addField('size_chart_data', 'hidden', ['name' => 'size_chart_data']);

        /* ---------- Button & Logic ---------- */
        $fieldset->addField('generate_template', 'note', [
            'text' => '
            <button type="button" class="action-primary" id="generate-template">
                <span>' . __('Generate Template') . '</span>
            </button>

            <script>
            require(["jquery", "Magento_Ui/js/modal/alert"], function ($, alert) {
                
                function buildTable(existingData = null) {
                    const sizes = [];
                    const measurements = [];

                    $("input[name=\'size_unit[]\']:checked").each(function () {
                        sizes.push($(this).val());
                    });

                    $("input[name=\'measurement[]\']:checked").each(function () {
                        measurements.push($(this).val());
                    });

                    if (!sizes.length || !measurements.length) {
                        $("#template-preview").hide();
                        return;
                    }

                    const table = $("#size-chart-table");
                    table.find("thead, tbody").empty();

                    let thead = "<tr><th>Size</th>";
                    measurements.forEach(m => thead += "<th>" + m + "</th>");
                    thead += "</tr>";
                    table.find("thead").append(thead);

                    sizes.forEach(size => {
                        let row = "<tr><td><strong>" + size + "</strong></td>";
                        measurements.forEach(m => {
                            const val = (existingData && existingData[size]) ? (existingData[size][m] || "") : "";
                            row += "<td><input type=\"text\" class=\"size-input\" " +
                                "data-size=\"" + size + "\" data-measurement=\"" + m + "\" " +
                                "value=\"" + val + "\" style=\"width:60px\" /></td>";
                        });
                        row += "</tr>";
                        table.find("tbody").append(row);
                    });

                    $("#template-preview").show();
                }

                // Handle Generate Click
                $("#generate-template").on("click", function () {
                    if (!$("input[name=\'size_unit[]\']:checked").length || !$("input[name=\'measurement[]\']:checked").length) {
                        alert({ title: "Selection Required", content: "Please select Size Units and Measurements first." });
                        return;
                    }
                    buildTable();
                });

                // Sync UI table to hidden field before save
                $("body").on("beforeSubmit", function () {
                    const data = {};
                    $(".size-input").each(function () {
                        const size = $(this).data("size");
                        const m = $(this).data("measurement");
                        if (!data[size]) data[size] = {};
                        data[size][m] = $(this).val();
                    });
                    $("input[name=\'size_chart_data\']").val(JSON.stringify(data));
                });

                // Initialize on load for Edit mode
                $(document).ready(function () {
                    const savedValue = $("input[name=\'size_chart_data\']").val();
                    if (savedValue && savedValue !== "" && savedValue !== "[]") {
                        try {
                            buildTable(JSON.parse(savedValue));
                        } catch (e) { console.error("Invalid JSON in size_chart_data"); }
                    }
                });
            });
            </script>'
        ]);

        /* ---------- Table Container ---------- */
        $fieldset->addField('template_preview', 'note', [
            'text' => '
            <div id="template-preview" style="display:none; margin-top:20px; border:1px solid #adadad; padding:15px; background:#fff;">
                <table class="admin__table-secondary" id="size-chart-table" style="width:auto">
                    <thead></thead>
                    <tbody class="ui-sortable"></tbody>
                </table>
            </div>'
        ]);

        if ($model) {
            $data = $model->getData();
            if (!empty($data['size_unit']) && !is_array($data['size_unit'])) {
                $data['size_unit'] = explode(',', $data['size_unit']);
            }
            if (!empty($data['measurement']) && !is_array($data['measurement'])) {
                $data['measurement'] = explode(',', $data['measurement']);
            }
            $form->setValues($data);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel() { return __('General Information'); }
    public function getTabTitle() { return __('General Information'); }
    public function canShowTab() { return true; }
    public function isHidden() { return false; }
}