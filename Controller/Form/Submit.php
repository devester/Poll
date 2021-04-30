<?php

namespace Devester\Poll\Controller\Form;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DomDocument\DomDocumentFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Devester\Poll\Model\PollFactory;
use Devester\Poll\Model\AnswerFactory;


class Submit extends \Magento\Framework\App\Action\Action
{
    /**
     * Variable.
     *
     * @var MessageManager
     */
    protected $_messageManager;


	protected $_pollFactory;

	protected $_answerFactory;

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * Construct.
     *
     * @param Context                $context                Context.
     * @param PollCollectionFactory  $PollCollectionFactory  Poll Collection.
     * @param SlideCollectionFactory $slideCollectionFactory Slide Collection.
     * @param CookieManagerInterface $cookieManager          Cookie Manager.
     * @param SessionManagerInterface $sessionManager        Session Manager.
     */
    public function __construct(
    	Context $context,
        ManagerInterface $messageManager,
        DomDocumentFactory $domFactory,
        Http $request,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
		PollFactory $pollFactory,
		AnswerFactory $answerFactory
    ) {
    	parent::__construct($context);
    	$this->request = $request;
    	$this->domFactory = $domFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
		$this->_pollFactory = $pollFactory;
		$this->_answerFactory = $answerFactory;
        $this->_messageManager = $messageManager;
    }

    /**
     * Submit action
     *
     * @return void
     */
    public function execute()
    {
        // Get Submit data
        $post = (array) $this->getRequest()->getPost();

			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
            // $logger->info('PAST SAVES');



        if (!empty($post)) {


            // Retrieve post data
            $poll_id = $this->request->getParam('poll_id');
            $answer_id = $post['vote'];

            // Display the succes form validation message
            $this->_messageManager->addSuccessMessage('Poll answered!');

			$pollModel = $this->_pollFactory->create();
			$answerModel = $this->_answerFactory->create();

			// Load Models
			$pollModel->load($poll_id);
			$answerModel->load($answer_id);

			// Add Vote to Poll
			$var1 = $pollModel->getData('votes_count');
			$var2 = $var1 + 1;
			$pollModel->setData('votes_count', $var2);

			// Add Vote to Answer
			$var1 = $answerModel->getData('votes_count');
			$var2 = $var1 + 1;
            $answerModel->setData('votes_count', $var2);

            // Models Save
            $pollModel->save();
            $answerModel->save();

            // Return
            // return 'success';

	        $cookieValueArray = array();
	        $noCookie = 0;
	       	if ($this->cookieManager->getCookie('devester_submitted_polls')) {
	       		$cookieValueArray =  $this->cookieManager->getCookie('devester_submitted_polls');
	       		$cookieValueArrayDecoded = json_decode($cookieValueArray, true);
	       		foreach ($cookieValueArrayDecoded as $cookiePoll) {
	       			if ($cookiePoll == $poll_id) {
	       				$noCookie = 1;
	       			}
	       		}
	       	}


	       	if ($noCookie == 0) {
	       		$this->cookieManager->deleteCookie('devester_submitted_polls');

	       		$cookieValueArray[] = $poll_id;
		        $duration = time() + (10 * 365 * 24 * 60 * 60);
		        $metadata = $this->cookieMetadataFactory
		            ->createPublicCookieMetadata()
		            ->setDuration($duration)
		            ->setPath($this->sessionManager->getCookiePath())
		            ->setDomain($this->sessionManager->getCookieDomain());

		        $this->cookieManager->setPublicCookie(
		            'devester_submitted_polls',
		            json_encode($cookieValueArray),
		            $metadata
		        );
	       	}

            // Redirect
	        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	        $demo = $resultRedirect->setUrl($this->_redirect->getRefererUrl());

	        return $demo;
        }
    }
}