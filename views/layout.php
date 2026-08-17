<?php
$titre = $titre ?? 'Atelier 22';
$base = $base ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
    <header class="bg-indigo-700 text-white">
        <div class="max-w-4xl mx-auto px-6 py-10">
            <p class="text-indigo-200 text-sm font-semibold uppercase tracking-wider">Atelier 22 — Exercice</p>
            <h1 class="text-3xl font-bold mt-1"><?= htmlspecialchars($titre) ?></h1>
            <nav class="mt-4 flex gap-3 text-sm">
                <a href="<?= htmlspecialchars($base) ?>/" class="bg-white/10 px-3 py-1.5 rounded-lg hover:bg-white/20">Accueil</a>
                <a href="<?= htmlspecialchars($base) ?>/books" class="bg-white/10 px-3 py-1.5 rounded-lg hover:bg-white/20">Livres</a>
            </nav>
        </div>
    </header>
    <main class="max-w-4xl mx-auto px-6 py-10">
        <?= $contenu ?? '' ?>
    </main>
</body>
</html>
