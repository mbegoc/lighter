<?php
namespace lighter\views;


use lighter\models\Menu;
use lighter\models\Config;

use lighter\handlers\Debug;

use lighter\helpers\html\HtmlHeader;
use lighter\helpers\html\tables\EditTable;
use lighter\helpers\html\forms\FormElement;
use lighter\helpers\html\forms\InputCheckBox;
use lighter\helpers\html\forms\InputText;
use lighter\helpers\html\forms\Form;

use lighter\helpers\Path;


/**
 * Admin View
 *
 * @name Admin
 * @package lighter
 * @subpackage views
 * @see lighter\views\PageBody
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Admin extends PageBody {
    const SAVE_OK = 'Your modification has been saved.';
    /**
     * the data source to use
     *
     * @var lighter\models\DataAccessor
     */
    protected $data = NULL;
    /**
     * the form to edit the data
     *
     * @var lighter\helpers\html\forms\Form
     */
    protected $form = NULL;
    /**
     * the table to display the data sets (lists)
     *
     * @var html\tables\EditTable
     */
    protected $table = NULL;
    /**
     * the title of the page
     *
     * @param string $template
     */
    protected $title = 'Administration';


    /**
     * default constructor
     *
     * @param string $template
     */
    public function __construct($template){
        parent::__construct('main', 'admin/'.$template);
        self::$tplEngine->addObject("pathHelper", Path::getInstance());
        HtmlHeader::getInstance()->addCssFile(
            Config::getInstance()->getApplicationRelativePath().'include/css/admin.css'
        );
    }


    /**
     * return the formto edit the current data
     *
     * @return lighter\helpers\html\forms\Form
     */
    public function getForm(){
        return $this->form;
    }


    /**
     * return data set as a HTML table
     */
    public function getList(){
        return $this->table;
    }


    /**
     * say if data needs to be saved
     *
     * @return boolean
     */
    public function isDataUpdated(){
        return $this->form->isPosted();
    }


    /**
     * return the values of the form
     *
     * @return array
     */
    public function getData(){
        return $this->form->getValues();
    }


    /**
     *
     *
     * @param unknown_type $data
     */
    public function initConfig(Config $data){
        $this->setTitle('Configuration');
        $this->data = $data;
        $this->form = new Form("config");
        $this->form->addElement(new InputText(
            'path', _('Relative path'), 200, $this->data->getApplicationRelativePath())
        );
        $this->form->addElement(new InputText(
            'rootPath', _('Root path'), 200, $this->data->getApplicationRootPath())
        );
        $this->form->addElement(new InputText(
            'controllerClass', _('Default controller class'), 200, $this->data->getControllerClass())
        );
        $this->form->addElement(new InputText(
            'controllerMethod', _('Default controller method'), 200, $this->data->getControllerMethod())
        );
        $this->form->addElement(new InputText(
            'debugConfPath', _('Debug configuration path'), 200, $this->data->getDebugConfigPath())
        );
        $this->form->addElement(new InputCheckBox(
            'debugActive', _('Is debug active ?'), $this->data->isDebugActive())
        );
        $this->form->addElement(new InputCheckBox(
            'indexFile', _('Is index file needed in the url ?'), $this->data->needIndexFile())
        );
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
        $this->table->addTool('Detail', Path::getInstance()->prefixURI('Admin/menu/'));

        while($menu->next()){
            $this->table->addRow($menu);
        }
    }


    public function initMenu(Menu $data){
        $this->setTitle('Menu element');
        $this->data = $data;
        $this->form = new Form("menu");
        $this->form->addElement(new InputText(
            'title', _('Title'), 200, $this->data->getTitle())
        );
        $this->form->addElement(new InputText(
            'short', _('URL name'), 200, $this->data->getShort())
        );
        $this->form->addElement(new InputText(
            'controllerClass', _('Controller class'), 200, $this->data->getController())
        );
        $this->form->addElement(new InputText(
            'controllerMethod', _('Controller method'), 200, $this->data->getControllerMethod())
        );
        $this->form->addElement(new InputText(
            'itemId', _('Item id'), 200, $this->data->getItemId())
        );
        $this->form->addElement(new InputCheckBox(
            'published', _('Published'), $this->data->isPublished())
        );
    }

}
