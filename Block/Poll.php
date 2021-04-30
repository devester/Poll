<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Block;

use Devester\Poll\Model\Answer;
use Devester\Poll\Model\ResourceModel\Poll\CollectionFactory as PollCollectionFactory;
use Devester\Poll\Model\ResourceModel\Answer\CollectionFactory as AnswerCollectionFactory;
// use Devester\Poll\Helper\Data;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Cms\Model\BlockFactory;


/**
 * Polls content block
 */
class Poll extends \Magento\Framework\View\Element\Template
{

    /**
     * Variable.
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Variable.
     *
     * @var Page
     */
    protected $_page;

    /**
     * Variable.
     *
     * @var GroupCollectionFactory
     */
    protected $_pollCollectionFactory;

    /**
     * Variable.
     *
     * @var SlideCollectionFactory
     */
    protected $_answerCollectionFactory;

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
     * Variable.
     *
     * @var inheret
     */
    protected $_storeManager;

    /**
     * Variable.
     *
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * Variable.
     *
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * Construct.
     *
     * @param Context                $context                Context.
     * @param Registry               $coreRegistry           CoreRegistry.
     * @param PollCollectionFactory  $PollCollectionFactory  Poll Collection.
     * @param SlideCollectionFactory $slideCollectionFactory Slide Collection.
     * @param CookieManagerInterface $cookieManager          Cookie Manager.
     * @param Page                   $page                   Page.
     * @param FilterProvider         $filterProvider         FilterProvider.
     * @param BlockFactory           $blockFactory           BlockFactory.
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PollCollectionFactory $pollCollectionFactory,
        AnswerCollectionFactory $answerCollectionFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Page $page,
        FilterProvider $filterProvider,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_pollCollectionFactory = $pollCollectionFactory;
        $this->_answerCollectionFactory = $answerCollectionFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_page = $page;
        $this->_storeManager = $context->getStoreManager();
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Check if there is valid group.
     *
     * @return bool || collection
     */
    public function hasValidPoll()
    {
        return is_object($this->_getCollection());
    }

    /**
     * Get collection.
     *
     * @return collection
     */
    public function _getCollection()
    {

        // $code = $this->getCode();
        // $isGlobal = $this->getGlobal();
        // $scope = $this->getScope();

        $pollId = $this->getPollId();

        $pollCollection = $this->_pollCollectionFactory->create()
            ->addEnabledFilter('1');
        if (!$this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore()->getId();
            $pollCollection->addStoreFilter($storeId);
        }
        if ($pollId) {
            $pollCollection->addPollIdFilter($pollId);
        };
        // $pollCollection->addAnswerSortFilter();
        
        return $pollCollection;
    }

    /**
     * Get Answer collection.
     *
     * @param int $pollId Poll ID.
     *
     * @return Answer collection
     */
    public function getAnswers($pollId)
    {
        $answerCollection = $this->_answerCollectionFactory->create()
            ->addPollFilter($pollId)
            ->addAnswerSortFilter('ASC');

        return $answerCollection;
    }

    /**
     * Get form action URL for POST request
     *
     * @return string
     */
    public function getFormAction()
    {
            // companymodule is given in routes.xml
            // controller_name is folder name inside controller folder
            // action is php file name inside above controller_name folder

        return 'devester_poll/Form/Submit';
        // here controller_name is index, action is booking
    }

    public function getQuestionsHtml()
    {
    	$block = $this->getLayout()->createBlock('Devester\Poll\Block\Poll')->setTemplate('Devester_Poll::poll/questions.phtml')->toHtml();
        return $block;
    }

    public function getAnswersHtml()
    {
    	$block = $this->getLayout()->createBlock('Devester\Poll\Block\Poll')->setTemplate('Devester_Poll::poll/answers.phtml')->toHtml();
        return $block;
    }

    /**
     * Return false if cookie is incorrect. Return true if cookie has pollID
     *
     * @return True|False
     */
    public function hasVoted($pollId)
    {
    	if (!$this->cookieManager->getCookie('devester_submitted_polls')) {
    		// If Cookie does not exist return false
    		return false;
    	} else {

    		$varArr = $this->cookieManager->getCookie('devester_submitted_polls');
    		$varDec = json_decode($varArr, true);
       		foreach ($varDec as $cookiePoll) {
       			if ($cookiePoll == $pollId) {
    			return true;
       			}
       		}
       		// If Cookie does exitst but not with Correct pollID
    		return false;
    	}
    	// Just in case
		return false;
    }
}