<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Clients</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; }
        
        .btn { 
            padding: 8px 12px; 
            background: #3498db; 
            color: #fff; 
            border: none; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block; 
            border-radius: 4px;
            font-size: 13px;
            transition: background 0.3s;
        }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: #e74c3c; }
        .btn-warning { background: #f1c40f; color: #000; }
        .btn-success { background: #2ecc71; }
        .btn-info { background: #17a2b8; }
        .btn-secondary { background: #95a5a6; }
        
        .main-actions { 
            margin-bottom: 20px; 
            display: flex; 
            gap: 10px; 
            flex-wrap: wrap;
            padding-bottom: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        /* Zone de recherche */
        .search-section {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .search-input {
            flex: 1;
            min-width: 250px;
        }
        .search-input input {
            padding: 10px;
            font-size: 14px;
        }
        .search-buttons {
            display: flex;
            gap: 10px;
        }
        .search-info {
            margin-top: 10px;
            font-size: 13px;
            color: #666;
        }
        .search-info strong {
            color: #2c3e50;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2c3e50; color: #fff; }
        tr:hover { background: #f9f9f9; }
        
        .form-section { display: flex; gap: 30px; flex-wrap: wrap; }
        .form-panel { flex: 1; min-width: 350px; }
        .list-panel { flex: 1; min-width: 450px; }
        
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-box h3 { margin: 0 0 10px 0; color: #721c24; }
        .error-box ul { margin: 10px 0; padding-left: 20px; }
        .error-box li { margin: 5px 0; }
        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 13px;
        }
        
        .table-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .table-actions .btn {
            padding: 5px 10px;
            font-size: 12px;
            min-width: 35px;
            text-align: center;
        }
        .action-group {
            display: flex;
            gap: 3px;
        }
        
        .stats-mini {
            display: flex;
            flex-direction: column;
            gap: 5px;
            font-size: 11px;
        }
        .stat-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        .stat-badge.warning { background: #f39c12; color: #fff; }
        .stat-badge.success { background: #2ecc71; color: #fff; }
        .stat-total { color: #777; font-size: 10px; }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .form-actions .btn {
            padding: 10px 20px;
        }
        
        .highlight {
            background: #fff3cd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Gestion des Clients</h1>
        
        <div class="main-actions">
            <a href="index.php?route=invoices" class="btn btn-secondary">↩️ Tableau de Bord</a>
            <a href="index.php?route=client_history" class="btn btn-info">📋 Historique par Client</a>
            <a href="exports/export_all_clients_csv.php" class="btn btn-success">📊 Export CSV (Tous)</a>
        </div>
        
        <!-- Zone de Recherche -->
        <div class="search-section">
            <h3 style="margin: 0 0 15px 0; color: #2c3e50;">🔍 Rechercher un Client</h3>
            <form method="GET" class="search-form">
                <input type="hidden" name="route" value="clients_create">
                <div class="search-input">
                    <label for="search">Nom, Modèle de voiture ou Téléphone</label>
                    <input type="text" id="search" name="search" placeholder="Ex: Mohamed, Clio, 98..." value="<?php echo htmlspecialchars($search); ?>" autofocus>
                </div>
                <div class="search-buttons">
                    <button type="submit" class="btn btn-info">🔍 Rechercher</button>
                    <a href="index.php?route=clients_create" class="btn btn-secondary">✕ Réinitialiser</a>
                </div>
            </form>
            <div class="search-info">
                <?php if(!empty($search)): ?>
                    <strong><?php echo count($clients); ?></strong> client(s) trouvé(s) pour "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <span style="float: right;">
                        <a href="index.php?route=clients_create" style="color: #e74c3c; text-decoration: underline;">Voir tous les clients</a>
                    </span>
                <?php else: ?>
                    <strong><?php echo count($clients); ?></strong> client(s) enregistré(s)
                <?php endif; ?>
            </div>
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
                        #<?php echo str_pad($inv['id'], 6, '0', STR_PAD_LEFT); ?> 
                        - <?php echo date('d/m/Y', strtotime($inv['invoice_date'])); ?> 
                        - <?php echo number_format($inv['total_ttc'], 3, ',', ' '); ?> TND
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>💡 Solutions :</strong> Modifier le client | Consulter l'historique | Archiver (ajouter "(ARCHIVÉ)" au nom)
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-section">
            <div class="form-panel">
                <h2><?php echo $client ? '✏️ Modifier' : '➕ Nouveau Client'; ?></h2>
                <form method="POST">
                    <div class="form-group">
                        <label>📛 Nom *</label>
                        <input type="text" name="name" value="<?php echo $client ? htmlspecialchars($client['name']) : ''; ?>" required placeholder="Nom et prénom">
                    </div>
                    <div class="form-group">
                        <label>🚗 Modèle Voiture</label>
                        <input type="text" name="car_model" value="<?php echo $client ? htmlspecialchars($client['car_model']) : ''; ?>" placeholder="Ex: Clio 4, Peugeot 308...">
                    </div>
                    <div class="form-group">
                        <label>📞 Téléphone</label>
                        <input type="text" name="phone" value="<?php echo $client ? htmlspecialchars($client['phone']) : ''; ?>" placeholder="+216 XX XXX XXX">
                    </div>
                    <div class="form-group">
                        <label>📍 Adresse</label>
                        <input type="text" name="address" value="<?php echo $client ? htmlspecialchars($client['address']) : ''; ?>" placeholder="Adresse complète">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo $client ? '✓ Modifier' : '✓ Enregistrer'; ?>
                        </button>
                        <?php if($client): ?>
                            <a href="index.php?route=clients_create" class="btn btn-danger">✕ Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="list-panel">
                <h2>📂 Liste des Clients <?php if(!empty($search)): ?> <span style="font-size: 14px; color: #666;">(Filtrée)</span><?php endif; ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%;">Nom</th>
                            <th style="width: 20%;">Modèle</th>
                            <th style="width: 15%;">Téléphone</th>
                            <th style="width: 15%;">Activité</th>
                            <th style="width: 25%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($clients)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px; color: #777;">
                                📭 Aucun client trouvé pour cette recherche
                                <br>
                                <a href="index.php?route=clients_create" class="btn btn-secondary" style="margin-top: 10px;">Voir tous les clients</a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $c): 
                                $invoiceCount = $this->model->getInvoiceCount($c['id']);
                                $totalSpent = $this->model->getTotalSpent($c['id']);
                                $highlight = (!empty($search) && (stripos($c['name'], $search) !== false || stripos($c['car_model'] ?? '', $search) !== false || stripos($c['phone'] ?? '', $search) !== false)) ? ' class="highlight"' : '';
                            ?>
                            <tr<?php echo $highlight; ?>>
                                <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['car_model'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($c['phone'] ?? '—'); ?></td>
                                <td>
                                    <div class="stats-mini">
                                        <?php if($invoiceCount > 0): ?>
                                            <span class="stat-badge warning">📄 <?php echo $invoiceCount; ?></span>
                                            <span class="stat-total"><?php echo number_format($totalSpent, 0, ',', ' '); ?> TND</span>
                                        <?php else: ?>
                                            <span class="stat-badge success">✓ Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <div class="action-group">
                                            <a href="index.php?route=clients_edit&id=<?php echo $c['id']; ?>" 
                                               class="btn btn-warning" 
                                               title="Modifier">✏️</a>
                                            <a href="index.php?route=client_history&client_id=<?php echo $c['id']; ?>" 
                                               class="btn btn-info" 
                                               title="Voir Historique">👁️</a>
                                        </div>
                                        <div class="action-group">
                                            <a href="index.php?route=invoices_create&client_id=<?php echo $c['id']; ?>" 
                                               class="btn btn-success" 
                                               title="Nouvelle Facture">📄</a>
                                            <a href="index.php?route=clients_delete&id=<?php echo $c['id']; ?>" 
                                               class="btn btn-danger" 
                                               title="Supprimer"
                                               <?php if($invoiceCount > 0): ?>
                                                   onclick="return confirm('⚠️ Ce client a <?php echo $invoiceCount; ?> facture(s).\n\nÊtes-vous sûr de vouloir le supprimer ?')"
                                               <?php else: ?>
                                                   onclick="return confirm('Supprimer ce client ?')"
                                               <?php endif; ?>
                                            >🗑️</a>
                                        </div>
                                    </div>
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