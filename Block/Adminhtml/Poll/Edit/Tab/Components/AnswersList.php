<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Block\Adminhtml\Poll\Edit\Tab\Components;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Validator\UniversalFactory;

use Magento\Backend\Block\Template;
// use Devester\Poll\Model\Answer;

/**
 * Devester Poll form block
 */
class AnswersList extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'Devester_Poll::poll/form/components/answers_list.phtml';

    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;

    /**
     * @var Devester\Poll\Model\Answer $answerModel
     */

    /*
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    * THI SHITTY MODEL GIVES 503 ERROR
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    *
    */
    protected $_answerModel;

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
        UniversalFactory $universalFactory,
        // Answer $answerModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_universalFactory = $universalFactory;
        // $this->_answerModel = $answerModel;
    }
    
	public function getAnswerArray()
	{

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/devesterpoll.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);



		$answersAray = [];
		$answersAray[0]['id'] = 'custom_attribute1';
		$answersAray[0]['magento'] = 'image';
		$answersAray[0]['constant'] = 1;
        $answersAray[0]['limit'] = NULL;
        $answersAray[0]['eppav'] = 0;
        $answersAray[0]['postproc'] = [NULL];
        $answersAray[0]['composed_value'] = NULL;
        $answersAray[0]['index'] = 0;
        $answersAray[0]['position'] = 1;
        $answersAray[0]['grid_index'] = 0;

        $answersAray[1]['id'] = 'custom_attribute2';
        $answersAray[1]['magento'] = 'category_ids';
        $answersAray[1]['constant'] = NULL;
        $answersAray[1]['limit'] = NULL;
        $answersAray[1]['eppav'] = 0;
        $answersAray[1]['postproc'] = [NULL];
        $answersAray[1]['composed_value'] = NULL;
        $answersAray[1]['index'] = 1;
        $answersAray[1]['position'] = 2;
        $answersAray[1]['grid_index'] = 1;


        // $answersAray = [];
        // $answersAray[0]['label'] = 1;
        // $answersAray[0]['title'] = 'test title';
        // $answersAray[0]['votes_count'] = 352;
        // $answersAray[0]['index'] = 0;
        // $answersAray[0]['position'] = 1;
        // $answersAray[0]['grid_index'] = 0;



		$logger->info('Array Log '.print_r($answersAray, true)); // Array Log
		
		return $answersAray;
		// $model = $this->_registry->registry('devester_poll_poll');

		// $answers = $this->_answerModel->getAnswers($model->getId());

		// $answers = $this->_answerModel->getAnswers(1);


		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$answerModelOBJ = $objectManager->create('\Devester\Poll\Model\Answer');
		$answers = $answerModelOBJ->getAnswers(1);

		$answersAray = [];

		// $logger->info('Simple Text Log'); // Simple Text Log
		foreach ($answers as $key => $answer) {
			$answersAray[$key]['id'] = $answer->getId();
			$answersAray[$key]['title'] = $answer->getTitle();
			$answersAray[$key]['votes_count'] = $answer->getVotesCount();
			$answersAray[$key]['answer_order'] = $answer->getAnswerOrder();
		}

		$logger->info('Array Log '.print_r($answersAray, true)); // Array Log
		return $answersAray;

		// Create array freom answers using model 
		/*
		$model = $this->_registry->registry('koongo_channel_profile');
		$config = $model->getConfigItem(Profile::CONFIG_FEED,true,Profile::CONFIG_COMMON);
		$intervals = [];
		
		if(isset($config['shipping']['cost_setup']))
			$intervals = $config['shipping']['cost_setup'];
		
		$values = [];
		$index = 0;
		foreach($intervals as $key => $value)
		{
			$value["id"] = "option_".$index;
			$value["sort_order"] = $index;
			$values[] = $value;
			$index++;
		}
		return $values;
		*/
	}

	public function getAnswerArrayJSON() {
		$answerArray = $this->getAnswerArray();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logfile.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Array Log '.print_r($answerArray, true));
        // $logger->info(is_array($answerArray) ? 'Array' : 'not an Array';); // Array Log


		return json_encode($answerArray);
	}
}
