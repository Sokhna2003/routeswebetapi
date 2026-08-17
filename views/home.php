<?php
ob_start();
?>
<div class="bg-white rounded-2xl shadow p-8 space-y-3">
    <h2 class="text-xl font-bold">Exercice guidé</h2>
    <p class="text-slate-600">Complétez les TODO dans <code class="bg-slate-100 px-1 rounded">routes/</code>,
        le contrôleur API et <code class="bg-slate-100 px-1 rounded">public/js/books.js</code>.</p>
    <a href="<?= htmlspecialchars(($base ?? '') . '/books') ?>"
       class="inline-block bg-indigo-600 text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-indigo-700">
        Aller au catalogue
    </a>
</div>
<?php
$contenu = ob_get_clean();
include __DIR__ . '/layout.php';
