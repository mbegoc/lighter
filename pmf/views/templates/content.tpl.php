<!-- the content template -->
<?php echo $this->body->getMessages(); ?>
<div style='background-color:#ccc;border:1px solid black;'>
<h1><?php echo $this->data->getTitle(); ?></h1>
<p><?php echo $this->data->getContent(); ?></p>
</div>