<x-app-layout>
    <div class="min-h-screen" style="background-color: var(--portal-bg);">
        <div class="mx-auto max-w-7xl px-6 py-10">
            <div class="grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
                <section class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: var(--portal-primary);">Eksplorasi Pengetahuan</p>
                    <h1 class="mt-3 text-3xl font-extrabold" style="color: var(--portal-primary-deep);">Basis Pengetahuan</h1>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        Jelajahi keterkaitan antar topik, dokumen, regulasi, dan sumber pengetahuan SPBE dalam tampilan graph.
                    </p>

                    @php($statusMessage = $portalSettings[\App\Models\Setting::KNOWLEDGE_BASE_STATUS] ?? 'Menunggu aktivasi integrasi basis pengetahuan lanjutan.')

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-bold text-slate-800">Status integrasi</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $statusMessage }}</p>
                    </div>

                    <div id="knowledge-filter-list" class="mt-5 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <button type="button" data-graph-filter="all" class="knowledge-filter rounded-2xl border border-slate-900 bg-slate-900 px-4 py-3 text-left text-xs font-bold text-white transition">
                            Semua relasi
                        </button>
                    </div>

                    <dl class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <dt class="text-xs font-semibold text-slate-500">Node</dt>
                            <dd id="knowledge-node-count" class="mt-1 text-2xl font-extrabold text-slate-900">0</dd>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <dt class="text-xs font-semibold text-slate-500">Relasi</dt>
                            <dd id="knowledge-edge-count" class="mt-1 text-2xl font-extrabold text-slate-900">0</dd>
                        </div>
                    </dl>
                </section>

                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Graph Pengetahuan SPBE</p>
                            <p id="knowledge-graph-status" class="mt-1 text-xs leading-5 text-slate-500">Menyiapkan visualisasi.</p>
                        </div>
                        <button type="button" id="knowledge-graph-retry" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                            Muat ulang
                        </button>
                    </div>

                    <div
                        id="knowledge-graph"
                        data-api-url="{{ $knowledgeGraphApiUrl }}"
                        class="relative min-h-[520px] bg-slate-50"
                    >
                        <svg id="knowledge-graph-svg" class="h-[520px] w-full" role="img" aria-label="Visualisasi graph basis pengetahuan"></svg>

                        <div id="knowledge-graph-empty" class="absolute inset-0 hidden items-center justify-center p-8 text-center">
                            <div class="max-w-sm rounded-2xl border border-dashed border-slate-300 bg-white/90 p-6">
                                <p class="text-sm font-bold text-slate-800">Graph belum tersedia</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Endpoint knowledge graph belum dikonfigurasi atau belum mengembalikan data publik.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const container = document.getElementById('knowledge-graph');
            const svg = document.getElementById('knowledge-graph-svg');
            const emptyState = document.getElementById('knowledge-graph-empty');
            const status = document.getElementById('knowledge-graph-status');
            const retry = document.getElementById('knowledge-graph-retry');
            const nodeCount = document.getElementById('knowledge-node-count');
            const edgeCount = document.getElementById('knowledge-edge-count');
            const filterList = document.getElementById('knowledge-filter-list');

            if (!container || !svg) {
                return;
            }

            const apiUrl = container.dataset.apiUrl;
            const sampleGraph = {
                nodes: [
                    { id: 'spbe', label: 'SPBE', type: 'topic', summary: 'Sistem Pemerintahan Berbasis Elektronik' },
                    { id: 'arsitektur-spbe', label: 'Arsitektur SPBE', type: 'topic', summary: 'Kerangka layanan dan proses digital' },
                    { id: 'regulasi', label: 'Regulasi', type: 'regulasi', summary: 'Dasar hukum dan pedoman pelaksanaan' },
                    { id: 'layanan-digital', label: 'Layanan Digital', type: 'layanan', summary: 'Layanan pemerintahan berbasis digital' },
                    { id: 'audit', label: 'Audit SPBE', type: 'indikator', summary: 'Evaluasi dan pengendalian tata kelola' },
                    { id: 'instansi', label: 'Instansi', type: 'instansi', summary: 'Unit pengelola layanan dan pengetahuan' },
                ],
                edges: [
                    { id: 'edge-1', source: 'spbe', target: 'arsitektur-spbe', label: 'memiliki' },
                    { id: 'edge-2', source: 'spbe', target: 'regulasi', label: 'diatur oleh' },
                    { id: 'edge-3', source: 'arsitektur-spbe', target: 'layanan-digital', label: 'mengarahkan' },
                    { id: 'edge-4', source: 'regulasi', target: 'audit', label: 'mendukung' },
                    { id: 'edge-5', source: 'audit', target: 'spbe', label: 'mengevaluasi' },
                    { id: 'edge-6', source: 'instansi', target: 'layanan-digital', label: 'mengelola' },
                ],
                meta: { visibility: 'sample' },
            };

            let currentGraph = sampleGraph;
            let activeFilter = 'all';

            const colorByType = {
                topic: '#0369a1',
                regulasi: '#047857',
                layanan: '#7c3aed',
                indikator: '#be123c',
                instansi: '#b45309',
                other: '#475569',
                default: '#334155',
            };

            const setStatus = (message) => {
                status.textContent = message;
            };

            const normalizeType = (type) => {
                const normalized = String(type || '').trim().toLowerCase();

                return normalized || 'other';
            };

            const labelFromType = (type) => {
                if (type === 'all') {
                    return 'Semua relasi';
                }

                if (type === 'other') {
                    return 'Lainnya';
                }

                return type
                    .replace(/[-_]+/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim()
                    .replace(/\b\w/g, (letter) => letter.toUpperCase());
            };

            const getFilterButtons = () => Array.from(document.querySelectorAll('.knowledge-filter'));

            const setFilterButtonState = () => {
                getFilterButtons().forEach((button) => {
                    const active = button.dataset.graphFilter === activeFilter;
                    button.classList.toggle('border-slate-900', active);
                    button.classList.toggle('bg-slate-900', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('border-slate-200', !active);
                    button.classList.toggle('bg-white', !active);
                    button.classList.toggle('text-slate-700', !active);
                });
            };

            const renderFilters = () => {
                if (!filterList) {
                    return;
                }

                const graph = normalizeGraph(currentGraph);
                const types = Array.from(new Set(graph.nodes.map((node) => normalizeType(node.type)))).sort();
                const activeExists = activeFilter === 'all' || types.includes(activeFilter);

                if (!activeExists) {
                    activeFilter = 'all';
                }

                filterList.innerHTML = '';

                ['all', ...types].forEach((type) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.dataset.graphFilter = type;
                    button.className = 'knowledge-filter rounded-2xl border px-4 py-3 text-left text-xs font-bold transition hover:border-slate-300';
                    button.textContent = labelFromType(type);
                    button.addEventListener('click', () => {
                        activeFilter = type;
                        setFilterButtonState();
                        renderGraph();
                    });
                    filterList.appendChild(button);
                });

                setFilterButtonState();
            };

            const normalizeGraph = (graph) => {
                const nodes = Array.isArray(graph?.nodes)
                    ? graph.nodes.map((node) => ({
                        ...node,
                        type: normalizeType(node.type),
                    }))
                    : [];
                const nodeIds = new Set(nodes.map((node) => node.id));
                const edges = Array.isArray(graph?.edges)
                    ? graph.edges.filter((edge) => nodeIds.has(edge.source) && nodeIds.has(edge.target))
                    : [];

                return { nodes, edges, meta: graph?.meta ?? {} };
            };

            const splitLabel = (label) => {
                const words = String(label || '').split(' ');

                if (words.length <= 1) {
                    return [words[0] || 'Node'];
                }

                const lines = [];
                let line = '';

                words.forEach((word) => {
                    const candidate = line ? `${line} ${word}` : word;

                    if (candidate.length > 12 && line) {
                        lines.push(line);
                        line = word;
                    } else {
                        line = candidate;
                    }
                });

                if (line) {
                    lines.push(line);
                }

                return lines.slice(0, 2);
            };

            const filteredGraph = () => {
                const graph = normalizeGraph(currentGraph);

                if (activeFilter === 'all') {
                    return graph;
                }

                const nodes = graph.nodes.filter((node) => normalizeType(node.type) === activeFilter);
                const ids = new Set(nodes.map((node) => node.id));
                const edges = graph.edges.filter((edge) => ids.has(edge.source) || ids.has(edge.target));
                const relatedIds = new Set(nodes.map((node) => node.id));

                edges.forEach((edge) => {
                    relatedIds.add(edge.source);
                    relatedIds.add(edge.target);
                });

                return {
                    ...graph,
                    nodes: graph.nodes.filter((node) => relatedIds.has(node.id)),
                    edges,
                };
            };

            const renderGraph = () => {
                const graph = filteredGraph();
                const width = Math.max(svg.clientWidth, 720);
                const height = 520;
                const centerX = width / 2;
                const centerY = height / 2;
                const radius = Math.min(width, height) * 0.34;
                const positions = new Map();

                svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
                svg.innerHTML = '';

                nodeCount.textContent = graph.nodes.length;
                edgeCount.textContent = graph.edges.length;
                emptyState.classList.toggle('hidden', graph.nodes.length > 0);
                emptyState.classList.toggle('flex', graph.nodes.length === 0);

                if (graph.nodes.length === 0) {
                    return;
                }

                graph.nodes.forEach((node, index) => {
                    const angle = graph.nodes.length === 1 ? 0 : (Math.PI * 2 * index) / graph.nodes.length - Math.PI / 2;
                    positions.set(node.id, {
                        x: centerX + Math.cos(angle) * radius,
                        y: centerY + Math.sin(angle) * radius,
                    });
                });

                graph.edges.forEach((edge) => {
                    const source = positions.get(edge.source);
                    const target = positions.get(edge.target);

                    if (!source || !target) {
                        return;
                    }

                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', source.x);
                    line.setAttribute('y1', source.y);
                    line.setAttribute('x2', target.x);
                    line.setAttribute('y2', target.y);
                    line.setAttribute('stroke', '#cbd5e1');
                    line.setAttribute('stroke-width', '2');
                    svg.appendChild(line);

                    const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    label.setAttribute('x', (source.x + target.x) / 2);
                    label.setAttribute('y', (source.y + target.y) / 2 - 8);
                    label.setAttribute('text-anchor', 'middle');
                    label.setAttribute('font-size', '11');
                    label.setAttribute('font-weight', '700');
                    label.setAttribute('fill', '#64748b');
                    label.textContent = edge.label || 'terkait';
                    svg.appendChild(label);
                });

                graph.nodes.forEach((node) => {
                    const position = positions.get(node.id);
                    const color = colorByType[normalizeType(node.type)] || colorByType.default;

                    const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    group.setAttribute('tabindex', '0');

                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', position.x);
                    circle.setAttribute('cy', position.y);
                    circle.setAttribute('r', '42');
                    circle.setAttribute('fill', color);
                    circle.setAttribute('opacity', '0.95');
                    group.appendChild(circle);

                    const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
                    title.textContent = node.summary || node.label || node.id;
                    group.appendChild(title);

                    const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    label.setAttribute('x', position.x);
                    label.setAttribute('y', position.y - 4);
                    label.setAttribute('text-anchor', 'middle');
                    label.setAttribute('font-size', '12');
                    label.setAttribute('font-weight', '800');
                    label.setAttribute('fill', '#ffffff');

                    splitLabel(node.label || node.id).forEach((line, index, lines) => {
                        const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
                        tspan.setAttribute('x', position.x);
                        tspan.setAttribute('dy', index === 0 ? (lines.length === 1 ? '0.35em' : '0') : '1.15em');
                        tspan.textContent = line;
                        label.appendChild(tspan);
                    });

                    group.appendChild(label);

                    svg.appendChild(group);
                });
            };

            const loadGraph = async () => {
                if (!apiUrl) {
                    currentGraph = sampleGraph;
                    setStatus('Mode contoh aktif. Endpoint knowledge graph belum dikonfigurasi.');
                    renderFilters();
                    renderGraph();
                    return;
                }

                setStatus('Mengambil data graph publik.');

                try {
                    const response = await fetch(apiUrl, {
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok) {
                        throw new Error('Graph API request failed.');
                    }

                    currentGraph = normalizeGraph(await response.json());
                    setStatus(currentGraph.nodes.length > 0
                        ? 'Data graph publik berhasil dimuat.'
                        : 'Endpoint tersedia, tetapi graph publik masih kosong.');
                } catch (error) {
                    currentGraph = { nodes: [], edges: [], meta: {} };
                    setStatus('Graph belum dapat dimuat. Coba beberapa saat lagi.');
                }

                renderFilters();
                renderGraph();
            };

            retry.addEventListener('click', loadGraph);
            window.addEventListener('resize', renderGraph);

            loadGraph();
        })();
    </script>
</x-app-layout>
