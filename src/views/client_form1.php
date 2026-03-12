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
        .btn-info { background: #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2c3e50; color: #fff; }
        tr:hover { background: #f9f9f9; }
        .actions { margin-bottom: 20px; }
        .form-section { display: flex; gap: 30px; flex-wrap: wrap; }
        .form-panel { flex: 1; min-width: 300px; }
        .list-panel { flex: 1; min-width: 300px; }
        
        /* Message d'erreur */
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-box h3 {
            margin: 0 0 10px 0;
            color: #721c24;
        }
        .error-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .error-box li {
            margin: 5px 0;
        }
        .warning-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 13px;
        }
        .delete-btn {
            padding: 5px 10px;
        }
        .stats-mini {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            font-size: 12px;
        }
        .stat-badge {
            background: #3498db;
            color: #fff;
            padding: 3px 8px;
            border-radius: 3px;
        }
        .stat-badge.warning {
            background: #f39c12;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Clients</h1>
        <div class="actions">
            <a href="index.php?route=invoices" class="btn">Retour Tableau de Bord</a>
            <a href="index.php?route=client_history" class="btn btn-info">📋 Historique Client</a>
        </div>
        
        <?php if(isset($error) && $error['type'] === 'foreign_key'): ?>
        <div class="error-box">
            <h3>⚠️ <?php echo $error['message']; ?></h3>
            <p>
                <strong><?php echo htmlspecialchars($error['clientName']); ?></strong> ne peut pas être supprimé car il a 
                <strong><?php echo $error['invoiceCount']; ?> facture(s)</strong> associée(s).
            </p>
            
            <?php if(!empty($error['invoices'])): ?>
            <p><strong>Dernières factures :</strong></p>
            <ul>
                <?php foreach($error['invoices'] as $inv): ?>
                    <li>
                        Facture #<?php echo str_pad($inv['id'], 6, '0', STR_PAD_LEFT); ?> 
                        - <?php echo date('d/m/Y', strtotime($inv['invoice_date'])); ?> 
                        - <?php echo number_format($inv['total_ttc'], 3, ',', ' '); ?> TND
                        <?php if($inv['mileage'] > 0): ?>
                            - KM: <?php echo number_format($inv['mileage'], 0, ',', ' '); ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if($error['invoiceCount'] > 5): ?>
                <p><em>... et <?php echo $error['invoiceCount'] - 5; ?> autre(s) facture(s)</em></p>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if($error['totalSpent'] > 0): ?>
            <p><strong>Montant total dépensé :</strong> <?php echo number_format($error['totalSpent'], 3, ',', ' '); ?> TND</p>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>💡 Solution :</strong> Vous ne pouvez pas supprimer un client qui a des factures. 
                Vous pouvez cependant :
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Le <strong>modifier</strong> pour mettre à jour ses informations</li>
                    <li>Consulter son <strong>historique complet</strong> via le menu "Historique Client"</li>
                    <li>Archiver ses coordonnées en ajoutant "(ARCHIVÉ)" dans son nom</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
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
                            <th>Factures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($clients)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Aucun client enregistré</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $c): 
                                $invoiceCount = $this->model->getInvoiceCount($c['id']);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['name']); ?></td>
                                <td><?php echo htmlspecialchars($c['car_model'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                                <td style="text-align: center;">
                                    <?php if($invoiceCount > 0): ?>
                                        <span class="stat-badge warning">
                                            📄 <?php echo $invoiceCount; ?>
                                        </span>
                                        <br>
                                        <small style="color: #777; font-size: 10px;">
                                            <?php echo number_format($this->model->getTotalSpent($c['id']), 0, ',', ' '); ?> TND
                                        </small>
                                    <?php else: ?>
                                        <span class="stat-badge" style="background: #2ecc71;">
                                            ✓ Aucun
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?route=clients_edit&id=<?php echo $c['id']; ?>" class="btn btn-warning" style="padding:5px 10px;">Mod</a>
                                    <a href="index.php?route=client_history&client_id=<?php echo $c['id']; ?>" class="btn btn-info" style="padding:5px 10px;">📋</a>
                                    <a href="index.php?route=clients_delete&id=<?php echo $c['id']; ?>" 
                                       class="btn btn-danger delete-btn" 
                                       <?php if($invoiceCount > 0): ?>
                                           onclick="return confirm('⚠️ Ce client a <?php echo $invoiceCount; ?> facture(s). Voulez-vous vraiment essayer de le supprimer ?')"
                                       <?php else: ?>
                                           onclick="return confirm('Supprimer ce client ?')"
                                       <?php endif; ?>
                                    >Sup</a>
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