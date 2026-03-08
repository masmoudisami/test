<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Clients</title>
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
        .actions { margin-bottom: 20px; }
        .form-section { display: flex; gap: 30px; flex-wrap: wrap; }
        .form-panel { flex: 1; min-width: 300px; }
        .list-panel { flex: 1; min-width: 300px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Clients</h1>
        <div class="actions">
            <a href="index.php?route=invoices" class="btn">Retour Tableau de Bord</a>
        </div>
        
        <div class="form-section">
            <div class="form-panel">
                <h2><?php echo $client ? 'Modifier Client' : 'Nouveau Client'; ?></h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="name" value="<?php echo $client ? htmlspecialchars($client['name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Modèle Voiture</label>
                        <input type="text" name="car_model" value="<?php echo $client ? htmlspecialchars($client['car_model']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="phone" value="<?php echo $client ? htmlspecialchars($client['phone']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="address" value="<?php echo $client ? htmlspecialchars($client['address']) : ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo $client ? 'Modifier' : 'Ajouter'; ?></button>
                    <?php if($client): ?>
                        <a href="index.php?route=clients_create" class="btn btn-danger">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="list-panel">
                <h2>Liste des Clients</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Modèle</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($clients)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">Aucun client enregistré</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['name']); ?></td>
                                <td><?php echo htmlspecialchars($c['car_model'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                                <td>
                                    <a href="index.php?route=clients_edit&id=<?php echo $c['id']; ?>" class="btn btn-warning" style="padding:5px 10px;">Mod</a>
                                    <a href="index.php?route=clients_delete&id=<?php echo $c['id']; ?>" class="btn btn-danger" style="padding:5px 10px;" onclick="return confirm('Supprimer ce client ?')">Sup</a>
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