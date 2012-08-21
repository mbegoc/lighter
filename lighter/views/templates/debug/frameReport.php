<div class="debug <?= $classes ?>">
    <div class="content">
        <p class="origin"><a href="<?= $origin ?>">Page d'origine</a></p>
        <p>Heure d'exécution: <?= $time ?></p>
        <? foreach ($messages as $title => $section) : ?>
            <h2><?= $title ?></h2>
            <table>
                <thead><tr><td>Type</td><td>Titre</td><td>Contenu</td><td>Fichier</td><td>Ligne</td></tr></thead>
                <tbody>
                    <? $highlighted = false ?>
                    <? foreach($section as $message) : ?>
                        <tr<?= $highlighted ? ' class="highlighted"' : ''; $highlighted = !$highlighted ?>>
                            <td><?= $message['type'] ?></td>
                            <td><?= $message['title'] ?></td>
                            <td><?= $message['content'] ?></td>
                            <td><?= $message['file'] ?></td>
                            <td><?= $message['line'] ?></td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        <? endforeach; ?>
    </div>
</div>