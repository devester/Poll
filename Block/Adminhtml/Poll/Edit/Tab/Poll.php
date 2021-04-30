<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Block\Adminhtml\Poll\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Devester Poll form block
 */
class Poll extends Generic implements TabInterface
{
    /**
     * Variable.
     *
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * Variable.
     *
     * @var Store
     */
    protected $systemStore;

    /**
     * Construct.
     *
     * @param Context      $context          Context.
     * @param Registry     $registry         Registry.
     * @param FormFactory  $formFactory      Context.
     * @param Store        $systemStore      Systemstore.
     * @param FieldFactory $fieldFactory     FormFactory.
     * @param array        $data             Data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->_localeDate = $context->getLocaleDate();
        $this->systemStore = $systemStore;
        $this->fieldFactory = $fieldFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('devester_poll_poll');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('poll_');
        // $form->setFieldNameSuffix('poll');

        $dependenceBlock = $this->getLayout()
        ->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $fieldMaps = [];
    
    	$fieldset = $form->addFieldset(
                    'general_fieldset',
                    ['legend' => __('General')]
                );

        if ($model->getId()) {
            $fieldset->addField('poll_id', 'hidden', ['name' => 'poll_id']);
        }

        $fieldMaps['title'] = $fieldset->addField(
            'title',
            'text',
            [
            'name' => 'title',
            'label' => __('Title'),
            'title' => __('Title'),
            'required' => true,
            'class' => 'required-entry',
            ]
        );

        $fieldMaps['status'] = $fieldset->addField(
            'status',
            'select',
            [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'required' => true,
            'options' => $model->getAvailableStatuses()
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->systemStore
                            ->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this
                ->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                'name' => 'stores[]',
                'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        foreach ($fieldMaps as $fieldMap) {
            $dependenceBlock->addFieldMap(
                $fieldMap->getHtmlId(),
                $fieldMap->getName()
            );
        }

/*
        $mappingFieldDependence = $this->getMappingFieldDependence();

        foreach ($mappingFieldDependence as $dep) {
            $negative = isset($dep['negative']) && $dep['negative'];
            if (is_array($dep['fieldName'])) {
                foreach ($dep['fieldName'] as $fieldName) {
                    $dependenceBlock->addFieldDependence(
                        $fieldMaps[$fieldName]->getName(),
                        $fieldMaps[$dep['fieldNameFrom']]->getName(),
                        $this->getDependencyField($dep['refField'], $negative)
                    );
                }
            } else {
                $dependenceBlock->addFieldDependence(
                    $fieldMaps[$dep['fieldName']]->getName(),
                    $fieldMaps[$dep['fieldNameFrom']]->getName(),
                    $this->getDependencyField($dep['refField'], $negative)
                );
            }
        }

*/
        $this->setChild('form_after', $dependenceBlock);
        $defaultData = [
            'status' => 1,
        ];

        if (!$model->getId()) {
            $model->addData($defaultData);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();

    }

    /**
     * Get field dependencies.
     *
     * @param string $refField    Reference field for field dependency.
     * @param bool   $negative    Invert selection of reference.
     * @param string $separator   Field seperator.
     * @param string $fieldPrefix Field prefix.
     *
     * @return $this->fieldFactory
     */
    public function getDependencyField(
        $refField,
        $negative = false,
        $separator = ',',
        $fieldPrefix = ''
    ) {
        return $this->fieldFactory->create(
            ['fieldData' =>
                [
                'value' => (string) $refField,
                'negative' => $negative,
                'separator' => $separator
                ],
                'fieldPrefix' => $fieldPrefix
            ]
        );
    }

    /**
     * Prepare label for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Poll Config');
    }

    /**
     * Prepare title for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
