<?php

namespace Commercers\AutoDelete\Cron;

class AutoDelete
{
    protected $_processDelete;
    protected $_processDeleteFolder;

    public function __Construct(
        \Commercers\AutoDelete\Service\ProcessDelete $processDelete,
        \Commercers\AutoDelete\Service\ProcessDeleteFolder $processDeleteFolder
    ){
        $this->_processDelete = $processDelete;
        $this->_processDeleteFolder = $processDeleteFolder;
    }

    public function execute(){
        $this->_processDelete->execute();
        $this->_processDeleteFolder->execute();
    }
}