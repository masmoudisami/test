<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?php echo $invoice['id']; ?></title>
    <style>
        body { font-family: 'Times New Roman', serif; padding: 20px; color: #000; margin: 0; }
        .invoice-container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; }
        .garage-info { flex: 1; }
        .garage-logo { max-width: 150px; max-height: 80px; margin-bottom: 10px; }
        .garage-name { font-size: 18px; font-weight: bold; color: #2c3e50; margin: 5px 0; }
        .garage-details { font-size: 12px; line-height: 1.5; color: #555; }
        .invoice-info { text-align: right; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .invoice-number { font-size: 14px; margin-bottom: 5px; }
        .client-section { margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .client-title { font-weight: bold; margin-bottom: 10px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #2c3e50; padding: 10px; text-align: left; }
        th { background: #2c3e50; color: #fff; }
        .totals { margin-top: 20px; text-align: right; }
        .totals p { margin: 5px 0; }
        .totals .total-ttc { font-size: 18px; font-weight: bold; color: #2c3e50; border-top: 2px solid #2c3e50; padding-top: 10px; margin-top: 10px; }
        .comment-section { margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #777; border-top: 1px solid #ddd; padding-top: 15px; }
        .no-print { margin-top: 20px; text-align: center; padding: 20px; background: #ecf0f1; }
        .btn { padding: 10px 20px; background: #3498db; color: #fff; border: none; cursor: pointer; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; }
        .btn-danger { background: #e74c3c; }
        @media print { 
            .no-print { display: none; } 
            .invoice-container { border: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="garage-info">
                <?php if(file_exists(GARAGE_LOGO)): ?>
                    <img src="<?php echo GARAGE_LOGO; ?>" alt="Logo" class="garage-logo">
                <?php endif; ?>
                <div class="garage-name"><?php echo GARAGE_NAME; ?></div>
                <div class="garage-details">
                    <?php echo GARAGE_ADDRESS; ?><br>
                    Tél: <?php echo GARAGE_PHONE; ?><br>
                    <?php if(defined('GARAGE_EMAIL')): ?>Email: <?php echo GARAGE_EMAIL; ?><br><?php endif; ?>
                    <?php if(defined('GARAGE_MATRICULE')): ?>Mat. Fiscal: <?php echo GARAGE_MATRICULE; ?><?php endif; ?>
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-number">N° <?php echo str_pad($invoice['id'], 6, '0', STR_PAD_LEFT); ?></div>
                <div class="invoice-number">Date: <?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?></div>
            </div>
        </div>

        <div class="client-section">
            <div class="client-title">INFORMATIONS CLIENT</div>
            <strong><?php echo htmlspecialchars($client['name']); ?></strong><br>
            <?php if(!empty($client['car_model'])): ?>Modèle: <?php echo htmlspecialchars($client['car_model']); ?><br><?php endif; ?>
            <?php if(!empty($client['address'])): ?><?php echo htmlspecialchars($client['address']); ?><br><?php endif; ?>
            <?php if(!empty($client['phone'])): ?>Tél: <?php echo htmlspecialchars($client['phone']); ?><?php endif; ?>
            <div style="margin-top: 10px;"><strong>Kilométrage:</strong> <?php echo number_format($invoice['mileage'], 0, ',', ' '); ?> KM</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Désignation</th>
                    <th style="width: 10%; text-align: center;">Qté</th>
                    <th style="width: 20%; text-align: right;">Prix Unit.</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $lineCount = 0;
                foreach ($lines as $line): 
                    $lineCount++;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($line['type_name']); ?></td>
                    <td style="text-align: center;"><?php echo $line['quantity']; ?></td>
                    <td style="text-align: right;"><?php echo number_format($line['price_unit'], 3, ',', ' '); ?></td>
                    <td style="text-align: right;"><?php echo number_format($line['total_line'], 3, ',', ' '); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if($lineCount < 5): ?>
                    <?php for($i = $lineCount; $i < 5; $i++): ?>
                    <tr style="height: 30px;"><td colspan="4"></td></tr>
                    <?php endfor; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals">
            <p>Total HT: <strong><?php echo number_format($invoice['total_ht'], 3, ',', ' '); ?> TND</strong></p>
            <p>TVA (<?php echo $invoice['tax_rate']; ?>%): <strong><?php echo number_format($invoice['total_tva'], 3, ',', ' '); ?> TND</strong></p>
            <p>Droit de timbre: <strong><?php echo number_format($invoice['droit_timbre'], 3, ',', ' '); ?> TND</strong></p>
            <p class="total-ttc">Net à Payer: <?php echo number_format($invoice['total_ttc'], 3, ',', ' '); ?> TND</p>
        </div>

        <?php if(!empty($invoice['comment'])): ?>
        <div class="comment-section">
            <div class="client-title">COMMENTAIRE</div>
            <?php echo nl2br(htmlspecialchars($invoice['comment'])); ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <?php echo GARAGE_NAME; ?> - <?php echo GARAGE_ADDRESS; ?> - Tél: <?php echo GARAGE_PHONE; ?><br>
            Merci de votre confiance - Document généré le <?php echo date('d/m/Y à H:i'); ?>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn">🖨️ Imprimer / PDF</button>
        <a href="index.php?route=invoices" class="btn btn-danger">Retour</a>
    </div>
</body>
</html>