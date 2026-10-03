<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Get parameters
$attachment_id = absint( $_GET['doc'] ?? 0 );
$course_id     = absint( $_GET['course'] ?? 0 );
$nonce         = sanitize_text_field( $_GET['nonce'] ?? '' );

if ( ! $attachment_id || ! $course_id || ! wp_verify_nonce( $nonce, 'rima_nonce' ) ) {
    wp_die( 'Invalid request.' );
}

// Generate the URL for the actual PDF binary data
$pdf_data_url = add_query_arg( array(
    'action' => 'rima_view_pdf_data', // We will rename the old one to _data
    'doc'    => $attachment_id,
    'course' => $course_id,
    'nonce'  => wp_create_nonce('rima_nonce'),
), admin_url('admin-ajax.php') );

// AJAX URL for tracking
$ajax_url = admin_url('admin-ajax.php');

$current_user = wp_get_current_user();
$watermark_text = esc_attr( $current_user->display_name . ' (' . $current_user->user_email . ')' );
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer - RIMA Academy</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden; /* We handle scrolling internally if needed, or PDF.js handles it */
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        /* Prevent selection */
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        #toolbar {
            background-color: #222;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5);
            z-index: 10;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        button {
            background-color: #444;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button:hover:not(:disabled) {
            background-color: #555;
        }

        button:disabled {
            background-color: #333;
            color: #777;
            cursor: not-allowed;
        }

        #page-info {
            font-size: 14px;
            background: #111;
            padding: 5px 10px;
            border-radius: 4px;
        }

        #timer-info {
            font-size: 13px;
            color: #ff9800;
            font-weight: bold;
            min-width: 150px;
            text-align: right;
        }

        #viewer-container {
            flex: 1;
            overflow: auto;
            display: flex;
            justify-content: center;
            background-color: #525659;
            position: relative;
            padding: 20px;
        }

        #pdf-render-wrapper {
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            background: #fff;
        }

        /* The canvas where PDF is drawn */
        #pdf-canvas {
            display: block;
        }

        /* Overlay to prevent right click on canvas and add watermark */
        #pdf-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 5;
            pointer-events: none; /* Let clicks pass through if we had links, but we just want to protect */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        /* Divs inside overlay to catch right clicks just in case */
        .click-shield {
            position: absolute;
            inset: 0;
            z-index: 6;
            pointer-events: auto;
        }

        .watermark-text {
            color: rgba(150, 150, 150, 0.2);
            font-size: 24px;
            transform: rotate(-45deg);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            width: 200%;
            text-align: center;
        }
        
        #loading-bar {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 18px;
        }
    </style>
</head>
<body oncontextmenu="return false;">

    <div id="toolbar">
        <div class="toolbar-group">
            <button id="prev-page" disabled><i class="fa fa-chevron-left"></i> Înapoi</button>
            <span id="page-info">Pagina <span id="page-num">0</span> / <span id="page-count">0</span></span>
            <button id="next-page" disabled>Înainte <i class="fa fa-chevron-right"></i></button>
        </div>
        <div class="toolbar-group">
            <button id="zoom-out"><i class="fa fa-search-minus"></i></button>
            <button id="zoom-in"><i class="fa fa-search-plus"></i></button>
            <span id="timer-info">Se încarcă...</span>
        </div>
    </div>

    <div id="viewer-container">
        <div id="loading-bar">Se încarcă documentul securizat...</div>
        <div id="pdf-render-wrapper" style="display:none;">
            <canvas id="pdf-canvas"></canvas>
            <div id="pdf-overlay">
                <div class="click-shield"></div>
                <!-- Multiple watermarks -->
                <div style="position:absolute; width:100%; height:100%; display:flex; flex-direction:column; justify-content:space-around; align-items:center;">
                    <div class="watermark-text"><?php echo $watermark_text; ?></div>
                    <div class="watermark-text"><?php echo $watermark_text; ?></div>
                    <div class="watermark-text"><?php echo $watermark_text; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const pdfDataUrl = "<?php echo $pdf_data_url; ?>";
        const ajaxUrl = "<?php echo $ajax_url; ?>";
        const courseId = <?php echo $course_id; ?>;
        const docId = <?php echo $attachment_id; ?>;
        
        let pdfDoc = null,
            pageNum = 1,
            pageIsRendering = false,
            pageNumIsPending = null,
            scale = 1.2,
            canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d');

        // Anti-Cheat & Timing
        const MIN_TIME_PER_PAGE = 30; // 30 seconds per page
        let pageTimer = 0;
        let pageInterval = null;
        let globalStudyTime = 0;
        let globalTimerInterval = null;
        let isWindowFocused = true;
        let highestPageReached = 1;

        // Elements
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        const timerInfo = document.getElementById('timer-info');
        const renderWrapper = document.getElementById('pdf-render-wrapper');
        const loadingBar = document.getElementById('loading-bar');

        // Block keyboard shortcuts (Save, Print, Inspect)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'c' || e.key === 'u')) {
                e.preventDefault();
            }
            if (e.key === 'F12') e.preventDefault();
        });

        // Focus Tracking for Global Timer
        window.addEventListener('focus', () => { isWindowFocused = true; });
        window.addEventListener('blur', () => { isWindowFocused = false; });
        
        // Handle window resize to fit PDF
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (pdfDoc && !pageIsRendering) {
                    calculateScale();
                    queueRenderPage(pageNum);
                }
            }, 300);
        });

        const calculateScale = () => {
            if (!pdfDoc) return;
            pdfDoc.getPage(pageNum).then(page => {
                const unscaledViewport = page.getViewport({ scale: 1 });
                const containerWidth = document.getElementById('viewer-container').clientWidth - 40; // minus padding
                if (unscaledViewport.width > 0) {
                    // Fit to width, up to a max scale
                    const newScale = containerWidth / unscaledViewport.width;
                    scale = Math.min(newScale, 2.5); // cap at 2.5 for performance
                }
            });
        };

        // Render the page
        const renderPage = num => {
            pageIsRendering = true;
            
            // Fetch page
            pdfDoc.getPage(num).then(page => {
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderCtx = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                page.render(renderCtx).promise.then(() => {
                    pageIsRendering = false;

                    if (pageNumIsPending !== null) {
                        renderPage(pageNumIsPending);
                        pageNumIsPending = null;
                    }
                });

                document.getElementById('page-num').textContent = num;
                
                // Track highest page reached
                if (num > highestPageReached) {
                    highestPageReached = num;
                }

                // Reset page timer for new pages
                startPageTimer(num);
                
                // Save progress
                saveProgress(num);
            });
        };

        const queueRenderPage = num => {
            if (pageIsRendering) {
                pageNumIsPending = num;
            } else {
                renderPage(num);
            }
        };

        const showPrevPage = () => {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        };

        const showNextPage = () => {
            if (pageNum >= pdfDoc.numPages) return;
            // Anti-cheat check
            if (pageTimer > 0 && pageNum === highestPageReached) {
                alert("Te rugăm să acorzi timpul necesar pentru a parcurge această pagină (" + pageTimer + " secunde rămase).");
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        };

        // Zoom logic
        document.getElementById('zoom-in').addEventListener('click', () => {
            scale += 0.2;
            queueRenderPage(pageNum);
        });
        document.getElementById('zoom-out').addEventListener('click', () => {
            if (scale <= 0.4) return;
            scale -= 0.2;
            queueRenderPage(pageNum);
        });

        // Event listeners
        prevBtn.addEventListener('click', showPrevPage);
        nextBtn.addEventListener('click', showNextPage);

        // Timers Logic
        function startPageTimer(num) {
            clearInterval(pageInterval);
            
            // If already visited this page, don't force them to wait again
            if (num < highestPageReached) {
                pageTimer = 0;
                updateTimerUI();
                return;
            }

            pageTimer = MIN_TIME_PER_PAGE;
            nextBtn.disabled = true;
            
            pageInterval = setInterval(() => {
                if (isWindowFocused) {
                    pageTimer--;
                    updateTimerUI();
                    
                    if (pageTimer <= 0) {
                        clearInterval(pageInterval);
                        nextBtn.disabled = (pageNum >= pdfDoc.numPages);
                        timerInfo.innerHTML = '<i class="fa fa-check-circle"></i> Poți continua';
                        timerInfo.style.color = '#4caf50';
                    }
                }
            }, 1000);
            updateTimerUI();
        }

        function updateTimerUI() {
            if (pageTimer > 0) {
                timerInfo.innerHTML = '<i class="fa fa-clock-o"></i> ' + pageTimer + 's pt. scroll';
                timerInfo.style.color = '#ff9800';
            }
        }

        // Global Study Time
        function startGlobalTimer() {
            globalTimerInterval = setInterval(() => {
                if (isWindowFocused) {
                    globalStudyTime++;
                    // Every 10 seconds, send a heartbeat to server to save time
                    if (globalStudyTime % 10 === 0) {
                        saveProgress(pageNum, true);
                    }
                }
            }, 1000);
        }

        // Save progress to backend
        function saveProgress(currentPage, isHeartbeat = false) {
            const formData = new FormData();
            formData.append('action', 'rima_save_pdf_progress');
            formData.append('course_id', courseId);
            formData.append('doc_id', docId);
            formData.append('current_page', highestPageReached);
            formData.append('total_pages', pdfDoc ? pdfDoc.numPages : 0);
            formData.append('session_time_added', isHeartbeat ? 10 : 0);
            formData.append('nonce', "<?php echo wp_create_nonce('rima_pdf_track'); ?>");

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(res => {
                if (res.success && res.data) {
                    if (res.data.limit_reached) {
                        triggerSupervisorBlock(res.data.limit_reason);
                    }
                    if (res.data.course_completed) {
                        showQuizButton(res.data.course_url);
                        window.parent.postMessage({ type: 'PDF_COURSE_COMPLETED', course_id: courseId }, '*');
                    }
                }
            }).catch(e => console.error(e));
        }
        
        function triggerSupervisorBlock(reason) {
            clearInterval(globalTimerInterval);
            clearInterval(pageInterval);
            
            const canvas = document.getElementById('pdf-canvas');
            if(canvas) canvas.style.display = 'none';
            document.getElementById('toolbar').style.display = 'none';
            
            // Create Supervisor Screen
            if (!document.getElementById('rima-supervisor-screen')) {
                const blocker = document.createElement('div');
                blocker.id = 'rima-supervisor-screen';
                blocker.style.position = 'absolute';
                blocker.style.inset = '0';
                blocker.style.background = '#111';
                blocker.style.color = '#fff';
                blocker.style.zIndex = '9999';
                blocker.style.display = 'flex';
                blocker.style.flexDirection = 'column';
                blocker.style.justifyContent = 'center';
                blocker.style.alignItems = 'center';
                blocker.style.padding = '40px';
                blocker.style.textAlign = 'center';
                
                blocker.innerHTML = `
                    <i class="fa fa-graduation-cap" style="font-size: 80px; color: #ff3366; margin-bottom: 20px;"></i>
                    <h1 style="margin: 0 0 15px 0; font-size: 32px; color: #fff;">Pauză de Studiu</h1>
                    <p style="font-size: 18px; color: #ddd; max-width: 600px; line-height: 1.6;">${reason}</p>
                    <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #ff3366;">
                        <p style="font-size: 14px; color: #aaa; margin: 0; text-align: left;">Acest algoritm educațional RIMA Academy este conceput pentru a asigura asimilarea corectă a informațiilor pe termen lung, prevenind suprasolicitarea.</p>
                    </div>
                `;
                document.body.appendChild(blocker);
            }
        }
        
        function showQuizButton(courseUrl) {
            if (pageNum >= pdfDoc.numPages) {
                const toolbar = document.getElementById('toolbar');
                toolbar.innerHTML = `
                    <div style="width:100%; text-align:center; padding: 10px; background: #222;">
                        <span style="margin:0 15px 0 0; color:#4caf50; font-weight:bold;"><i class="fa fa-check-circle"></i> Ai finalizat materialul!</span>
                        <a href="${courseUrl}" target="_top" style="display:inline-block; background:#ff3366; color:#fff; padding:8px 20px; text-decoration:none; font-weight:bold; border-radius:20px; font-size:14px; box-shadow: 0 2px 5px rgba(255,51,102,0.3);">
                            <i class="fa fa-pencil-square-o"></i> Susține Quiz-ul / Continuă
                        </a>
                    </div>
                `;
            }
        }

        // Load PDF
        pdfjsLib.getDocument(pdfDataUrl).promise.then(pdfDoc_ => {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            
            loadingBar.style.display = 'none';
            renderWrapper.style.display = 'block';
            
            // Initial render
            renderPage(pageNum);
            
            // Enable prev/next correctly
            prevBtn.disabled = false;
            
            startGlobalTimer();
            // Check limits immediately in case they already exceeded today
            saveProgress(pageNum, false);
        }).catch(err => {
            loadingBar.textContent = 'Eroare la încărcarea documentului: ' + err.message;
        });

    </script>
</body>
</html>
