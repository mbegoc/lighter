<h1><?=$this->view->getTitle();?></h1>
<?=$this->view->getMessages();?>
<?=$this->view->getList()->toHTML();?>
<a href='./emptyForm'>Création</a>
<a href='./'>Retour</a>
