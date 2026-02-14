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
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-success">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-success" id="soldeTotalCaisse" data-bs-toggle="tooltip" title="Solde total en caisse">0</div>
                    <div class="small text-muted">Solde total</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-primary">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-primary" id="soldeCashCaisse" data-bs-toggle="tooltip" title="Solde en espèces">0</div>
                    <div class="small text-muted">Espèces (Cash)</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-warning">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-warning" id="soldeMobileCaisse" data-bs-toggle="tooltip" title="Solde Mobile Money">0</div>
                    <div class="small text-muted">Mobile Money</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-info">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-info" id="soldeCarteCaisse" data-bs-toggle="tooltip" title="Solde Carte">0</div>
                    <div class="small text-muted">Carte</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-secondary">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-secondary" id="soldeChequeCaisse" data-bs-toggle="tooltip" title="Solde Chèque">0</div>
                    <div class="small text-muted">Chèque</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-2 border-dark">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-dark" id="soldeCreditCaisse" data-bs-toggle="tooltip" title="Solde Crédit">0</div>
                    <div class="small text-muted">Crédit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-2 border-success">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-success" id="soldeFermetureCaisse" data-bs-toggle="tooltip" title="Solde de fermeture">0</div>
                    <div class="small text-muted">Solde de fermeture (dernier mouvement)</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-2 border-primary">
                <div class="card-body py-2 px-3 text-center">
                    <div class="fw-bold text-primary" id="entreesJourCaisse" data-bs-toggle="tooltip" title="Total des entrées du jour">0</div>
                    <div class="small text-muted">Total entrées du jour</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-light d-flex flex-wrap gap-2 align-items-center">
            <b>Filtres</b>
            <input type="date" id="filtreDateDebut" class="form-control form-control-sm" style="max-width:150px;" placeholder="Début">
            <input type="date" id="filtreDateFin" class="form-control form-control-sm" style="max-width:150px;" placeholder="Fin">
            <select id="filtreType" class="form-select form-select-sm" style="max-width:120px;">
                <option value="">Tous types</option>
                <option value="Entrée">Entrée</option>
                <option value="Sortie">Sortie</option>
            </select>
            <button class="btn btn-sm btn-outline-primary" onclick="chargerCaisse()"><span class="material-symbols-outlined align-middle">search</span> Filtrer</button>
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
                            <th data-bs-toggle="tooltip" title="Actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCaisse">
                        <!-- Les lignes seront injectées dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
// Export Excel
    document.getElementById('btnExportCaisse').addEventListener('click', function() {
        fetch('ajax/caisse.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action:'export',
                date_debut:document.getElementById('filtreDateDebut').value,
                date_fin:document.getElementById('filtreDateFin').value,
                type:document.getElementById('filtreType').value
            })
        })
        .then(r=>r.blob())
        .then(blob=>{
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'export_caisse_'+(new Date().toISOString().slice(0,10))+'.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        });
    });
// CRUD caisse avec filtres et calculs
function chargerCaisse() {
    const date_debut = document.getElementById('filtreDateDebut').value;
    const date_fin = document.getElementById('filtreDateFin').value;
    const type = document.getElementById('filtreType').value;
    fetch('ajax/caisse.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action:'list',date_debut,date_fin,type})
    })
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('tbodyCaisse');
        let solde = 0, entrees = 0, sorties = 0;
        if (!data.success || !data.data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-muted text-center">Aucun mouvement enregistré.</td></tr>';
        } else {
            tbody.innerHTML = data.data.map(m => {
                if(m.type==='Entrée') { solde += parseFloat(m.montant); entrees += parseFloat(m.montant); }
                else { solde -= parseFloat(m.montant); sorties += parseFloat(m.montant); }
                return `<tr>
                    <td>${m.date_mouvement}</td>
                    <td>${e(m.type)}</td>
                    <td><b>${parseFloat(m.montant).toLocaleString('fr-FR', {minimumFractionDigits:2})} <?php echo $devise; ?></b></td>
                    <td>${e(m.motif)}</td>
                    <td>${e(m.nom_complet||'')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editMouvement(${m.id_mouvement})"><span class="material-symbols-outlined">edit</span></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMouvement(${m.id_mouvement})"><span class="material-symbols-outlined">delete</span></button>
                    </td>
                </tr>`;
            }).join('');
        }
        document.getElementById('soldeTotalCaisse').textContent = (data.solde_total||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        document.getElementById('soldeCashCaisse').textContent = (data.solde_especes||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        document.getElementById('soldeMobileCaisse').textContent = (data.solde_mobile_money||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        if(document.getElementById('soldeCarteCaisse')) document.getElementById('soldeCarteCaisse').textContent = (data.solde_carte||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        if(document.getElementById('soldeChequeCaisse')) document.getElementById('soldeChequeCaisse').textContent = (data.solde_cheque||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        if(document.getElementById('soldeCreditCaisse')) document.getElementById('soldeCreditCaisse').textContent = (data.solde_credit||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        // Solde fermeture et total entrées du jour
        if(document.getElementById('soldeFermetureCaisse')) document.getElementById('soldeFermetureCaisse').textContent = (data.solde_fermeture||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        if(document.getElementById('entreesJourCaisse')) document.getElementById('entreesJourCaisse').textContent = (data.total_entrees_jour||0).toLocaleString('fr-FR', {minimumFractionDigits:2});
        document.getElementById('entreesCaisse').textContent = entrees.toLocaleString('fr-FR', {minimumFractionDigits:2});
        document.getElementById('sortiesCaisse').textContent = sorties.toLocaleString('fr-FR', {minimumFractionDigits:2});
        document.getElementById('mouvementsCaisse').textContent = data.data ? data.data.length : 0;
    });
}
function addMouvement() {
    showConfirmModal({
        title:'Ajouter mouvement',
        message:'<form id="formAddMouv" class="mt-2">'+
            '<div class="mb-2"><label>Type</label><select name="type" class="form-select"><option value="Entrée">Entrée</option><option value="Sortie">Sortie</option></select></div>'+
            '<div class="mb-2"><label>Montant</label><input name="montant" type="number" class="form-control" required></div>'+
            '<div class="mb-2"><label>Date</label><input name="date_mouvement" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>"></div>'+
            '<div class="mb-2"><label>Motif</label><input name="motif" type="text" class="form-control"></div>'+
            '</form>',
        onConfirm:()=>{
            const f=document.getElementById('formAddMouv');
            const fd=new FormData(f);
            fd.append('action','add');
            fetch('ajax/caisse.php', {
                method:'POST',body:new URLSearchParams(fd)
            })
            .then(r=>r.json())
            .then(data=>{
                if(data.success){showAlertModal({title:'Succès',message:data.message,type:'success'});chargerCaisse();}
                else showAlertModal({title:'Erreur',message:data.message,type:'error'});
            });
        }
    });
}
function editMouvement(id) {
    fetch('ajax/caisse.php', {
        method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({action:'get',id})
    })
    .then(r=>r.json())
    .then(data=>{
        if(!data.success) return showAlertModal({title:'Erreur',message:data.message,type:'error'});
        const m=data.data;
        showConfirmModal({
            title:'Modifier mouvement',
            message:'<form id="formEditMouv" class="mt-2">'+
                '<div class="mb-2"><label>Type</label><select name="type" class="form-select"><option value="Entrée"'+(m.type==='Entrée'?' selected':'')+'>Entrée</option><option value="Sortie"'+(m.type==='Sortie'?' selected':'')+'>Sortie</option></select></div>'+
                '<div class="mb-2"><label>Montant</label><input name="montant" type="number" class="form-control" value="'+m.montant+'" required></div>'+
                '<div class="mb-2"><label>Date</label><input name="date_mouvement" type="date" class="form-control" value="'+m.date_mouvement+'"></div>'+
                '<div class="mb-2"><label>Motif</label><input name="motif" type="text" class="form-control" value="'+e(m.motif)+'"></div>'+
                '</form>',
            onConfirm:()=>{
                const f=document.getElementById('formEditMouv');
                const fd=new FormData(f);
                fd.append('action','update');
                fd.append('id',id);
                fetch('ajax/caisse.php', {
                    method:'POST',body:new URLSearchParams(fd)
                })
                .then(r=>r.json())
                .then(data=>{
                    if(data.success){showAlertModal({title:'Succès',message:data.message,type:'success'});chargerCaisse();}
                    else showAlertModal({title:'Erreur',message:data.message,type:'error'});
                });
            }
        });
    });
}
function deleteMouvement(id) {
    showConfirmModal({
        title:'Supprimer mouvement',
        message:'Voulez-vous vraiment supprimer ce mouvement ? Action irréversible.',
        onConfirm:()=>{
            fetch('ajax/caisse.php', {
                method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({action:'delete',id})
            })
            .then(r=>r.json())
            .then(data=>{
                if(data.success){showAlertModal({title:'Succès',message:data.message,type:'success'});chargerCaisse();}
                else showAlertModal({title:'Erreur',message:data.message,type:'error'});
            });
        }
    });
}
// Initialisation
chargerCaisse();
</script>
<div class="mb-3 text-end">
    <button class="btn btn-success" onclick="addMouvement()"><span class="material-symbols-outlined align-middle">add</span> Ajouter mouvement</button>
</div>
<!-- ...existing code... -->
<?php require_once('footer.php'); ?>
