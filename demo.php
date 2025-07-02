<?php
require_once 'GreekEmail.php';
use GreekEmail\EmailTemplateBuilder;

$output = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name']  ?? ''),
        'gender'     => $_POST['gender'] ?? 'm',
        'email'      => trim($_POST['email'] ?? ''),
    ];

    $template = $_POST['template'] ?? '';
    $builder  = new EmailTemplateBuilder($template);
    $output   = $builder->render($data);
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Δημιουργία Email</title>
    <style>
        body {font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: #f9fafb; margin: 0; padding: 2rem; color: #1f2937;}
        h1   {text-align: center; margin-bottom: 1rem;}
        form {max-width: 720px; margin: auto; background: #ffffff; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05);}        
        label{display: block; margin-top: 1rem;}
        input, textarea, select {width: 100%; padding: 0.6rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 1rem;}
        button {margin-top: 1.5rem; padding: 0.8rem 1.5rem; background: #2563eb; color: #ffffff; border: none; border-radius: 0.5rem; font-size: 1rem; cursor: pointer;}
        pre {white-space: pre-wrap; background: #f3f4f6; padding: 1rem; border-radius: 0.75rem; margin-top: 2rem;}
    </style>
</head>
<body>
    <h1>Φόρμα Δημιουργίας Email</h1>
    <form method="post">
        <label>Όνομα:
            <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
        </label>
        <label>Επώνυμο:
            <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
        </label>
        <label>Φύλο:
            <select name="gender">
                <option value="m" <?= (($_POST['gender'] ?? '') === 'm') ? 'selected' : '' ?>>Άνδρας</option>
                <option value="f" <?= (($_POST['gender'] ?? '') === 'f') ? 'selected' : '' ?>>Γυναίκα</option>
                <option value="n" <?= (($_POST['gender'] ?? '') === 'n') ? 'selected' : '' ?>>Ουδέτερο</option>
            </select>
        </label>
        <label>Email παραλήπτη:
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </label>
        <label>Template:
            <textarea name="template" rows="6"><?= htmlspecialchars($_POST['template'] ?? "Αγαπητέ {{first_name_voc}},\nΤο αίτημα {{last_name_gen}} εγκρίθηκε.") ?></textarea>
        </label>
        <button type="submit">Δημιουργία</button>
    </form>

    <?php if ($output): ?>
        <h2 style="text-align:center; margin-top: 3rem;">Προεπισκόπηση</h2>
        <pre><?= htmlspecialchars($output) ?></pre>
    <?php endif; ?>
</body>
</html>
