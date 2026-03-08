<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Types de Réparation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 15px; background: #3498db; color: #fff; border: none; cursor: pointer; text-decoration: none; display: inline-block; border-radius: 4px; }
        .btn-danger { background: #e74c3c; }
        .btn-warning { background: #f1c40f; color: #000; }
        .btn-success { background: #2ecc71; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #ecf0f1; }
        tr:hover { background: #f9f9f9; }
        .text-right { text-align: right; }
        .actions { margin-bottom: 20px; }
        .form-section { display: flex; gap: 30px; flex-wrap: wrap; }
        .form-panel { flex: 1; min-width: 300px; }
        .list-panel { flex: 1; min-width: 300px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Types de Réparation</h1>
        <div class="actions">
            <a href="index.php?route=invoices" class="btn">Retour Tableau de Bord</a>
        </div>
        
        <div class="form-section">
            <div class="form-panel">
                <h2><?php echo $type ? 'Modifier Type' : 'Nouveau Type'; ?></h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Nom du Type</label>
                        <input type="text" name="name" value="<?php echo $type ? htmlspecialchars($type['name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prix Défaut (TND)</label>
                        <input type="number" step="0.001" name="default_price" value="<?php echo $type ? $type['default_price'] : 0; ?>">
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo $type ? 'Modifier' : 'Ajouter'; ?></button>
                    <?php if($type): ?>
                        <a href="index.php?route=types_create" class="btn btn-danger">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="list-panel">
                <h2>Liste des Types</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th class="text-right">Prix Défaut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($types)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center;">Aucun type enregistré</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($types as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['name']); ?></td>
                                <td class="text-right"><?php echo number_format($t['default_price'], 3, ',', ' '); ?></td>
                                <td>
                                    <a href="index.php?route=types_edit&id=<?php echo $t['id']; ?>" class="btn btn-warning" style="padding:5px 10px;">Mod</a>
                                    <a href="index.php?route=types_delete&id=<?php echo $t['id']; ?>" class="btn btn-danger" style="padding:5px 10px;" onclick="return confirm('Supprimer ce type ?')">Sup</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>