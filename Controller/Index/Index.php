<?php
namespace Devester\Poll\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	protected $_pollFactory;

	protected $_answerFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Devester\Poll\Model\PollFactory $pollFactory,
		\Devester\Poll\Model\AnswerFactory $answerFactory
		)
	{
		$this->_pageFactory = $pageFactory;
		$this->_pollFactory = $pollFactory;
		$this->_answerFactory = $answerFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$polls = $this->_pollFactory->create();


		// $answerCollection = $answers->getCollection();

		$answers = $this->_answerFactory->create();

		$pollCollection = $polls->getCollection();
		foreach($pollCollection as $poll){

			$answerCollection = $answers->getCollection()->addFieldToFilter('poll_id', $poll->getData('poll_id'));

			echo "<pre>";
			echo "<hr>";
			print_r($poll->getData());
			echo "<br><br>";
			foreach($answerCollection as $answer){
				print_r($answer->getData());
			}
			echo "<hr>";
			echo "</pre>";
		}
		exit();
		return $this->_pageFactory->create();
	}
}