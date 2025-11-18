<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/GeoApp/CSS/style.css">
</head>

<body>
    <header>
        <?php include 'HTML/header.html'; ?>
    </header>
    
    <h1 id="page-title">Welcome to GeoApp</h1>

    <div id="spline-wrapper">
        <div id="spline-container">
            <spline-viewer id="spline-viewer" url="https://prod.spline.design/8oXCdWMzJ17pHIwf/scene.splinecode"></spline-viewer>
        </div>

        <button id="enter-map">Open Map</button>
    </div>

    <!-- Overlay transition -->
    <div id="spline-overlay" aria-hidden="true"></div>
    <!-- Non-invasive visual cover to hide attribution/watermark (placed at body level so it's not affected by transforms) -->
    <div id="spline-cover" aria-hidden="true"></div>

    <!--<script type="module" src="https://unpkg.com/@splinetool/runtime@latest/build/runtime.js"></script>-->
    <script type="module" src="https://unpkg.com/@splinetool/viewer@latest/build/spline-viewer.js"></script>

    <script>
    (function () {
        const btn = document.getElementById('enter-map');
        const el = document.getElementById('spline-container');

        btn.addEventListener('click', function () {
            const rect = el.getBoundingClientRect();

            // Freeze layout: make the element fixed at its current rect
            el.style.position = 'fixed';
            el.style.top = rect.top + 'px';
            el.style.left = rect.left + 'px';
            el.style.width = rect.width + 'px';
            el.style.height = rect.height + 'px';
            el.style.margin = '0';
            el.classList.add('animating');

            // ensure transform origin is centered and set initial transform
            el.style.transformOrigin = '50% 50%';
            el.style.transform = 'translate(0px, 0px) scale(1)';

            // Force reflow so the browser registers the fixed placement
            void el.offsetWidth;

                // Compute center-based translation and scale so the container's center maps to viewport center
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                const vx = window.innerWidth / 2;
                const vy = window.innerHeight / 2;
                const scaleX = window.innerWidth / rect.width;
                const scaleY = window.innerHeight / rect.height;
                // use the larger scale so the element fully covers the viewport; add slight overscale to avoid 1px gaps
                const scaleFill = Math.max(scaleX, scaleY) * 1.05;

                // how much further to continuously scale after filling (tweakable)
                const extraFactor = 5.0;
                const scaleFinal = scaleFill * extraFactor;

                // compute translate values for both phases so center stays centered
                // With transform-origin=center, center maps to o + translate, so translate = viewportCenter - elementCenter
                const txFill = vx - cx;
                const tyFill = vy - cy;
                const txFinal = vx - cx;
                const tyFinal = vy - cy;

            // Single continuous linear transition to final scale (no pause between phases)
            // total duration = fillPhase + extraPhase (tweak as needed)
            const totalMs = 2000; // 2500ms by default
            el.style.transition = `transform ${totalMs}ms linear`;

            // Show and sync overlay fade with the transform
            const overlay = document.getElementById('spline-overlay');
            if (overlay) {
                // match overlay transition duration to totalMs
                overlay.style.transition = `opacity ${totalMs}ms linear`;
                // ensure overlay is above the container but below the highest z-index used in animating element
                overlay.classList.add('visible');
            }

            // Also apply the same transform to the non-invasive cover so it stays over the watermark
            const cover = document.getElementById('spline-cover');
            if (cover) {
                try {
                    // compute cover position (may have been set by placement routine)
                    const coverLeft = parseFloat(cover.style.left) || cover.getBoundingClientRect().left;
                    const coverTop = parseFloat(cover.style.top) || cover.getBoundingClientRect().top;
                    // set transform-origin on the cover so the same translate+scale moves it identically
                    cover.style.transformOrigin = `${cx - coverLeft}px ${cy - coverTop}px`;
                    cover.style.willChange = 'transform';
                    cover.style.transition = `transform ${totalMs}ms linear`;
                } catch (e) { console.warn('cover transform setup failed', e); }
            }

            // Animate directly to the final transform (keeps center aligned)
            requestAnimationFrame(() => {
                el.style.transform = `translate(${txFinal}px, ${tyFinal}px) scale(${scaleFinal})`;
                if (cover) {
                    try { cover.style.transform = `translate(${txFinal}px, ${tyFinal}px) scale(${scaleFinal})`; } catch (e) {}
                }
            });

            // Redirect when the (single) transition completes
            const onTransitionEnd = (e) => {
                if (e.target !== el) return;
                if (e.propertyName && e.propertyName !== 'transform') return;
                el.removeEventListener('transitionend', onTransitionEnd);
                // hide overlay fast before redirect to avoid ghosting on some browsers
                if (overlay) {
                    overlay.style.transition = 'opacity 160ms linear';
                    overlay.classList.remove('visible');
                }
                setTimeout(() => { window.location.href = 'PHP/map.php'; }, 120);
            };

            el.addEventListener('transitionend', onTransitionEnd);
        });
    })();
    </script>

    <script>
    // Position the fixed spline cover over the viewer's bottom-right corner
    (function positionSplineCover() {
        const cover = document.getElementById('spline-cover');
        console.log('positionSplineCover init', { coverExists: !!cover });
        if (!cover) return;

        function place() {
            // Prefer the container rect (more stable). Fallback to the viewer element if container missing.
            const container = document.getElementById('spline-container');
            const viewer = document.querySelector('spline-viewer');
            console.log('place invoked', { containerExists: !!container, viewerExists: !!viewer });
            let r;
            if (container) {
                r = container.getBoundingClientRect();
            } else {
                if (!viewer) { cover.style.display = 'none'; console.warn('no viewer found'); return; }
                r = viewer.getBoundingClientRect();
            }
            // normalize rect values and guard against NaN/Infinity
            const rect = {
                left: Number.isFinite(r.left) ? r.left : 0,
                top: Number.isFinite(r.top) ? r.top : 0,
                right: Number.isFinite(r.right) ? r.right : (Number.isFinite(r.left) && Number.isFinite(r.width) ? r.left + r.width : 0),
                bottom: Number.isFinite(r.bottom) ? r.bottom : (Number.isFinite(r.top) && Number.isFinite(r.height) ? r.top + r.height : 0),
                width: Number.isFinite(r.width) ? r.width : (Number.isFinite(r.right) && Number.isFinite(r.left) ? r.right - r.left : 0),
                height: Number.isFinite(r.height) ? r.height : (Number.isFinite(r.bottom) && Number.isFinite(r.top) ? r.bottom - r.top : 0)
            };
            if (!rect.width || !rect.height) {
                // fallback to container if viewer has zero height
                const container = document.getElementById('spline-container');
                if (container) {
                    const crect = container.getBoundingClientRect();
                    if (crect.width && crect.height) {
                        rect.left = crect.left; rect.top = crect.top; rect.width = crect.width; rect.height = crect.height; rect.right = crect.right; rect.bottom = crect.bottom;
                    }
                }
            }
            if (rect.width && rect.height) {
                // Size the cover proportional to the viewer so it scales with different viewer sizes
                const minW = 140, minH = 36;
                // proportion of viewer size to cover (tweakable)
                const wRatio = 0.32; // 32% of viewer width (increased to cover watermark)
                const hRatio = 0.12;  // 12% of viewer height
                let baseCoverW = Math.ceil(Math.max(minW, rect.width * wRatio));
                let baseCoverH = Math.ceil(Math.max(minH, rect.height * hRatio));

                // base padding from viewer edge (declared before it's used)
                const padding = 14;

                // oversize a bit to ensure full coverage (avoid 1px gaps).
                // Use a smaller oversize so the cover doesn't extend too far left.
                const oversize = 1.08;
                let coverW = Math.ceil(baseCoverW * oversize);
                let coverH = Math.ceil(baseCoverH * oversize);

                // Prevent the cover from extending too far left: cap its width to viewer width minus padding
                const maxCoverW = Math.max(minW, Math.floor(rect.width - (padding * 2)));
                const maxCoverH = Math.max(minH, Math.floor(rect.height - (padding * 2)));
                const cappedW = coverW > maxCoverW;
                const cappedH = coverH > maxCoverH;
                if (cappedW) coverW = maxCoverW;
                if (cappedH) coverH = maxCoverH;

                // apply size
                cover.style.width = coverW + 'px';
                cover.style.height = coverH + 'px';

                // desired positions: align cover flush to the viewer's right edge (minus padding)
                // previous code subtracted an extra 'inwardShift' which moved the cover leftwards
                // and could leave the watermark exposed. Aligning to the right edge is more robust.
                let left = Math.floor(rect.right - coverW - padding);
                let top = Math.floor(rect.bottom - coverH - padding);

                // clamp into viewport
                const maxLeft = Math.max(8, window.innerWidth - coverW - 8);
                const maxTop = Math.max(8, window.innerHeight - coverH - 8);
                if (!Number.isFinite(left) || left < 0) left = 8;
                if (!Number.isFinite(top) || top < 0) top = 8;
                left = Math.min(Math.max(8, left), maxLeft);
                top = Math.min(Math.max(8, top), maxTop);
                // diagnostics: include cover right edge and viewer right for quick visual diff
                try {
                    console.debug('spline-cover place:', { left, top, coverW, coverH, coverRight: left + coverW, viewerRight: rect.right, maxCoverW, maxCoverH, cappedW, cappedH, rect });
                } catch (e) {}
                cover.style.left = left + 'px';
                cover.style.top = top + 'px';
                cover.style.display = 'block';
            } else {
                cover.style.display = 'none';
            }
        }

        // Try a few times while the viewer initializes
        let tries = 0;
        const ti = setInterval(() => {
            tries++;
            try { place(); } catch (e) { console.error('place error', e); }
            if (tries > 12) clearInterval(ti);
        }, 300);

        // reposition immediately (and on next frame) and on resize/scroll
        try { place(); } catch (e) { console.error(e); }
        requestAnimationFrame(() => { try { place(); } catch (e) { console.error(e); } });
        window.addEventListener('resize', place);
        window.addEventListener('scroll', place, true);
    })();
    </script>

    <!-- Diagnostic: recurse shadow DOM and outline elements to locate branding -->
    <script>
    (function debugSplineStructure() {
        function summarizeNode(node) {
            if (!node) return null;
            const obj = {};
            obj.tag = node.tagName || (node.nodeType === Node.TEXT_NODE ? '#text' : node.nodeName);
            if (node.id) obj.id = node.id;
            if (node.className) obj.class = node.className;
            if (node.getAttribute) {
                const aria = node.getAttribute('aria-label');
                if (aria) obj.aria = aria;
            }
            if (node.textContent) {
                const txt = node.textContent.trim();
                if (txt.length > 0) obj.text = txt.slice(0, 80);
            }
            if (node instanceof HTMLCanvasElement) obj.canvas = true;
            return obj;
        }

        function walk(root, depth = 0, path = '') {
            const list = [];
            if (!root) return list;
            const children = root.children || [];
            for (let i = 0; i < children.length; i++) {
                const el = children[i];
                const p = path + '/' + (el.tagName || el.nodeName) + (el.id ? ('#' + el.id) : '') + (el.className ? ('.' + el.className.split(' ').join('.')) : '');
                const info = summarizeNode(el) || {};
                info.path = p;
                info.depth = depth;
                list.push(info);
                // visually outline element to help locate it on screen
                try { el.style.outline = '1px solid rgba(255,0,0,0.35)'; } catch (e) {}
                // if element has shadowRoot, walk into it too
                if (el.shadowRoot) {
                    list.push({ tag: '::shadow-root', path: p + '::shadow', depth: depth + 1 });
                    try {
                        Array.prototype.push.apply(list, walk(el.shadowRoot, depth + 1, p + '::shadow'));
                    } catch (e) {}
                }
                // recurse into normal children
                try { Array.prototype.push.apply(list, walk(el, depth + 1, p)); } catch (e) {}
            }
            return list;
        }

        function dumpViewer() {
            const viewer = document.querySelector('spline-viewer');
            if (!viewer) { console.warn('No <spline-viewer> found'); return; }
            console.groupCollapsed('spline-viewer debug');
            try {
                if (!viewer.shadowRoot) {
                    console.log('viewer has no shadowRoot yet');
                } else {
                    console.log('viewer.shadowRoot:', viewer.shadowRoot);
                    const nodes = walk(viewer.shadowRoot, 0, 'spline-viewer');
                    console.table(nodes.map(n => ({ path: n.path, tag: n.tag, id: n.id || '', class: n.class || '', aria: n.aria || '', text: n.text || '', canvas: n.canvas || '' }))); 
                }
            } catch (e) { console.error(e); }
            console.groupEnd();
        }

        // Try multiple times while the viewer initializes
        let tries = 0;
        const ti = setInterval(() => {
            tries++;
            dumpViewer();
            if (tries > 8) clearInterval(ti);
        }, 500);
    })();
    </script>

    <script>
    (function removeSplineBranding() {
        function looksLikeLogo(el) {
            try {
                const id = (el.id || '').toLowerCase();
                const cls = (el.className || '').toString().toLowerCase();
                const aria = (el.getAttribute && (el.getAttribute('aria-label') || '')).toLowerCase();
                const txt = (el.textContent || '').toLowerCase();
                if (!el) return false;
                if (id.includes('logo') || cls.includes('logo')) return true;
                if (aria.includes('spline') || aria.includes('logo')) return true;
                if (txt.includes('made with spline') || txt.includes('spline')) return true;
                return false;
            } catch (e) { return false; }
        }

        function scanAndRemove(root) {
            if (!root) return false;
            let removed = false;
            // remove matching direct descendants
            try {
                Array.from(root.querySelectorAll('*')).forEach(el => {
                    if (looksLikeLogo(el)) {
                        el.remove();
                        removed = true;
                    }
                });
            } catch (e) { /* ignore shadow query errors */ }

            // recursively scan nested shadowRoots
            try {
                Array.from(root.querySelectorAll('*')).forEach(el => {
                    if (el && el.shadowRoot) {
                        if (scanAndRemove(el.shadowRoot)) removed = true;
                    }
                });
            } catch (e) { /* ignore */ }

            return removed;
        }

        function attachObserver(root) {
            if (!root || root.__logoObserverAttached) return;
            const obs = new MutationObserver(mutations => {
                for (const m of mutations) {
                    if (m.addedNodes && m.addedNodes.length) {
                        // try quick removal on added nodes
                        scanAndRemove(root);
                    }
                }
            });
            obs.observe(root, { childList: true, subtree: true });
            root.__logoObserverAttached = true;
        }

        function trySetup() {
            const viewer = document.querySelector('spline-viewer');
            if (!viewer) return setTimeout(trySetup, 300);
            if (!viewer.shadowRoot) return setTimeout(trySetup, 300);

            // initial pass
            scanAndRemove(viewer.shadowRoot);
            attachObserver(viewer.shadowRoot);

            // attempt to find and attach to nested shadowRoots for a short period
            const nestedScanInterval = setInterval(() => {
                let found = false;
                try {
                    Array.from(viewer.shadowRoot.querySelectorAll('*')).forEach(el => {
                        if (el && el.shadowRoot) {
                            attachObserver(el.shadowRoot);
                            scanAndRemove(el.shadowRoot);
                            found = true;
                        }
                    });
                } catch (e) { /* ignore */ }
                // stop scanning after a few attempts once we found nested roots
                if (found) clearInterval(nestedScanInterval);
            }, 500);
        }

        trySetup();
    })();
    </script>
</body>

</html>