<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<?= $htmlHeader->display() ?>
<body>
    <div id="content"><?= $view->getMainContent() ?></div>
    <?= isset($debug) ? $debug->getMainContent() : '' ?>
</body>
</html>