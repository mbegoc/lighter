<!-- the content template -->
<?=$this->view->getMessages();?>
<div style='background-color:#ccc;border:1px solid black;'>
<h1><?=$this->data->getTitle();?></h1>
<p><?=$this->data->getContent();?></p>
</div>