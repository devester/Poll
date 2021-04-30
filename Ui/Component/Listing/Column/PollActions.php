<?php
/**
 * @author Jason Sylvester Devester
 * @copyright Copyright © 2019 Devester. All rights reserved.
 * @package Devester/Poll
 */

namespace Devester\Poll\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class PageActions
 */
class PollActions extends Column
{
	/* Url path Edit item */
    const POLL_URL_PATH_EDIT = 'devester_poll/poll/edit';
    /* Url path delete item */
    const POLL_URL_PATH_DELETE = 'devester_poll/poll/delete';

    /**
     * Variable.
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Variable.
     *
     * @var string
     */
    private $editUrl;
    private $deleteUrl;

    /**
     * Construct.
     *
     * @param ContextInterface   $context            Context.
     * @param UiComponentFactory $uiComponentFactory UIComponentFactory.
     * @param UrlInterface       $urlBuilder         UrlBuilder.
     * @param array              $components         Compontents.
     * @param array              $data               Data.
     * @param string             $editUrl            EditURL.
     * @param string             $deleteUrl          DeleteURL.
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::POLL_URL_PATH_EDIT,
        $deleteUrl = self::POLL_URL_PATH_DELETE
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->deleteUrl = $deleteUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource Data Source.
     *
     * @return datasource[]
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['poll_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $this->editUrl,
                            ['poll_id' => $item['poll_id']]
                        ),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            $this->deleteUrl,
                            ['poll_id' => $item['poll_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => sprintf(
                                __('Delete Poll "%s"'),
                                $item['title']
                            ),
                            'message' => sprintf(
                                __('Are you sure you want to delete poll "%s"?'),
                                $item['title']
                            )
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }

}