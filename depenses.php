<?php
require_once('protection_pages.php');
$page_title = 'Suivi des dépenses & comptabilité';
require_once('header.php');
?>
<div class="container py-4">
    <h1 class="mb-4"><i class="material-symbols-outlined align-middle text-primary">receipt_long</i> Dépenses</h1>
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Solde caisse</div>
                    <div class="fw-bold fs-4 text-success" id="soldeCaisse">0 <?php echo $devise; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Dépenses du jour</div>
                    <div class="fw-bold fs-4 text-danger" id="depensesJour">0 <?php echo $devise; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="text-muted small">Entrées du jour</div>
                    <div class="fw-bold fs-4 text-primary" id="entreesJour">0 <?php echo $devise; ?></div>
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
    <div class="alert alert-info mb-3">
        Visualisez, ajoutez et exportez toutes les dépenses de la pharmacie : achats, charges, frais divers, et suivez la comptabilité en temps réel.
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-success" id="btnAddDepense"><i class="material-symbols-outlined align-middle">add</i> Nouvelle dépense</button>
        <button class="btn btn-outline-secondary" id="btnExportDepenses"><i class="material-symbols-outlined align-middle">download</i> Exporter</button>
    </div>
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <b>Dépenses récentes</b>
            <input type="text" class="form-control form-control-sm w-auto" id="searchDepenses" placeholder="Filtrer (libellé, catégorie, date)">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0" id="depensesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Montant</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="depensesTbody">
                        <tr><td colspan="6" class="text-center text-muted">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <span id="totalDepenses" class="fw-bold"></span>
        </div>
    </div>

    <!-- Modal Dépense -->
    <div class="modal fade" id="modalDepense" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="depenseForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDepenseTitle">Nouvelle dépense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="depense_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" id="depense_libelle" name="libelle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant *</label>
                            <input type="number" class="form-control" id="depense_montant" name="montant" min="0.01" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="depense_date" name="date_depense" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select" id="depense_categorie" name="categorie" required>
                                <option value="">Sélectionner</option>
                                <option value="Achat stock">Achat stock</option>
                                <option value="Frais">Frais</option>
                                <option value="Salaire">Salaire</option>
                                <option value="Transfert">Transfert</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Transport">Transport</option>
                                <option value="Communication">Communication</option>
                                <option value="Loyer">Loyer</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="depense_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// CRUD Dépenses - JS natif
let depensesData = [];
function chargerDepenses() {
    fetch('ajax/depenses.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action: 'list'})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            depensesData = data.data || [];
            afficherDepenses(depensesData);
            majDashboard(depensesData);
        } else {
            document.getElementById('depensesTbody').innerHTML = '<tr><td colspan="6" class="text-danger">Erreur : '+data.message+'</td></tr>';
        }
    });
}
function afficherDepenses(depenses) {
    const tbody = document.getElementById('depensesTbody');
    if (!depenses || depenses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Aucune dépense enregistrée.</td></tr>';
        document.getElementById('totalDepenses').textContent = '';
        return;
    }
    let total = 0;
    tbody.innerHTML = depenses.map(dep => {
        total += parseFloat(dep.montant);
        return `<tr>
            <td>${dep.date_depense}</td>
            <td>${dep.libelle}</td>
            <td><b>${parseFloat(dep.montant).toLocaleString('fr-FR', {minimumFractionDigits:2})} <?php echo $devise; ?></b></td>
            <td>${dep.categorie||''}</td>
            <td>${dep.description||''}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-1" onclick="editDepense(${dep.id_depense})"><i class="material-symbols-outlined">edit</i></button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteDepense(${dep.id_depense})"><i class="material-symbols-outlined">delete</i></button>
            </td>
        </tr>`;
    }).join('');
    document.getElementById('totalDepenses').textContent = 'Total : ' + total.toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
}
// Dashboard synthétique
function majDashboard(depenses) {
    const today = new Date().toISOString().slice(0,10);
    let depJour = 0, entreesJour = 0, nb = 0;
    depenses.forEach(d => {
        if (d.date_depense === today) depJour += parseFloat(d.montant);
        nb++;
    });
    // TODO : calculer le vrai solde caisse et entrées du jour (mock ici)
    document.getElementById('soldeCaisse').textContent = (100000 - depJour).toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
    document.getElementById('depensesJour').textContent = depJour.toLocaleString('fr-FR', {minimumFractionDigits:2}) + ' <?php echo $devise; ?>';
    document.getElementById('entreesJour').textContent = '0.00 <?php echo $devise; ?>';
    document.getElementById('nbOperations').textContent = nb;
}
// Filtrage
document.getElementById('searchDepenses').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    afficherDepenses(depensesData.filter(dep =>
        (dep.libelle && dep.libelle.toLowerCase().includes(q)) ||
        (dep.categorie && dep.categorie.toLowerCase().includes(q)) ||
        (dep.date_depense && dep.date_depense.includes(q))
    ));
});
// Ajout
document.getElementById('btnAddDepense').addEventListener('click', function() {
    document.getElementById('depenseForm').reset();
    document.getElementById('depense_id').value = '';
    document.getElementById('modalDepenseTitle').textContent = 'Nouvelle dépense';
    new bootstrap.Modal(document.getElementById('modalDepense')).show();
});
// Soumission
document.getElementById('depenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', formData.get('id') ? 'update' : 'add');
    fetch('ajax/depenses.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalDepense')).hide();
            showAlertModal({title:'Succès', message:data.message, type:'success'});
            chargerDepenses();
        } else {
            showAlertModal({title:'Erreur', message:data.message, type:'error'});
        }
    });
});
// Edition
function editDepense(id) {
    fetch('ajax/depenses.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action:'get', id})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const d = data.data;
            document.getElementById('depense_id').value = d.id_depense;
            document.getElementById('depense_libelle').value = d.libelle;
            document.getElementById('depense_montant').value = d.montant;
            document.getElementById('depense_date').value = d.date_depense;
            document.getElementById('depense_categorie').value = d.categorie||'';
            document.getElementById('depense_description').value = d.description||'';
            document.getElementById('modalDepenseTitle').textContent = 'Modifier la dépense';
            new bootstrap.Modal(document.getElementById('modalDepense')).show();
        } else {
            showAlertModal({title:'Erreur', message:data.message, type:'error'});
        }
    });
}
// Suppression
function deleteDepense(id) {
    showConfirmModal({
        title:'Supprimer la dépense',
        message:'Voulez-vous vraiment supprimer cette dépense ?',
        type:'warning',
        onConfirm:()=>{
            fetch('ajax/depenses.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({action:'delete', id})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlertModal({title:'Succès', message:data.message, type:'success'});
                    chargerDepenses();
                } else {
                    showAlertModal({title:'Erreur', message:data.message, type:'error'});
                }
            });
        }
    });
}
// Export CSV
document.getElementById('btnExportDepenses').addEventListener('click', function() {
    if (!depensesData.length) return showAlertModal({title:'Aucune donnée', message:'Aucune dépense à exporter.', type:'info'});
    let csv = 'Date;Libellé;Montant;Catégorie;Description\n';
    depensesData.forEach(d => {
        csv += `${d.date_depense};${d.libelle};${d.montant};${d.categorie||''};${d.description||''}\n`;
    });
    const blob = new Blob([csv], {type:'text/csv'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'depenses_<?php echo date('Ymd_His'); ?>.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});
// Init
chargerDepenses();
</script>
<?php require_once('footer.php'); ?>
