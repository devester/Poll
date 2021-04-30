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
class Answers extends Generic implements TabInterface
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
                    'poll_answers',
                    ['legend' => __('Answers')]
                );
        if ($model->getId()) {
            $fieldset->addField('poll_id', 'hidden', ['name' => 'poll_id']);
        }
    	/*

        $fieldset->addField(
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

        $fieldset->addField(
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

*/

        if (!$model->getId()) {
            $model->addData($defaultData);
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();

    }

    /**
     * Prepare label for tab.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Poll Answers');
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
