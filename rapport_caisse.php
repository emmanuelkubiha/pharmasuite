<?php
require_once('protection_pages.php');
$page_title = 'Rapport caisse';
require_once('header.php');
?>
<div class="container py-4">
    <h1 class="mb-4"><i class="material-symbols-outlined align-middle text-warning">bar_chart</i> Rapport caisse</h1>
    <div class="alert alert-info mb-4">
        Analysez et exportez tous les mouvements de caisse, soldes, écarts, et l’historique détaillé. Utilisez les filtres pour affiner votre analyse.
    </div>
    <!-- Dashboard synthétique -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Solde actuel</div>
                    <div class="fw-bold fs-4 text-success" id="soldeCaisse">0 <?php echo $devise; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Entrées (période)</div>
                    <div class="fw-bold fs-4 text-primary" id="entreesPeriode">0 <?php echo $devise; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Dépenses (période)</div>
                    <div class="fw-bold fs-4 text-danger" id="depensesPeriode">0 <?php echo $devise; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Nombre d'opérations</div>
                    <div class="fw-bold fs-4" id="nbOperations">0</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Filtres avancés -->
    <form class="row g-2 align-items-end mb-3" id="filtresCaisse">
        <div class="col-md-3">
            <label class="form-label">Période</label>
            <input type="date" class="form-control" id="dateDebut">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" id="dateFin">
        </div>
        <div class="col-md-3">
            <label class="form-label">Catégorie</label>
            <select class="form-select" id="categorieCaisse">
                <option value="">Toutes</option>
                <option value="Vente">Vente</option>
                <option value="Achat stock">Achat stock</option>
                <option value="Frais">Frais</option>
                <option value="Salaire">Salaire</option>
                <option value="Transfert">Transfert</option>
                <option value="Autre">Autre</option>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-primary" id="btnFiltrer">Filtrer</button>
            <button type="button" class="btn btn-outline-secondary ms-2" id="btnExportCaisse">Exporter</button>
        </div>
    </form>
    <!-- Tableau analytique -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <b>Rapport analytique</b>
            <span class="small text-muted" id="periodeAffichee"></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="caisseTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="caisseTbody">
                        <tr><td colspan="6" class="text-center text-muted">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
    // MOCK DATA - à remplacer par AJAX réel
    let mouvementsCaisse = [
        {date:'2026-01-23', libelle:'Vente médicaments', montant:50000, type:'Entrée', categorie:'Vente', description:'Vente du jour'},
        {date:'2026-01-23', libelle:'Achat stock', montant:-15000, type:'Dépense', categorie:'Achat stock', description:'Achat grossiste'},
        {date:'2026-01-22', libelle:'Frais électricité', montant:-3000, type:'Dépense', categorie:'Frais', description:'Facture SNEL'},
        {date:'2026-01-22', libelle:'Salaire agent', montant:-8000, type:'Dépense', categorie:'Salaire', description:'Janvier'},
        {date:'2026-01-21', libelle:'Vente médicaments', montant:40000, type:'Entrée', categorie:'Vente', description:'Vente du jour'},
        {date:'2026-01-21', libelle:'Transfert banque', montant:20000, type:'Entrée', categorie:'Transfert', description:'Versement banque'},
    ];
    function afficherCaisse(data) {
        const tbody = document.getElementById('caisseTbody');
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Aucune opération trouvée.</td></tr>';
            return;
        }
        tbody.innerHTML = data.map(m => `
            <tr>
                <td>${m.date}</td>
                <td>${m.libelle}</td>
                <td><b>${Math.abs(m.montant).toLocaleString('fr-FR', {minimumFractionDigits:2})} <?php echo $devise; ?></b> ${m.montant>0?'<span class="badge bg-success">Entrée</span>':'<span class="badge bg-danger">Dépense</span>'}</td>
                <td>${m.type}</td>
                <td>${m.categorie}</td>
                <td>${m.description||''}</td>
            </tr>
        `).join('');
    }
    function majDashboardCaisse(data) {
        let solde = 0, entrees = 0, depenses = 0, nb = 0;
        data.forEach(m => {
            solde += m.montant;
            if (m.montant > 0) entrees += m.montant;
            if (m.montant < 0) depenses += Math.abs(m.montant);
            nb++;
        });
        document.getElementById('soldeCaisse').textContent = solde.toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
        document.getElementById('entreesPeriode').textContent = entrees.toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
        document.getElementById('depensesPeriode').textContent = depenses.toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
        document.getElementById('nbOperations').textContent = nb;
    }
    function filtrerCaisse() {
        let d1 = document.getElementById('dateDebut').value;
        let d2 = document.getElementById('dateFin').value;
        let cat = document.getElementById('categorieCaisse').value;
        let res = mouvementsCaisse.filter(m => {
            let ok = true;
            if (d1 && m.date < d1) ok = false;
            if (d2 && m.date > d2) ok = false;
            if (cat && m.categorie !== cat) ok = false;
            return ok;
        });
        afficherCaisse(res);
        majDashboardCaisse(res);
        let periode = '';
        if (d1 && d2) periode = 'Du '+d1+' au '+d2;
        else if (d1) periode = 'Depuis '+d1;
        else if (d2) periode = 'Jusqu\'au '+d2;
        else periode = 'Toutes dates';
        document.getElementById('periodeAffichee').textContent = periode;
    }
    document.getElementById('btnFiltrer').addEventListener('click', filtrerCaisse);
    // Export CSV
    document.getElementById('btnExportCaisse').addEventListener('click', function() {
        let data = mouvementsCaisse;
        let d1 = document.getElementById('dateDebut').value;
        let d2 = document.getElementById('dateFin').value;
        let cat = document.getElementById('categorieCaisse').value;
        if (d1 || d2 || cat) {
            data = mouvementsCaisse.filter(m => {
                let ok = true;
                if (d1 && m.date < d1) ok = false;
                if (d2 && m.date > d2) ok = false;
                if (cat && m.categorie !== cat) ok = false;
                return ok;
            });
        }
        if (!data.length) return showAlertModal({title:'Aucune donnée', message:'Aucune opération à exporter.', type:'info'});
        let csv = 'Date;Libellé;Montant;Type;Catégorie;Description\n';
        data.forEach(m => {
            csv += `${m.date};${m.libelle};${m.montant};${m.type};${m.categorie};${m.description||''}\n`;
        });
        const blob = new Blob([csv], {type:'text/csv'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'rapport_caisse_<?php echo date('Ymd_His'); ?>.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    // Init
    afficherCaisse(mouvementsCaisse);
    majDashboardCaisse(mouvementsCaisse);
    </script>
</div>
<?php require_once('footer.php'); ?>
