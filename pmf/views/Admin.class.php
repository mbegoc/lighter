<?php
namespace views;


use html\forms\InputCheckBox;

use handlers\Debug;

use html\forms\FormElement;

use html\HtmlHeader;

use dto\Config;

use html\forms\InputText;
use html\forms\Form;

use dto\DataObject;

use helpers\Path;


class Admin extends View {
    const SAVE_OK = 'Your modification has been saved.';
    /**
     *
     * @var dto\DataObject
     */
    protected $data = NULL;
    /**
     *
     * @var html\forms\Form
     */
    protected $form = NULL;
    /**
     *
     * @param string $template
     */
    protected $title = 'Administration';


    public function __construct($template){
        parent::__construct('admin/'.$template);
        self::$tplEngine->addObject("pathHelper", Path::getInstance());
        HtmlHeader::getInstance()->addCssFile(Config::getInstance()->getApplicationRelativePath().'include/css/admin.css');
    }


    public function setConfig(Config $data){
        $this->data = $data;

        $this->form = new Form("config");

        $this->form->addElement(new InputText('path', _('Relative path'), 200, $this->data->getApplicationRelativePath()));
        $this->form->addElement(new InputText('rootPath', _('Root path'), 200, $this->data->getApplicationRootPath()));
        $this->form->addElement(new InputText('controllerClass', _('Default controller class'), 200, $this->data->getControllerClass()));
        $this->form->addElement(new InputText('controllerMethod', _('Default controller method'), 200, $this->data->getControllerMethod()));
        $this->form->addElement(new InputText('debugConfPath', _('Debug configuration path'), 200, $this->data->getDebugConfigPath()));
        $this->form->addElement(new InputCheckBox('debugActive', _('Is debug active ?'), $this->data->isDebugActive()));
        $this->form->addElement(new InputText('mainViewClass', _('Main view class'), 200, $this->data->getMainViewName()));
        $this->form->addElement(new InputText('tplExtension', _('Template files extension'), 200, $this->data->getTemplateExt()));
        $this->form->addElement(new InputText('tplPath', _('Template path'), 200, $this->data->getTemplatePath()));
        $this->form->addElement(new InputCheckBox('indexFile', _('Is index file needed in the url ?'), $this->data->needIndexFile()));
    }


    public function isUpdated(){
        if($this->form->isPosted()){
            $values = $this->form->getValues();

            $this->data->setApplicationPath($values['rootPath'], $values['path']);
            $this->data->setDefaultController($values['controllerClass'], $values['controllerMethod']);
            $this->data->setDebugData($values['debugConfPath'], $values['debugActive']);
            $this->data->setDefaultView($values['mainViewClass']);
            $this->data->setTemplateData($values['tplPath'], $values['tplExtension']);
            $this->data->setIndexFile($values['indexFile']);

            return true;
        }else{
            return false;
        }

    }


    public function getForm(){
        return $this->form;
    }

}