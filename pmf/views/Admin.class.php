<?php
namespace pmf\views;


use pmf\dto\Menu;
use pmf\dto\Config;

use pmf\handlers\Debug;

use pmf\html\HtmlHeader;
use pmf\html\tables\EditTable;
use pmf\html\forms\FormElement;
use pmf\html\forms\InputCheckBox;
use pmf\html\forms\InputText;
use pmf\html\forms\Form;

use pmf\helpers\Path;


class Admin extends PageBody {
    const SAVE_OK = 'Your modification has been saved.';
    /**
     *
     * @var pmf\dto\DataAccessor
     */
    protected $data = NULL;
    /**
     *
     * @var pmf\helpers\html\forms\Form
     */
    protected $form = NULL;
    /**
     *
     * @var html\tables\EditTable
     */
    protected $table = NULL;
    /**
     *
     * @param string $template
     */
    protected $title = 'Administration';


    public function __construct($template){
        parent::__construct('main', 'admin/'.$template);
        self::$tplEngine->addObject("pathHelper", Path::getInstance());
        HtmlHeader::getInstance()->addCssFile(Config::getInstance()->getApplicationRelativePath().'include/css/admin.css');
    }


    public function getForm(){
        return $this->form;
    }


    public function getList(){
        return $this->table;
    }


    public function isUpdated(){
        if($this->form->isPosted()){
            $values = $this->form->getValues();

            switch($this->form->getName()){
                case 'config':
                    $this->fillConfigData($values);
                    break;
                case 'menu':
                    $this->fillMenuData($values);
                    break;
                default:

            }

            return true;
        }else{
            return false;
        }

    }


    public function setConfig(Config $data){
        $this->setTitle('Configuration');

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


    private function fillConfigData(array $values){
        $this->data->setApplicationPath($values['rootPath'], $values['path']);
        $this->data->setDefaultController($values['controllerClass'], $values['controllerMethod']);
        $this->data->setDebugData($values['debugConfPath'], $values['debugActive']);
        $this->data->setDefaultView($values['mainViewClass']);
        $this->data->setTemplateData($values['tplPath'], $values['tplExtension']);
        $this->data->setIndexFile($values['indexFile']);
    }


    public function initMenuList(Menu $menu){
        $this->setTitle('Menu List');
        $this->table = new EditTable("menuList");
        $this->table->addHeaderCell('Title');
        $this->table->addCol('Title');
        $this->table->addHeaderCell('URL name');
        $this->table->addCol('Short');
        $this->table->addHeaderCell('Controller');
        $this->table->addCol('Controller');
        $this->table->addHeaderCell('Method');
        $this->table->addCol('ControllerMethod');
        $this->table->setTool('Detail', Path::getInstance()->prefixURI('Admin/menu/'));

        while($menu->next()){
            $this->table->addRow($menu);
        }
    }


    public function setMenu(Menu $data){
        $this->setTitle('Menu element');

        $this->data = $data;

        $this->form = new Form("menu");

        $this->form->addElement(new InputText('title', _('Title'), 200, $this->data->getTitle()));
        $this->form->addElement(new InputText('short', _('URL name'), 200, $this->data->getShort()));
        $this->form->addElement(new InputText('controllerClass', _('Controller class'), 200, $this->data->getController()));
        $this->form->addElement(new InputText('controllerMethod', _('Controller method'), 200, $this->data->getControllerMethod()));
        $this->form->addElement(new InputText('itemId', _('Item id'), 200, $this->data->getItemId()));
        $this->form->addElement(new InputCheckBox('published', _('Published'), $this->data->isPublished()));
    }


    private function fillMenuData(array $values){
        $this->data->setTitle($values['title']);
        $this->data->setShort($values['short']);
        $this->data->setController($values['controllerClass']);
        $this->data->setControllerMethod($values['controllerMethod']);
        $this->data->setItemId($values['itemId']);
        $this->data->setPublished($values['published']);
    }

}