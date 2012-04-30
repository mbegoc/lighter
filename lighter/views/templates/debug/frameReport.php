<div class="debug">
    <p>Heure d'exécution: <?= $time ?></p>
    <? foreach ($messages as $section) : ?>
        <h2><?= $section ?></h2>
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