<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/layout.php';

// All projects, newest first, with the number of issues in each one
$projects = db()->query(
    'SELECT p.id, p.name, p.description, COUNT(i.id) AS issue_count
     FROM projects p
     LEFT JOIN issues i ON i.project_id = p.id
     GROUP BY p.id
     ORDER BY p.created_at DESC, p.id DESC'
)->fetchAll();

page_header('Projects');
?>

<div class="page-title">
    <h1>Projects</h1>
    <a href="/project_form.php" class="button">+ New project</a>
</div>

<?php if (count($projects) === 0): ?>
    <p class="empty">No projects yet. Create the first one.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Issues</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><a href="/project_view.php?id=<?= (int) $project['id'] ?>"><?= e($project['name']) ?></a></td>
                <td><?= e($project['description']) ?></td>
                <td><?= (int) $project['issue_count'] ?></td>
                <td class="actions">
                    <a href="/project_form.php?id=<?= (int) $project['id'] ?>">Edit</a>
                    <form method="post" action="/project_delete.php"
                          onsubmit="return confirm('Delete this project and all its issues?');">
                        <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php page_footer(); ?>