/**
 * PARAMETRES.JS - PharmaSuite
 * Scripts pour la page de paramètres
 */

let cropper = null;
let currentImageFile = null;
let currentReinitType = null;
let currentLogsPage = 1;

// ============================================
// GESTION DU CROP D'IMAGE (LOGO)
// ============================================

function selectLogo(input) {
    if (input.files && input.files[0]) {
        currentImageFile = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const cropImage = document.getElementById('cropImage');
            cropImage.src = e.target.result;
            
            document.getElementById('cropModal').classList.add('active');
            
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(cropImage, {
                aspectRatio: NaN,
                viewMode: 1,
                autoCropArea: 0.8,
                responsive: true,
                background: false,
                zoomable: true,
                scalable: true,
                cropBoxResizable: true,
                cropBoxMovable: true,
            });
        };
        
        reader.readAsDataURL(currentImageFile);
    }
}

function applyCrop() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 800,
            maxHeight: 800,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        const croppedDataUrl = canvas.toDataURL('image/png');
        document.getElementById('logoCroppedData').value = croppedDataUrl;
        
        const preview = document.getElementById('logoPreview');
        preview.src = croppedDataUrl;
        document.getElementById('logoPreviewContainer').style.display = 'block';
        
        document.getElementById('cropModal').classList.remove('active');
        
        cropper.destroy();
        cropper = null;
    }
}

function cancelCrop() {
    document.getElementById('cropModal').classList.remove('active');
    document.getElementById('logoInput').value = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

function reopenCrop() {
    if (currentImageFile) {
        const input = document.getElementById('logoInput');
        const dt = new DataTransfer();
        dt.items.add(currentImageFile);
        input.files = dt.files;
        selectLogo(input);
    }
}

function updateColorText(inputId) {
    const color = document.getElementById(inputId).value;
    document.getElementById(inputId + '_text').value = color;
}

// ============================================
// RÉINITIALISATIONS
// ============================================

function showPasswordModal(type, message) {
    currentReinitType = type;
    
    const modalId = 'passwordModal_' + Date.now();
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmation de Réinitialisation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger mb-3"><strong>${message}</strong></p>
                        <div class="mb-3">
                            <label class="form-label">Entrez votre mot de passe :</label>
                            <input type="password" id="reinitPassword" class="form-control" placeholder="Mot de passe..." autofocus>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger" onclick="confirmerReinitAvecPassword('${type}', '${modalId}')">
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
    
    document.getElementById('reinitPassword').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            confirmerReinitAvecPassword(type, modalId);
        }
    });
}

function confirmerReinitAvecPassword(type, modalId) {
    const password = document.getElementById('reinitPassword').value;
    
    if (!password) {
        showAlertModal({
            title: 'Erreur',
            message: 'Le mot de passe est requis',
            type: 'error'
        });
        return;
    }
    
    bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
    executerReinit(type, password);
}

function reinitVentes() {
    showPasswordModal('ventes', 'Supprimer TOUTES les ventes ? Cette action est irréversible.');
}

function reinitProduits() {
    showPasswordModal('produits', 'Supprimer TOUS les produits ? Cette action est irréversible.');
}

function reinitClients() {
    showPasswordModal('clients', 'Supprimer TOUS les clients ? Cette action est irréversible.');
}

function reinitUtilisateurs() {
    showPasswordModal('utilisateurs', 'Supprimer TOUS les utilisateurs ? Cette action est irréversible.');
}

function reinitComplet() {
    showPasswordModal('complet', 'RÉINITIALISATION COMPLÈTE ! Tout sera effacé. Cette action est irréversible !');
}

function executerReinit(type, password) {
    fetch('ajax/reinitialiser_donnees.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=' + encodeURIComponent(type) + '&password=' + encodeURIComponent(password)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlertModal({
                title: 'Succès',
                message: data.message + ' La page va se rafraîchir...',
                type: 'success',
                onClose: function() {
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlertModal({
                title: 'Erreur',
                message: data.message,
                type: 'error'
            });
        }
    })
    .catch(err => {
        showAlertModal({
            title: 'Erreur',
            message: 'Erreur lors de la réinitialisation: ' + err.message,
            type: 'error'
        });
    });
}

// ============================================
// IMPORT/EXPORT
// ============================================

function exporterExcel() {
    const type = document.getElementById('typeExport').value;
    const dateDebut = document.getElementById('dateDebutExport').value;
    const dateFin = document.getElementById('dateFinExport').value;
    window.open(`ajax/export_excel.php?type=${type}&date_debut=${dateDebut}&date_fin=${dateFin}`, '_blank');
}

function exporterPDF() {
    const type = document.getElementById('typeExport').value;
    const dateDebut = document.getElementById('dateDebutExport').value;
    const dateFin = document.getElementById('dateFinExport').value;
    window.open(`ajax/export_pdf.php?type=${type}&date_debut=${dateDebut}&date_fin=${dateFin}`, '_blank');
}

// ============================================
// LOGS D'ACTIVITÉS
// ============================================

function chargerLogs(page = 1) {
    const typeAction = document.getElementById('typeActionFilter').value;
    const utilisateur = document.getElementById('utilisateurFilter').value;
    const dateDebut = document.getElementById('dateDebutLogs').value;
    const dateFin = document.getElementById('dateFinLogs').value;
    
    const tableBody = document.getElementById('logsTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <p class="mt-2 text-muted mb-0 small">Récupération...</p>
            </td>
        </tr>
    `;
    
    const params = new URLSearchParams({
        page: page,
        type_action: typeAction,
        id_utilisateur: utilisateur,
        date_debut: dateDebut,
        date_fin: dateFin
    });
    
    fetch(`ajax/get_logs.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentLogsPage = page;
                renderLogs(data.logs);
                renderPagination(data.total, data.limit, data.page, data.total_pages);
                document.getElementById('totalLogs').textContent = `${data.total} log(s)`;
                
                if (data.logs.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 13h3l3 3h4l3 -3h3"/></svg>
                                <p class="mt-2">Aucun log trouvé</p>
                            </td>
                        </tr>
                    `;
                }
            } else {
                showAlertModal({
                    title: 'Erreur',
                    message: data.message,
                    type: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                        <p>Erreur lors du chargement des logs</p>
                    </td>
                </tr>
            `;
        });
}

function renderLogs(logs) {
    const tableBody = document.getElementById('logsTableBody');
    
    if (logs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <p class="mb-0">Aucun log trouvé</p>
                </td>
            </tr>
        `;
        return;
    }
    
    const html = logs.map(log => `
        <tr>
            <td class="align-middle"><small>${log.date}</small></td>
            <td class="align-middle"><strong>${log.utilisateur}</strong></td>
            <td class="align-middle">
                <span class="badge bg-${log.type_badge} text-white">${log.type_action}</span>
            </td>
            <td class="align-middle">
                <div class="text-truncate" style="max-width:300px;" title="${escapeHtml(log.description)}">
                    ${escapeHtml(log.description)}
                </div>
            </td>
            <td class="align-middle"><code class="small">${log.ip}</code></td>
            <td class="align-middle">
                <small title="${escapeHtml(log.user_agent_full)}">${log.browser}</small>
            </td>
        </tr>
    `).join('');
    
    tableBody.innerHTML = html;
}

function renderPagination(total, limit, currentPage, totalPages) {
    const paginationContainer = document.getElementById('paginationLogs');
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let html = '<ul class="pagination pagination-sm justify-content-center mb-0">';
    
    if (currentPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(${currentPage - 1}); return false;">Préc.</a>
            </li>
        `;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Préc.</span></li>';
    }
    
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="chargerLogs(1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="chargerLogs(${i}); return false;">${i}</a></li>`;
        }
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="chargerLogs(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    if (currentPage < totalPages) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="chargerLogs(${currentPage + 1}); return false;">Suiv.</a>
            </li>
        `;
    } else {
        html += '<li class="page-item disabled"><span class="page-link">Suiv.</span></li>';
    }
    
    html += '</ul>';
    paginationContainer.innerHTML = html;
}

function purgerVieuxLogs() {
    showConfirmModal({
        title: 'Purger les vieux logs?',
        message: 'Supprimer tous les logs de plus de 90 jours ?',
        onConfirm: () => {
            const btn = document.querySelector('button[onclick="purgerVieuxLogs()"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Purge...';
            
            fetch('ajax/purger_logs.php', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                if (data.success) {
                    showAlertModal({
                        title: 'Succès',
                        message: data.message,
                        type: 'success'
                    });
                    chargerLogs(1);
                } else {
                    showAlertModal({
                        title: 'Erreur',
                        message: data.message,
                        type: 'error'
                    });
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                showAlertModal({
                    title: 'Erreur',
                    message: 'Impossible de purger les logs',
                    type: 'error'
                });
            });
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    
    // Import Excel
    const formImport = document.getElementById('formImport');
    if (formImport) {
        formImport.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const resultDiv = document.getElementById('importResult');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Import...';
            
            fetch('ajax/import_excel.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>Importer';
                
                if (data.success) {
                    resultDiv.className = 'alert alert-success mt-3';
                    resultDiv.innerHTML = `<strong>${data.message}</strong>`;
                    if (data.stats) {
                        resultDiv.innerHTML += `
                            <ul class="mb-0 mt-2 small">
                                <li>Ajoutés : ${data.stats.ajoutes}</li>
                                <li>Mis à jour : ${data.stats.mis_a_jour}</li>
                                <li>Erreurs : ${data.stats.erreurs}</li>
                            </ul>
                        `;
                    }
                    resultDiv.style.display = 'block';
                    formImport.reset();
                    setTimeout(() => resultDiv.style.display = 'none', 10000);
                } else {
                    resultDiv.className = 'alert alert-danger mt-3';
                    resultDiv.innerHTML = `<strong>Erreur :</strong> ${data.message}`;
                    resultDiv.style.display = 'block';
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>Importer';
                
                resultDiv.className = 'alert alert-danger mt-3';
                resultDiv.innerHTML = `<strong>Erreur :</strong> ${error.message}`;
                resultDiv.style.display = 'block';
            });
        });
    }
    
    // Charger logs si section ouverte
    const logsSection = document.getElementById('logsTableBody');
    if (logsSection) {
        // Charger au clic sur l'accordéon logs
        const logsAccordion = document.getElementById('heading3');
        if (logsAccordion) {
            logsAccordion.querySelector('button').addEventListener('click', function() {
                setTimeout(() => {
                    if (document.getElementById('collapse3').classList.contains('show')) {
                        chargerLogs(1);
                    }
                }, 350);
            });
        }
    }
});
