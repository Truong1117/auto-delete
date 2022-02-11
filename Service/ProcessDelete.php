<?php
/**
 * Commercers Vietnam
 */
namespace Commercers\AutoDelete\Service;

class ProcessDelete
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
        //current date
        $date = date('Y-m-d');  

        $getDateDeleteFolder = $this->_config->getDateDeleteFolder($storeId); 
        if($getDateDeleteFolder != ""){
            $splitDateDeleteFolder = $this->splitDeleteFolder($getDateDeleteFolder,$root);
            //process delete folder with date
            if(count($splitDateDeleteFolder) > 0){
                foreach($splitDateDeleteFolder as $value){
                    $allFileInFolder[] = $this->getFileInFolderToDelete($value['pathFolder']);
                }
                // echo "<pre>";print_r($allFileInFolder);exit;
                //delete file in folder
                if(count($allFileInFolder) > 0){
                    foreach ($allFileInFolder as $value) {
                        if($value != ""){
                            foreach ($value as $values) {
                                $getAllFileInFolder[] = $values;
                            }
                        }
                    }
                }
                if(isset($getAllFileInFolder)){
                    $timeOfAllFile = $this->getTimeFileInFolder($getAllFileInFolder);
                        $this->deleteFiles($timeOfAllFile);
                }
            }
        }

        //delete file if it over size
        $getSizeFileDelete = $this->_config->getSizeFileDelete($storeId);
        if($getSizeFileDelete != ""){
            $splitSizeDeleteFolder = $this->splitDeleteFolder($getSizeFileDelete,$root);
            $getSizeAllFile = $this->getSizeAllFile($splitSizeDeleteFolder);
            
            foreach($splitSizeDeleteFolder as $value){
                foreach($getSizeAllFile as $sizeFile){
                    $conditionDate = $value['date'];
                    if($conditionDate > 0){
                        $datediff = abs(strtotime($date) - strtotime($sizeFile['time']));
                        $calculateDate = floor($datediff / (60*60*24));
                        if($calculateDate >= $conditionDate){
                            if($value['pathFolder'] == $sizeFile['pathFolder']){
                                if($sizeFile['size'] > $value['size']){
                                    $this->_file->deleteFile($sizeFile['pathFolder']);
                                }
                            }
                        }
                    }else{
                        if($value['pathFolder'] == $sizeFile['pathFolder']){
                            if($sizeFile['size'] >= $value['size']){
                                $this->_file->deleteFile($sizeFile['pathFolder']);
                            }
                        }
                    }
                }
            }
        }
    }

    public function deleteFiles($timeOfAllFile){
        $root = $this->_dir->getPath('var');
        $storeId = $this->_config->getStoreId();

        $getDateDeleteFolder = $this->_config->getDateDeleteFolder($storeId); 
        if($getDateDeleteFolder != ""){
            $splitDateDeleteFolder = $this->splitDeleteFolder($getDateDeleteFolder,$root);
            //current date
            $date = date('Y-m-d');
            foreach($splitDateDeleteFolder as $value){
                $conditionDate = $value['date'];
                foreach($timeOfAllFile as $file){
                    if(isset($file['pathFolder'])){
                        if(file_exists($file['pathFolder'])){
                            $datediff = abs(strtotime($date) - strtotime($file['time']));
                            $calculateDate = floor($datediff / (60*60*24));
                            if($calculateDate >= $conditionDate){
                                if(is_dir($file['pathFolder']))
                                continue;

                                $this->_file->deleteFile($file['pathFolder']);
                            }
                        }
                    }else{
                        $this->deleteFiles($file);
                    }
                }
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

    public function getFileInFolderToDelete($pathFolder){
        $iterator = new \FilesystemIterator($pathFolder);
        $data = array();
        if ($iterator->getPathName() != '') {

            /** @var \FilesystemIterator $file */
            foreach ($iterator as $file) {
                if($file->getPathname() != ""){
                    //$data[] = $file->getPathname();
                    if(is_dir($file->getPathname())){
                        if ($this->getFileInFolderToDelete($file->getPathname()) != "") {
                            $data[] = $this->getFileInFolderToDelete($file->getPathname());
                        }else{
                            if(is_dir($file->getPathname())){
                                rmdir($file->getPathname(), true);
                            }
                        }
                    }
                    if(is_file($file->getPathname())){
                        $data[] = $file->getPathname();
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

    public function getTimeFileInFolder($allFileInFolder){
        $result = array();
        foreach ($allFileInFolder as $value) {
            if(!is_array($value)){
                if(file_exists($value)) {
                    $result[] = [
                        'pathFolder'=> $value,
                        'time' => date ("Y-m-d", filemtime($value)),
                    ];
                }
            }
            if(is_array($value))
            $result[] = $this->getTimeFileInFolder($value);

        }
        return $result;
    }

    public function getSizeAllFile($splitSizeDeleteFolder){
        foreach($splitSizeDeleteFolder as $value){
            if(file_exists($value['pathFolder'])) {
                $result[] = [
                    'pathFolder'=> $value['pathFolder'],
                    'time' => date ("Y-m-d", filemtime($value['pathFolder'])),
                    'size' => filesize($value['pathFolder'])
                ];
            }
        }
        return $result;
    }
}