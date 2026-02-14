/* ==========================================================================
   SIGNATURE CANVAS — EST Fkih Ben Salah
   Mouse + Touch support for jury and coordinator signatures
   ========================================================================== */

(function() {
    'use strict';

    var canvases = [];
    var contexts = [];
    var isDrawing = false;
    var lastX = 0;
    var lastY = 0;

    // ===== Helper: Get coordinates from mouse or touch event =====
    function getCoords(canvas, e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;

        if (e.touches && e.touches.length > 0) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        }
        return {
            x: e.offsetX,
            y: e.offsetY
        };
    }

    // ===== Jury Signature Canvases =====
    for (var i = 0; i < jury_num; i++) {
        var canvas = document.getElementById('signatureCanvas' + i);
        if (!canvas) continue;

        var context = canvas.getContext('2d');
        context.strokeStyle = '#1a1d23';
        context.lineWidth = 1.5;
        context.lineCap = 'round';
        context.lineJoin = 'round';

        canvases.push(canvas);
        contexts.push(context);

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing.bind(null, i));
        canvas.addEventListener('mousemove', draw.bind(null, i));
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events
        canvas.addEventListener('touchstart', handleTouchStart.bind(null, i), { passive: false });
        canvas.addEventListener('touchmove', handleTouchMove.bind(null, i), { passive: false });
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchcancel', stopDrawing);
    }

    function startDrawing(index, e) {
        isDrawing = true;
        var coords = getCoords(canvases[index], e);
        lastX = coords.x;
        lastY = coords.y;
    }

    function draw(index, e) {
        if (!isDrawing) return;
        var coords = getCoords(canvases[index], e);
        contexts[index].beginPath();
        contexts[index].moveTo(lastX, lastY);
        contexts[index].lineTo(coords.x, coords.y);
        contexts[index].stroke();
        lastX = coords.x;
        lastY = coords.y;
    }

    function stopDrawing() {
        isDrawing = false;
    }

    function handleTouchStart(index, e) {
        e.preventDefault();
        var coords = getCoords(canvases[index], e);
        isDrawing = true;
        lastX = coords.x;
        lastY = coords.y;
    }

    function handleTouchMove(index, e) {
        e.preventDefault();
        if (!isDrawing) return;
        var coords = getCoords(canvases[index], e);
        contexts[index].beginPath();
        contexts[index].moveTo(lastX, lastY);
        contexts[index].lineTo(coords.x, coords.y);
        contexts[index].stroke();
        lastX = coords.x;
        lastY = coords.y;
    }

    // Expose clearSignature globally
    window.clearSignature = function(index) {
        if (contexts[index] && canvases[index]) {
            contexts[index].clearRect(0, 0, canvases[index].width, canvases[index].height);
        }
    };

    // ===== Coordinator Signature Canvas =====
    var coordCanvas = document.getElementById('coordinatorSignatureCanvas');
    if (coordCanvas) {
        var coordCtx = coordCanvas.getContext('2d');
        coordCtx.strokeStyle = '#1a1d23';
        coordCtx.lineWidth = 1.5;
        coordCtx.lineCap = 'round';
        coordCtx.lineJoin = 'round';

        var coordDrawing = false;
        var coordLastX = 0;
        var coordLastY = 0;

        function startCoord(e) {
            coordDrawing = true;
            var coords = getCoords(coordCanvas, e);
            coordLastX = coords.x;
            coordLastY = coords.y;
        }

        function drawCoord(e) {
            if (!coordDrawing) return;
            var coords = getCoords(coordCanvas, e);
            coordCtx.beginPath();
            coordCtx.moveTo(coordLastX, coordLastY);
            coordCtx.lineTo(coords.x, coords.y);
            coordCtx.stroke();
            coordLastX = coords.x;
            coordLastY = coords.y;
        }

        function stopCoord() {
            coordDrawing = false;
        }

        function touchStartCoord(e) {
            e.preventDefault();
            startCoord(e);
        }

        function touchMoveCoord(e) {
            e.preventDefault();
            drawCoord(e);
        }

        // Mouse events
        coordCanvas.addEventListener('mousedown', startCoord);
        coordCanvas.addEventListener('mousemove', drawCoord);
        coordCanvas.addEventListener('mouseup', stopCoord);
        coordCanvas.addEventListener('mouseout', stopCoord);

        // Touch events
        coordCanvas.addEventListener('touchstart', touchStartCoord, { passive: false });
        coordCanvas.addEventListener('touchmove', touchMoveCoord, { passive: false });
        coordCanvas.addEventListener('touchend', stopCoord);
        coordCanvas.addEventListener('touchcancel', stopCoord);

        window.clearCoordinatorSignature = function() {
            coordCtx.clearRect(0, 0, coordCanvas.width, coordCanvas.height);
        };
    }

})();
