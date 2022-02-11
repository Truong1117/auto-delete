<?php
/**
 * Commercers Vietnam
 */
namespace Commercers\AutoDelete\Service;

class ProcessDeleteFolder
{
    protected $_file;
    protected $_dir;
    protected $_config;
    
    public function __Construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Commercers\AutoDelete\Service\Config $config
    ){
        $this->_file = $file;
        $this->_config = $config;
        $this->_dir = $dir;
    }

    public function execute(){
        $root = $this->_dir->getPath('var');
        $storeId = $this->_config->getStoreId();

        $getDateDeleteFolder = $this->_config->getDateDeleteFolder($storeId); 
        if($getDateDeleteFolder != ""){
            $splitDateDeleteFolder = $this->splitDeleteFolder($getDateDeleteFolder,$root);

            //process delete folder with date
            foreach($splitDateDeleteFolder as $value){
                $this->deleteFolders($value['pathFolder']);
            }
        }
    }

    public function deleteFolders($pathFolder){
    	if(is_dir($pathFolder)){
    		$iterator = new \FilesystemIterator($pathFolder);
	    	$data = array();
	        if ($iterator->getPathName() != '') {
	            /** @var \FilesystemIterator $file */
	            foreach ($iterator as $file) {
	                if($file->getPathname() != ""){
	                    if(is_dir($file->getPathname())){
	                        if ($this->deleteFolders($file->getPathname()) != "") {
	                            $data[] = $this->deleteFolders($file->getPathname());
	                        }else{
	                            if(is_dir($file->getPathname())){
	                                rmdir($file->getPathname(), true);
	                            }
	                        }
	                    }
	                }else{
	                    rmdir($pathFolder);
	                }
	            }
	            if(isset($data)){
	                return $data;
	            }
	        }else{
	            rmdir($pathFolder);
	        }
    	}
    }

    public function splitDeleteFolder($getInfoDeleteFolder,$root){
        $getInfoDeleteFolder = preg_split('/\\r\\n|\\r|\\n/',trim($getInfoDeleteFolder));
        $result = array();
        foreach ($getInfoDeleteFolder as $value){
            $attribute = explode('|',trim($value));
            $pathFolder = $root . $attribute[0];
            $size = isset($attribute[2])?$attribute[2]:"";
            if (file_exists($pathFolder)) {
                $result[] = [
                    'pathFolder'=> $pathFolder,
                    'time' => date ("Y-m-d", filemtime($pathFolder)),
                    'date' => $attribute[1],
                    'size' => $size
                ];
            }
        }
        return $result;
    }
}