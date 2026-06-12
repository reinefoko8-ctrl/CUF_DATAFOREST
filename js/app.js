// ============================================================
// CUF DataForest - JavaScript Principal
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- Score buttons (1 / 0 / NA) ----
    document.querySelectorAll('.critere-score').forEach(function (group) {
        group.querySelectorAll('.score-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Reset all buttons in this group
                group.querySelectorAll('.score-btn').forEach(function(b) {
                    b.classList.remove('selected-1', 'selected-0', 'selected-na');
                });

                // Apply selection style
                var val = btn.dataset.val;
                if (val === '1' || val === '0.5') {
                    btn.classList.add('selected-1');
                } else if (val === '0') {
                    btn.classList.add('selected-0');
                } else if (val === 'NA') {
                    btn.classList.add('selected-na');
                }

                // Update hidden input in the same <tr>
                var row = group.closest('tr');
                if (row) {
                    var hidden = row.querySelector('input[type=hidden]');
                    if (hidden) {
                        hidden.value = val;
                    }
                }

                // Recalculate total
                updateTotal();
            });
        });
    });

    // ---- Restore button states from hidden inputs on page load ----
    document.querySelectorAll('tr[data-pts]').forEach(function(row) {
        var hidden = row.querySelector('input[type=hidden]');
        if (!hidden || !hidden.value) return;
        var val = hidden.value;
        var group = row.querySelector('.critere-score');
        if (!group) return;
        group.querySelectorAll('.score-btn').forEach(function(btn) {
            btn.classList.remove('selected-1','selected-0','selected-na');
            if (btn.dataset.val === val) {
                if (val === '1' || val === '0.5') btn.classList.add('selected-1');
                else if (val === '0') btn.classList.add('selected-0');
                else if (val === 'NA') btn.classList.add('selected-na');
            }
        });
    });

    // ---- Calcul total auto ----
    function updateTotal() {
        var total = 0;
        var max   = 0;

        document.querySelectorAll('tr[data-pts]').forEach(function(row) {
            var pts    = parseFloat(row.dataset.pts || 1);
            var hidden = row.querySelector('input[type=hidden]');
            if (!hidden) return;
            var val = hidden.value;

            if (val === 'NA' || val === '') {
                // NA ne compte ni dans total ni dans max
                return;
            }
            max += pts;
            if (val === '1') {
                total += pts;
            } else if (val === '0.5') {
                total += pts * 0.5;
            }
            // '0' → ajoute 0
        });

        // Afficher
        var display = document.getElementById('total-display');
        if (display) {
            // Trouver le max affiché (ex: "0/10" → prendre le "/10")
            var maxStr = display.textContent.split('/')[1] || '10';
            display.textContent = total.toFixed(1) + '/' + maxStr.trim();
        }

        // Mettre à jour le champ hidden total_points
        var hiddenTotal = document.getElementById('total-hidden');
        if (hiddenTotal) {
            hiddenTotal.value = total.toFixed(2);
        }
    }

    // Calculer le total au chargement (pour les fiches déjà remplies)
    updateTotal();

    // ---- Tabs (login page) ----
    document.querySelectorAll('[data-tab]').forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            var target = tab.dataset.tab;
            document.querySelectorAll('[data-tab]').forEach(function(t) {
                t.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(function(p) {
                p.classList.remove('active');
            });
            tab.classList.add('active');
            var pane = document.getElementById(target);
            if (pane) pane.classList.add('active');
        });
    });

    // ---- Alert auto-dismiss ----
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
        }, 5000);
    });

    // ---- Confirm delete ----
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // ---- Sidebar active link ----
    var path = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-link').forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && href.indexOf(path) !== -1 && path !== '') {
            link.classList.add('active');
        }
    });

    // ---- Dynamic row add (traçabilité grumes) ----
    var addRowBtn = document.getElementById('add-grume-row');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            var tbody = document.getElementById('grumes-tbody');
            var rowCount = tbody.querySelectorAll('tr').length;
            var idx = rowCount;
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td style="text-align:center;font-weight:700;">' + (rowCount + 1) + '</td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][essence]" style="min-width:90px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][num_df10]" style="min-width:80px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][code_barre]" style="min-width:80px;"/></td>' +
                '<td><input class="form-control" type="date" name="grumes[' + idx + '][date_abattage]" style="min-width:120px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][num_seq]" style="min-width:70px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][n_ligne]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][n_ordre]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][n_fiche]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="number" step="0.01" name="grumes[' + idx + '][volume]" style="min-width:70px;"/></td>' +
                '<td><input class="form-control" type="number" name="grumes[' + idx + '][diam_pb]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="number" name="grumes[' + idx + '][diam_gb]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="number" step="0.01" name="grumes[' + idx + '][long]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][n_lv]" style="min-width:60px;"/></td>' +
                '<td><input class="form-control" type="text" name="grumes[' + idx + '][affectation]" style="min-width:100px;"/></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove()">✕</button></td>';
            tbody.appendChild(tr);
            var counter = document.getElementById('grume-count');
            if (counter) counter.textContent = tbody.querySelectorAll('tr').length;
        });
    }

    // ---- Tronçonneuse rows ----
    var addTcBtn = document.getElementById('add-tc-row');
    if (addTcBtn) {
        addTcBtn.addEventListener('click', function () {
            var tbody = document.getElementById('tc-tbody');
            var idx = tbody.querySelectorAll('tr').length;
            var tr = document.createElement('tr');
            var cells = '<td><input class="form-control" type="text" name="tc[' + idx + '][num_serie]" placeholder="N° série" /></td>';
            ['e1','e2','e3','e4','e5','e6','e7','e8'].forEach(function(e) {
                cells += '<td class="text-center"><select class="form-control" name="tc[' + idx + '][' + e + ']">' +
                    '<option value="">-</option>' +
                    '<option value="1">✓</option>' +
                    '<option value="0">✗</option>' +
                    '</select></td>';
            });
            cells += '<td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove()">✕</button></td>';
            tr.innerHTML = cells;
            tbody.appendChild(tr);
        });
    }

    // ---- Recherche tableau ----
    var searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(function (tr) {
                tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }

    // ---- Fade-in on load ----
    document.querySelectorAll('.fade-in').forEach(function (el, i) {
        el.style.animationDelay = (i * 0.07) + 's';
    });

    // ---- Score progress bar ----
    document.querySelectorAll('.progress-bar').forEach(function (bar) {
        var pct = parseFloat(bar.dataset.pct || 0);
        bar.style.width = pct + '%';
        bar.style.background = pct >= 70 ? 'var(--vert-clair)' : pct >= 40 ? 'var(--orange-alerte)' : 'var(--rouge)';
    });

    // ---- Radio role tabs style (login page) ----
    document.querySelectorAll('.role-tab input[type=radio]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.role-tab').forEach(function(t) {
                t.classList.remove('active');
            });
            this.closest('.role-tab').classList.add('active');
        });
        if (radio.checked) radio.closest('.role-tab').classList.add('active');
    });

});
