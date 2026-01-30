<?php
require_once('protection_pages.php');
$page_title = 'Situation de la caisse';
require_once('header.php');
?>
<div class="container py-4">
    <h1 class="mb-4"><span class="material-symbols-outlined align-middle text-success" style="font-size:2.2rem;">savings</span> Situation de la caisse</h1>
    <div class="alert alert-info mb-4">
        Visualisez ici l’état complet de la caisse : <b>entrées</b>, <b>sorties</b>, <b>solde</b>, <b>mouvements</b>, et <b>analyse des flux quotidiens</b>.<br>
        <span class="text-muted small" data-bs-toggle="tooltip" title="Astuce : Utilisez les filtres pour analyser les flux par jour ou par type de mouvement.">Module interactif pour le suivi financier.</span>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-2 border-success">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-success" id="soldeCaisse" data-bs-toggle="tooltip" title="Solde actuel de la caisse">0</div>
                    <div class="small text-muted">Solde actuel</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-2 border-primary">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-primary" id="entreesCaisse" data-bs-toggle="tooltip" title="Total des entrées (ventes, dépôts)">0</div>
                    <div class="small text-muted">Entrées</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-2 border-danger">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-danger" id="sortiesCaisse" data-bs-toggle="tooltip" title="Total des sorties (dépenses, retraits)">0</div>
                    <div class="small text-muted">Sorties</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-2 border-warning">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-warning" id="mouvementsCaisse" data-bs-toggle="tooltip" title="Nombre de mouvements enregistrés">0</div>
                    <div class="small text-muted">Mouvements</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <b>Solde et mouvements</b>
            <button class="btn btn-sm btn-outline-secondary" id="btnExportCaisse"><i class="material-symbols-outlined align-middle">download</i> Export Excel</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="tableCaisse">
                    <thead class="table-light">
                        <tr>
                            <th data-bs-toggle="tooltip" title="Date du mouvement">Date</th>
                            <th data-bs-toggle="tooltip" title="Type d'opération (entrée/sortie)">Type</th>
                            <th data-bs-toggle="tooltip" title="Montant du mouvement">Montant</th>
                            <th data-bs-toggle="tooltip" title="Motif ou description">Motif</th>
                            <th data-bs-toggle="tooltip" title="Utilisateur ayant effectué le mouvement">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCaisse">
                        <tr><td colspan="5" class="text-muted text-center">Module en construction. Les mouvements de caisse s’afficheront ici.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="alert alert-warning mt-4">
        <span class="material-symbols-outlined align-middle text-warning" style="font-size:1.3em;vertical-align:middle;">info</span>
        <b>LE MODULE SITUATION CAISSE</b> : Suivi complet des flux financiers, export, analyse et sécurité. <span class="text-muted">(À finaliser selon vos besoins)</span>
    </div>
</div>
<script>
// TODO : Charger les mouvements de caisse via AJAX et calculer les statistiques
// Astuce : Ajoutez des filtres par date/type pour une analyse fine
// Export Excel
    document.getElementById('btnExportCaisse').addEventListener('click', function() {
        showAlertModal({title:'Export Excel', message:'Fonction à activer (export des mouvements de caisse).', type:'info'});
    });
// Activation tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?php require_once('footer.php'); ?>
