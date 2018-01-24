<?php
define("PICTURES", 0);
define("FILES", 1);
class Upload extends Error{
	private $_paths;
	public function __construct($data, $type, $from)
	{
        parent::__construct();
		$this->_paths=[];
        switch ($type){
            case PICTURES:
                $this->upload_pictures($data, $from);
                break;
            case FILES:
                //$this->upload_files($data, $from);
                break;
        }
    }
    private function upload_pictures($data, $from)
    {
        global $base_web_view, $keyCamping;
        $extensions_valides=['jpg', 'png', 'jpeg', 'gif', 'bmp'];
        foreach ($data as $image)
		{
            if (empty($image['name'])){ continue; }
            if ($image['error']!=0)
            {
                switch ($image['error'])
                {
                    default: 
                        $this->_errors++;
                        $this->addErrors(ERROR_UPLOAD_WRONG);
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->_errors++; 
                        $this->addErrors(ERROR_UPLOAD_NO_FILE);
                        break;
                    case UPLOAD_ERR_INI_SIZE: 
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->_errors++; 
                        $this->addErrors(ERROR_UPLOAD_TOO_BIG);
                        break;
                    case UPLOAD_ERR_PARTIAL: 
                        $this->_errors++; 
                        $this->addErrors(ERROR_UPLOAD_PROGRESS);
                        break;
                }
                break;
            }
            if ($image['size']>MAX_SIZE_UPLOAD_ONCE)
            {
                $this->_errors++;
                $this->addErrors(ERROR_UPLOAD_TOO_BIG);
                break;
            }
            $extension=strtolower(substr(strrchr($image['name'], '.'),1));
            if (!in_array($extension,$extensions_valides))
            {
                $this->_errors++;
                $this->addErrors(ERROR_UPLOAD_WRONG_FORMAT_FILES);
                break;
            }
            $location=$base_web_view."images/uploaded/".$keyCamping."/".$from."/";
            $name_file=md5(uniqid(rand(), true)).".".$extension;
            if (!mkdir($location, 0777, true) || !move_uploaded_file($image['tmp_name'],$location.$name_file))
            {
                $this->_errors++;
                $this->addErrors(ERROR_UPLOAD_WRONG);
                break;
            }
            $this->_paths[]=$location.$name_file;
        }
        if ($this->_errors)
        {
            $this->cancel();
        }
    }
    private function cancel()
	{
		foreach ($this->_paths as $path)
		{
			if (file_exists($path))
            {
                unlink($path);
            }
		}
	}
    public function getPath()
    {
        return $this->_paths;
    }
}