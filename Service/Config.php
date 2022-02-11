<?php

/**
 * Commercers Vietnam
 */
namespace Commercers\AutoDelete\Service;

class Config {
    
    const DATE_DELETE_FOLDER = 'auto_delete/delte/set_date_delete_folder';
    const DELETE_FILE_SIZE = 'auto_delete/delte/set_size_delete_file';
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    public function getStoreCode($storeId = null){
        $this->_storeManager->getStore($storeId)->getStoreCode();
    }

    public function getDateDeleteFolder($storeId = null){
        
        $dateDeleteFolder  = $this->_scopeConfig->getValue(self::DATE_DELETE_FOLDER, $this->_storeScope, $this->getStoreCode($storeId));
        return $dateDeleteFolder;
        
    }
    
    public function getSizeFileDelete($storeId = null){
        
        $deleteFileSize  = $this->_scopeConfig->getValue(self::DELETE_FILE_SIZE, $this->_storeScope, $this->getStoreCode($storeId));
        return $deleteFileSize;
        
    }
    
}