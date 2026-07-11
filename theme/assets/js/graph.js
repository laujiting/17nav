/**
 * 17Nav 知识图谱可视化
 * 使用 D3.js force-directed graph
 */
(function() {
    'use strict';

    var btn = document.getElementById('graph-btn');
    if (!btn) return;

    // 由后端注入
    var bookmarks = window.NAV_BOOKMARKS || [];
    var categories = window.NAV_CATEGORIES || [];

    // 颜色映射
    var colors = ['#a5b4fc', '#fbbf24', '#5eead4', '#f87171', '#34d399', '#fb923c', '#c084fc', '#60a5fa'];

    function buildGraph() {
        // 构建节点和边
        var nodes = bookmarks.map(function(b, i) {
            return {
                id: i,
                name: b.name,
                url: b.url,
                desc: b.desc || '',
                category: b.category || '其他',
                tags: b.tags || []
            };
        });

        var edges = [];
        // 同分类的连边
        for (var i = 0; i < nodes.length; i++) {
            for (var j = i + 1; j < nodes.length; j++) {
                if (nodes[i].category === nodes[j].category) {
                    edges.push({source: i, target: j, type: 'category'});
                }
                // 同标签连边
                var commonTags = nodes[i].tags.filter(function(t) {
                    return nodes[j].tags.indexOf(t) >= 0;
                });
                if (commonTags.length > 0) {
                    edges.push({source: i, target: j, type: 'tag'});
                }
            }
        }

        return {nodes: nodes, edges: edges};
    }

    function showGraph() {
        // 动态加载 D3.js
        if (typeof d3 === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js';
            script.onload = renderGraph;
            document.head.appendChild(script);
        } else {
            renderGraph();
        }
    }

    function renderGraph() {
        // 关闭已有的
        var existing = document.getElementById('graph-modal');
        if (existing) existing.remove();

        var data = buildGraph();

        // 弹窗
        var modal = document.createElement('div');
        modal.id = 'graph-modal';
        modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;' +
            'background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);' +
            'display:flex;align-items:center;justify-content:center;animation:fadeIn 0.3s ease;';

        var content = document.createElement('div');
        content.style.cssText = 'position:relative;width:90vw;height:85vh;background:var(--container-bg);' +
            'border-radius:1.5rem;overflow:hidden;transform:scale(0.9);' +
            'animation:scaleIn 0.3s ease forwards;';

        var closeBtn = document.createElement('button');
        closeBtn.textContent = '✕';
        closeBtn.style.cssText = 'position:absolute;top:1rem;right:1rem;z-index:10;' +
            'width:36px;height:36px;border-radius:50%;border:none;cursor:pointer;' +
            'background:var(--card-bg);color:var(--text);font-size:1rem;';
        closeBtn.onclick = function() { modal.remove(); };

        content.appendChild(closeBtn);
        modal.appendChild(content);
        document.body.appendChild(modal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.remove();
        });

        // D3 力导向图
        var width = content.clientWidth;
        var height = content.clientHeight;

        var svg = d3.select(content).append('svg')
            .attr('width', width).attr('height', height);

        var g = svg.append('g');

        // 缩放
        svg.call(d3.zoom().scaleExtent([0.5, 3]).on('zoom', function(e) {
            g.attr('transform', e.transform);
        }));

        // 力模拟
        var sim = d3.forceSimulation(data.nodes)
            .force('link', d3.forceLink(data.edges).id(function(d) { return d.id; })
                .distance(80).strength(0.1))
            .force('charge', d3.forceManyBody().strength(-200))
            .force('center', d3.forceCenter(width / 2, height / 2))
            .force('collision', d3.forceCollide().radius(25));

        // 边
        var link = g.append('g').selectAll('line')
            .data(data.edges).enter().append('line')
            .attr('stroke', function(d) {
                return d.type === 'tag' ? 'rgba(165,180,252,0.3)' : 'rgba(148,163,184,0.15)';
            })
            .attr('stroke-width', 1)
            .style('opacity', 0)
            .transition().delay(500).duration(500).style('opacity', 1);

        // 节点
        var node = g.append('g').selectAll('circle')
            .data(data.nodes).enter().append('circle')
            .attr('r', 8)
            .attr('fill', function(d) {
                var idx = categories.indexOf(d.category);
                return colors[idx % colors.length] || '#a5b4fc';
            })
            .style('cursor', 'pointer')
            .call(d3.drag()
                .on('start', dragStart)
                .on('drag', dragging)
                .on('end', dragEnd));

        // 节点标签
        var label = g.append('g').selectAll('text')
            .data(data.nodes).enter().append('text')
            .text(function(d) { return d.name; })
            .attr('font-size', '10px')
            .attr('fill', 'var(--text)')
            .attr('dx', 12).attr('dy', 4)
            .style('pointer-events', 'none');

        // tooltip
        node.append('title').text(function(d) {
            return d.name + (d.desc ? '\n' + d.desc : '');
        });

        // 点击跳转
        node.on('click', function(e, d) {
            window.open(d.url, '_blank');
        });

        sim.on('tick', function() {
            link.attr('x1', function(d) { return d.source.x; })
                .attr('y1', function(d) { return d.source.y; })
                .attr('x2', function(d) { return d.target.x; })
                .attr('y2', function(d) { return d.target.y; });
            node.attr('cx', function(d) { return d.x; })
                .attr('cy', function(d) { return d.y; });
            label.attr('x', function(d) { return d.x; })
                .attr('y', function(d) { return d.y; });
        });

        function dragStart(e, d) {
            if (!e.active) sim.alphaTarget(0.3).restart();
            d.fx = d.x; d.fy = d.y;
        }
        function dragging(e, d) { d.fx = e.x; d.fy = e.y; }
        function dragEnd(e, d) {
            if (!e.active) sim.alphaTarget(0);
            d.fx = null; d.fy = null;
        }

        // 添加动画 keyframes
        var style = document.createElement('style');
        style.textContent = '@keyframes scaleIn{from{transform:scale(0.9);opacity:0}to{transform:scale(1);opacity:1}}';
        document.head.appendChild(style);
    }

    btn.addEventListener('click', showGraph);
})();
