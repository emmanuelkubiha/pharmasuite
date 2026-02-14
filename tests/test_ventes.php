<?php
require_once __DIR__ . '/../config/database.php';
$sql = "SELECT id_vente, montant_total, mode_paiement, statut, date_vente FROM ventes ORDER BY date_vente DESC LIMIT 20";
$ventes = db_fetch_all($sql);
echo "<h2>Ventes récentes</h2><table border='1'><tr><th>ID</th><th>Montant</th><th>Mode</th><th>Statut</th><th>Date</th></tr>";
foreach ($ventes as $v) {
    echo "<tr><td>".e($v['id_vente'])."</td><td>".e($v['montant_total'])."</td><td>".e($v['mode_paiement'])."</td><td>".e($v['statut'])."</td><td>".e($v['date_vente'])."</td></tr>";
}
echo "</table>";
?>
