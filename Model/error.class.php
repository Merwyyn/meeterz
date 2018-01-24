<?php
/**
 * Description of error
 *
 * @author Rayan
 */
class ErrorType {
    protected $_errors;
    protected $_errorsText;
    protected $_inputError;
    
    public function __construct(){
        $this->_errorsText="";
        $this->_errors=false;
        $this->_inputError=[];
    }
    public function addErrors($text){
        if ($this->_errorsText!="")
        {
            $this->_errorsText.="<br/>";
        }
        $this->_errorsText.=$text;
    }
    public function addInput($i){
        $this->_inputError[]=$i;
    }
    public function haveErrors() {
        return $this->_errors;
    }
    public function getErrorsText() {
        return $this->_errorsText;
    }
    public function getInputError() {
        return $this->_inputError;
    }
    public function setErrors($errors) {
        $this->_errors = $errors;
    }
    public function setErrorsText($errorsText) {
        $this->_errorsText = $errorsText;
    }
    public function setInputError($inputError) {
        $this->_inputError = $inputError;
    }
}
