<?php
require_once __DIR__ . '/../config/database.php';
// Test direct du calcul des soldes caisse
$sqlV = "SELECT montant_total, mode_paiement FROM ventes WHERE statut='validee'";
$sqlM = "SELECT montant, mode_paiement, type FROM caisse_mouvements";
$ventes = db_fetch_all($sqlV);
$mouvements = db_fetch_all($sqlM);

$modes = ['especes','mobile_money','carte','cheque','credit'];
$soldes = array_fill_keys($modes, 0);
foreach ($ventes as $v) {
    $mode = strtolower($v['mode_paiement']);
    if (isset($soldes[$mode])) $soldes[$mode] += floatval($v['montant_total']);
}
foreach ($mouvements as $m) {
    $mode = strtolower($m['mode_paiement']);
    if ($m['type'] === 'Sortie' && isset($soldes[$mode])) $soldes[$mode] -= floatval($m['montant']);
}
$total = array_sum($soldes);
echo "<h2>Test solde caisse</h2><table border='1'><tr><th>Mode</th><th>Solde</th></tr>";
foreach ($soldes as $mode=>$solde) {
    echo "<tr><td>".e($mode)."</td><td>".number_format($solde,2,',',' ')."</td></tr>";
}
echo "<tr><th>Total général</th><th>".number_format($total,2,',',' ')."</th></tr></table>";
?>
