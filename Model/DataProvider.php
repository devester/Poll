<?php

namespace Devester\Poll\Model;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $rowCollection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Devester\Poll\Model\ResourceModel\Answer\Collection $collection,
        \Devester\Poll\Model\ResourceModel\Answer\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection    = $collection;
        $this->rowCollection = $collectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $collection = $this->rowCollection->create()->setOrder('position', 'ASC');
        $items      = $collection->getItems();
        foreach ($items as $item) {
            $this->loadedData['stores']['answer_poll_container'][] = $item->getData();
        }

        return $this->loadedData;
    }
}