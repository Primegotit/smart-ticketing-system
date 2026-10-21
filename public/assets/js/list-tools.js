/*!
 * list-tools.js - search / filter / sort for tables and card grids.
 *
 * TABLES   <table data-list-tools data-search-placeholder="...">
 *   th attributes:
 *     data-type="text|number|date|none"   (none = not sortable/searchable, e.g. Actions)
 *     data-filter[="Label"]               dropdown filter built from the column's values
 *     data-filter="range"                 (date columns) from/to date filter
 *   td attributes (optional):
 *     data-value         raw value used for sorting (number or unix timestamp)
 *     data-filter-value  value used for the dropdown filter (defaults to data-value / text)
 *
 * CARDS    <div data-list-cards data-search-placeholder="..."
 *               data-sorts='[{"key":"title","label":"Title","type":"text"}]'
 *               data-filters='[{"key":"status","label":"Status"},{"key":"date","label":"Date","range":true}]'>
 *            ... elements with data-item and data-<key>="..." attributes ...
 *          </div>
 */
(function () {
    'use strict';

    function norm(s) { return (s == null ? '' : String(s)).replace(/\s+/g, ' ').trim(); }
    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text != null) e.textContent = text;
        return e;
    }
    function blank(v) { return v === '' || v == null; }

    function compare(a, b, type) {
        if (type === 'number' || type === 'date') {
            return parseFloat(a) - parseFloat(b);
        }
        return String(a).localeCompare(String(b), undefined, { sensitivity: 'base', numeric: true });
    }

    /* Shared engine ---------------------------------------------------- */
    function engine(cfg) {
        // cfg: items[], getVal(item,key), getSearch(item), filters[], sorts[], placeholder,
        //      mountBefore (node), render(visibleSorted, allItems), onSortState(key,dir)
        var state = { q: '', f: {}, sort: '', dir: 1 };
        var controls = {};

        var bar = el('div', 'card card-body bg-light border-0 mb-3 lt-bar');
        var row = el('div', 'row g-2 align-items-end');
        bar.appendChild(row);

        function col(cls, label, node) {
            var c = el('div', cls);
            if (label) {
                var l = el('label', 'form-label small text-muted mb-1', label);
                c.appendChild(l);
            }
            c.appendChild(node);
            row.appendChild(c);
            return c;
        }

        // Search
        var search = el('input', 'form-control form-control-sm');
        search.type = 'search';
        search.placeholder = cfg.placeholder || 'Search...';
        search.setAttribute('aria-label', 'Search');
        search.addEventListener('input', function () { state.q = norm(search.value).toLowerCase(); apply(); });
        col('col-12 col-md-4', 'Search', search);

        // Filters
        cfg.filters.forEach(function (f) {
            if (f.range) {
                var wrap = el('div', 'd-flex gap-1');
                var from = el('input', 'form-control form-control-sm');
                var to = el('input', 'form-control form-control-sm');
                from.type = 'date'; to.type = 'date';
                from.title = f.label + ' from'; to.title = f.label + ' to';
                wrap.appendChild(from); wrap.appendChild(to);
                var upd = function () {
                    state.f[f.id] = {
                        from: from.value ? new Date(from.value + 'T00:00:00').getTime() / 1000 : null,
                        to: to.value ? new Date(to.value + 'T23:59:59').getTime() / 1000 : null
                    };
                    apply();
                };
                from.addEventListener('change', upd); to.addEventListener('change', upd);
                controls[f.id] = { reset: function () { from.value = ''; to.value = ''; delete state.f[f.id]; } };
                col('col-12 col-md-4 col-lg-3', f.label + ' (from - to)', wrap);
            } else {
                var sel = el('select', 'form-select form-select-sm');
                var all = el('option', null, 'All'); all.value = '';
                sel.appendChild(all);
                var seen = {};
                cfg.items.forEach(function (it) {
                    var v = norm(cfg.getFilterVal(it, f));
                    if (v !== '' && !seen[v]) { seen[v] = 1; }
                });
                Object.keys(seen).sort(function (a, b) { return compare(a, b, 'text'); }).forEach(function (v) {
                    var o = el('option', null, v); o.value = v; sel.appendChild(o);
                });
                sel.addEventListener('change', function () {
                    if (sel.value === '') delete state.f[f.id]; else state.f[f.id] = sel.value;
                    apply();
                });
                controls[f.id] = { reset: function () { sel.value = ''; delete state.f[f.id]; } };
                col('col-6 col-md-3 col-lg-2', f.label, sel);
            }
        });

        // Sort
        var sortSel = el('select', 'form-select form-select-sm');
        var def = el('option', null, 'Default order'); def.value = '';
        sortSel.appendChild(def);
        cfg.sorts.forEach(function (s) {
            var o = el('option', null, s.label); o.value = s.id; sortSel.appendChild(o);
        });
        sortSel.addEventListener('change', function () { state.sort = sortSel.value; apply(); });
        col('col-6 col-md-3 col-lg-2', 'Sort by', sortSel);

        var dirBtn = el('button', 'btn btn-sm btn-outline-secondary w-100');
        dirBtn.type = 'button';
        dirBtn.addEventListener('click', function () { state.dir = -state.dir; apply(); });
        col('col-6 col-md-2 col-lg-1', 'Order', dirBtn);

        var resetBtn = el('button', 'btn btn-sm btn-outline-danger w-100', 'Reset');
        resetBtn.type = 'button';
        resetBtn.addEventListener('click', function () {
            search.value = ''; state.q = ''; state.sort = ''; state.dir = 1; sortSel.value = '';
            Object.keys(controls).forEach(function (k) { controls[k].reset(); });
            apply();
        });
        col('col-6 col-md-2 col-lg-1', ' ', resetBtn);

        var count = el('div', 'small text-muted mt-2 lt-count');
        count.setAttribute('aria-live', 'polite');
        bar.appendChild(count);

        cfg.mountBefore.parentNode.insertBefore(bar, cfg.mountBefore);

        function matches(it) {
            if (state.q && cfg.getSearch(it).indexOf(state.q) === -1) return false;
            for (var i = 0; i < cfg.filters.length; i++) {
                var f = cfg.filters[i], want = state.f[f.id];
                if (want == null) continue;
                if (f.range) {
                    var ts = parseFloat(cfg.getVal(it, f));
                    if (isNaN(ts)) return false;
                    if (want.from != null && ts < want.from) return false;
                    if (want.to != null && ts > want.to) return false;
                } else if (norm(cfg.getFilterVal(it, f)) !== want) {
                    return false;
                }
            }
            return true;
        }

        function apply() {
            var visible = cfg.items.filter(matches);
            var s = null;
            for (var i = 0; i < cfg.sorts.length; i++) if (cfg.sorts[i].id === state.sort) s = cfg.sorts[i];
            if (s) {
                visible.sort(function (a, b) {
                    var x = cfg.getVal(a, s), y = cfg.getVal(b, s);
                    var bx = blank(x) || ((s.type === 'number' || s.type === 'date') && isNaN(parseFloat(x)));
                    var by = blank(y) || ((s.type === 'number' || s.type === 'date') && isNaN(parseFloat(y)));
                    if (bx && by) return a._i - b._i;
                    if (bx) return 1;           // blanks always last
                    if (by) return -1;
                    var r = compare(x, y, s.type) * state.dir;
                    return r !== 0 ? r : a._i - b._i;
                });
            } else {
                visible.sort(function (a, b) { return (a._i - b._i) * state.dir; });
            }
            dirBtn.textContent = state.dir === 1 ? 'Ascending' : 'Descending';
            dirBtn.title = 'Click to reverse the order';
            count.textContent = 'Showing ' + visible.length + ' of ' + cfg.items.length;
            cfg.render(visible, cfg.items);
            if (cfg.onSortState) cfg.onSortState(s ? s.id : '', state.dir);
        }

        return {
            apply: apply,
            setSort: function (id) {
                if (state.sort === id) { state.dir = -state.dir; }
                else { state.sort = id; state.dir = 1; }
                sortSel.value = state.sort;
                apply();
            }
        };
    }

    /* Tables ------------------------------------------------------------ */
    function initTable(table) {
        if (!table.tHead || !table.tBodies[0]) return;
        var tbody = table.tBodies[0];
        var ths = Array.prototype.slice.call(table.tHead.rows[0].cells);
        var rows = Array.prototype.slice.call(tbody.rows);
        rows.forEach(function (r, i) { r._i = i; });

        var cols = ths.map(function (th, idx) {
            var type = th.getAttribute('data-type') || 'text';
            return {
                idx: idx, th: th, type: type, id: 'c' + idx, label: norm(th.textContent),
                active: type !== 'none',
                filter: th.hasAttribute('data-filter'),
                filterLabel: th.getAttribute('data-filter')
            };
        });
        var sorts = cols.filter(function (c) { return c.active; });
        var filters = [];
        cols.forEach(function (c) {
            if (!c.filter) return;
            if (c.filterLabel === 'range') filters.push({ id: c.id, idx: c.idx, label: c.label, range: true, type: c.type });
            else filters.push({ id: c.id, idx: c.idx, label: c.filterLabel || c.label });
        });

        function cell(row, idx) { return row.cells[idx]; }
        function rawVal(row, c) {
            var td = cell(row, c.idx);
            if (!td) return '';
            var v = td.getAttribute('data-value');
            return v !== null ? v : norm(td.textContent);
        }

        var emptyRow = el('tr', 'lt-empty');
        var emptyTd = el('td', 'text-center text-muted py-4', 'No results match your search or filters.');
        emptyTd.colSpan = ths.length;
        emptyRow.appendChild(emptyTd);

        var anchor = table.closest('.table-responsive') || table;
        var eng = engine({
            items: rows,
            placeholder: table.getAttribute('data-search-placeholder'),
            mountBefore: anchor,
            filters: filters,
            sorts: sorts.map(function (c) { return { id: c.id, label: c.label, type: c.type, idx: c.idx }; }),
            getVal: function (row, s) { return rawVal(row, s); },
            getFilterVal: function (row, f) {
                var td = cell(row, f.idx);
                if (!td) return '';
                var v = td.getAttribute('data-filter-value');
                return v !== null ? v : rawVal(row, f);
            },
            getSearch: function (row) {
                if (row._s) return row._s;
                var parts = [];
                cols.forEach(function (c) {
                    if (!c.active) return;
                    var td = cell(row, c.idx);
                    if (!td) return;
                    parts.push(norm(td.textContent));
                    var extra = td.getAttribute('data-search');
                    if (extra) parts.push(extra);
                });
                row._s = parts.join(' ').toLowerCase();
                return row._s;
            },
            render: function (visible) {
                var nodes = visible.slice();
                if (!nodes.length) nodes.push(emptyRow);
                // replaceChildren keeps Bootstrap's zebra striping correct
                tbody.replaceChildren.apply(tbody, nodes);
            },
            onSortState: function (id, dir) {
                cols.forEach(function (c) {
                    if (!c.ind) return;
                    c.ind.textContent = c.id === id ? (dir === 1 ? ' \u25B2' : ' \u25BC') : '';
                });
            }
        });

        // Clickable headers
        sorts.forEach(function (c) {
            c.th.style.cursor = 'pointer';
            c.th.style.userSelect = 'none';
            c.th.title = 'Click to sort';
            c.ind = el('span', 'text-primary');
            c.th.appendChild(c.ind);
            c.th.addEventListener('click', function () { eng.setSort(c.id); });
        });
        eng.apply();
    }

    /* Cards ------------------------------------------------------------- */
    function initCards(root) {
        var items = Array.prototype.slice.call(root.querySelectorAll('[data-item]'));
        if (!items.length) return;
        items.forEach(function (it, i) { it._i = i; });

        var sortCfg = [], filterCfg = [];
        try { sortCfg = JSON.parse(root.getAttribute('data-sorts') || '[]'); } catch (e) {}
        try { filterCfg = JSON.parse(root.getAttribute('data-filters') || '[]'); } catch (e) {}

        var groups = [];
        items.forEach(function (it) { if (groups.indexOf(it.parentNode) === -1) groups.push(it.parentNode); });
        var empties = groups.map(function () {
            var d = el('div', 'col-12 lt-empty');
            d.appendChild(el('div', 'alert alert-info', 'No results match your search or filters.'));
            return d;
        });

        function key(k) { return k.replace(/-([a-z])/g, function (m, c) { return c.toUpperCase(); }); }
        function val(it, o) {
            var v = it.dataset[key(o.key)];
            return v == null ? '' : v;
        }

        engine({
            items: items,
            placeholder: root.getAttribute('data-search-placeholder'),
            mountBefore: root.firstElementChild,
            filters: filterCfg.map(function (f) { return { id: f.key, key: f.key, label: f.label, range: !!f.range }; }),
            sorts: sortCfg.map(function (s) { return { id: s.key, key: s.key, label: s.label, type: s.type || 'text' }; }),
            getVal: val,
            getFilterVal: val,
            getSearch: function (it) {
                if (it._s) return it._s;
                it._s = ((it.dataset.search || '') + ' ' + norm(it.textContent)).toLowerCase();
                return it._s;
            },
            render: function (visible) {
                groups.forEach(function (g, gi) {
                    var mine = visible.filter(function (it) { return it.parentNode === g; });
                    items.forEach(function (it) { if (it.parentNode === g) it.classList.add('d-none'); });
                    mine.forEach(function (it) { it.classList.remove('d-none'); g.appendChild(it); });
                    if (empties[gi].parentNode === g) g.removeChild(empties[gi]);
                    if (!mine.length) g.appendChild(empties[gi]);
                });
            }
        }).apply();
    }

    function boot() {
        Array.prototype.forEach.call(document.querySelectorAll('table[data-list-tools]'), initTable);
        Array.prototype.forEach.call(document.querySelectorAll('[data-list-cards]'), initCards);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
