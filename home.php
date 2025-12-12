<?php
// Require authentication to access home page
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mood Tracker — Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8f6;
      --card:#ffffff;
      --accent:#7c9d85;
      --accent-strong:#6a8b74;
      --muted:#6b7280;
      --shadow:0 8px 24px rgba(0,0,0,0.08);
    }
   
    *{
      box-sizing:border-box;
      margin:0;
      padding:0}
    
    body{font-family:Inter,system-ui,Segoe UI,sans-serif;background:var(--bg);color:#1f2a1f;min-height:100vh;display:flex}
    a{text-decoration:none;color:inherit}
    /* Sidebar */
    .sidebar{width:250px;background:#ecf2ec;min-height:100vh;padding:20px 18px;display:flex;flex-direction:column;gap:12px;position:fixed;left:0;top:0;bottom:0}
    .brand{display:flex;align-items:center;gap:10px;margin-bottom:16px}
    .brand-icon{width:42px;height:42px;border-radius:12px;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;font-weight:700}
    .brand-text{display:flex;flex-direction:column;gap:2px}
    .brand-title{font-weight:700;font-size:16px;color:#1f2a1f}
    .brand-sub{font-size:12px;color:#54625a}
    .nav{display:flex;flex-direction:column;gap:6px;margin-top:6px}
    .nav-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;cursor:pointer;color:#1f2a1f;font-weight:600;transition:all 0.18s ease;text-decoration:none}
    .nav-item:hover{background:#dde6df}
    .nav-item.active{background:var(--accent);color:#fff;box-shadow:0 6px 14px rgba(124,157,133,0.25)}
    .nav-icon{font-size:18px;width:22px;text-align:center}
    .welcome{margin-top:auto;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px;font-size:13px;color:#1f2a1f;box-shadow:0 1px 3px rgba(0,0,0,0.05)}
    /* Main */
    .main{margin-left:250px;flex:1;padding:26px 34px}
    .header-title{font-size:30px;font-weight:700;margin-bottom:6px;color:#1f2a1f}
    .header-sub{color:#6b7280;font-size:15px}
    .section-title{display:flex;align-items:center;gap:8px;font-weight:700;color:#2f3e32;margin-bottom:12px}
    .section-title .icon{font-size:18px}
    /* Layout grids */
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px}
    .card{background:var(--card);border-radius:16px;padding:16px;box-shadow:0 6px 16px rgba(0,0,0,0.06);border:1px solid #e8efe8}
    /* Mood cards row */
    .mood-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:10px}
    .mood-card{display:flex;flex-direction:column;gap:10px;padding:14px;border-radius:14px;background:#f8faf8;border:1px solid #e3ebe3}
    .mood-top{display:flex;align-items:center;gap:12px}
    .mood-icon{width:46px;height:46px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;color:#7c9d85;box-shadow:inset 0 1px 2px rgba(0,0,0,0.06)}
    .label{font-weight:700;font-size:15px;color:#1f2a1f}
    .sub{font-size:13px;color:#6b7280}
    .meta{display:flex;gap:12px;font-size:13px;color:#6b7280}
    /* Combined score */
    .score-card{grid-column:span 2;background:linear-gradient(135deg,#7c9d85,#6a8b74);color:#fff;border-radius:16px;padding:18px 20px;display:flex;align-items:center;gap:20px;box-shadow:var(--shadow)}
    .score-number{font-size:46px;font-weight:700;line-height:1}
    .score-text{font-size:14px;opacity:0.92}
    /* Quick actions */
    .quick-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:6px}
    .quick-btn{background:#f6f7f6;border:1px solid #e5ede5;border-radius:14px;padding:16px 12px;display:flex;flex-direction:column;gap:10px;align-items:center;text-align:center;cursor:pointer;transition:all 0.15s ease;font-weight:700;color:#1f2a1f;white-space:normal;word-break:break-word;text-decoration:none;width:100%;border:none;font-family:Inter,sans-serif}
    .quick-btn:hover{background:#eef3ee;transform:translateY(-1px)}
    .quick-icon{width:40px;height:40px;border-radius:12px;background:#edf3ed;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6f8b74;flex-shrink:0}
    @media(max-width:768px){
      .quick-grid{grid-template-columns:repeat(2,1fr)}
    }
    /* Insights */
    .insights-wrap{display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start;margin-top:18px}
    .summary-card{background:#fff;border:1px solid #e8efe8;border-radius:16px;padding:16px;box-shadow:0 8px 20px rgba(0,0,0,0.06);overflow:hidden;word-wrap:break-word}
    .summary-row{margin-top:8px;color:#1f2a1f;font-size:14px;word-break:break-word}
    .summary-row strong{color:#1f2a1f}
    .reminder{margin-top:16px;background:#fff7e5;border:1px solid #ffe6b3;border-radius:16px;padding:14px;font-size:14px;color:#8b6a1f;word-wrap:break-word}
    .recommend{background:#ffffff;border:1px solid #e8efe8;border-radius:16px;padding:16px;box-shadow:0 8px 20px rgba(0,0,0,0.06);overflow:hidden}
    .rec-item{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:14px;color:#1f2a1f;word-break:break-word}
    .rec-icon{width:28px;height:28px;border-radius:10px;background:#f2f5f2;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
    .affirmation{margin-top:14px;background:#6f9078;color:#fff;padding:16px;border-radius:16px;text-align:center;font-style:italic;box-shadow:var(--shadow);word-wrap:break-word}
    @media(max-width:1024px){
      .insights-wrap{grid-template-columns:1fr}
      .score-card{grid-column:span 1}
    }

     /* Detector area */
    .detectors{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-top:12px}
    .detector-card{border-radius:14px;padding:14px;background:#fff;border:1px solid #e8efe8}
    #videoHolder{position:relative;border-radius:12px;overflow:hidden;background:#000;min-height:220px;display:flex;align-items:center;justify-content:center}
    video#webcam{width:100%;height:100%;object-fit:cover;display:block}

    /* controls */
    .controls{display:flex;gap:10px;margin-top:10px}
    .toggle-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:12px;border:none;background:#f4f6f4;cursor:pointer;font-weight:700;transition:all .15s}
    .toggle-btn.on{background:var(--accent);color:white;box-shadow:0 6px 14px rgba(106,139,116,0.16)}
    .indicator{width:12px;height:12px;border-radius:50%;background:#cbd5ca;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.08)}
    .indicator.active{background:#4ee07a;box-shadow:0 6px 10px rgba(78,224,122,0.12)}
    .indicator.recording{background:#ff6b6b;box-shadow:0 6px 12px rgba(255,107,107,0.12);animation:pulse 1s infinite}
    @keyframes pulse {
      0% { transform: scale(1); opacity: 0.95 }
      50% { transform: scale(1.2); opacity: 0.7 }
      100% { transform: scale(1); opacity: 0.95 }
    }

    /* Score animation */
    .score-card{display:flex;align-items:center;gap:18px;border-radius:14px;padding:16px;background:linear-gradient(135deg,#7c9d85,#6a8b74);color:#fff;box-shadow:var(--shadow);font-weight:700}
    .score-num{font-size:42px;line-height:1}
    .small{font-size:13px;opacity:0.95}

    /* small text */
    .muted{color:var(--muted);font-size:13px}

    /* animation for cards */
    .fade-in { animation: fadeIn .45s ease both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(6px) } to { opacity:1; transform: translateY(0) } }

    @media (max-width: 980px) {
      .detectors{grid-template-columns:1fr}
      .main { padding: 16px }
    }
  </style>
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">⭐</div>
      <div class="brand-text">
        <div class="brand-title">Mood Tracker</div>
        <div class="brand-sub">Track your wellbeing</div>
      </div>
    </div>
    <nav class="nav">
      <a class="nav-item active" href="home.php"><span class="nav-icon">🏠</span>Home</a>
      <a class="nav-item" href="calendar.php"><span class="nav-icon">📅</span>Calendar</a>
      <a class="nav-item" href="daily-log.php"><span class="nav-icon">📖</span>Daily Log</a>
      <a class="nav-item" href="calming-tools.php"><span class="nav-icon">✨</span>Calming Tools</a>
      <a class="nav-item" href="flashcard.php"><span class="nav-icon">💬</span>Flashcards</a>
      <a class="nav-item" href="progress.php"><span class="nav-icon">📊</span>Progress</a>
      <a class="nav-item" href="settings.php"><span class="nav-icon">⚙️</span>Settings</a>
    </nav>
    <div class="welcome" id="authSection">
      <div id="userInfo" style="display:none;flex-direction:column;gap:10px">
        <div style="font-size:12px;color:#6b7280;font-weight:400">Logged in as</div>
        <div id="username" style="font-weight:700;color:#1f2a1f;font-size:16px;line-height:1.3">—</div>
        <a href="logout.php" style="display:inline-block;padding:8px 14px;background:#6a8b74;color:#fff;border-radius:8px;font-size:13px;text-align:center;text-decoration:none;font-weight:500;width:100%;transition:background 0.2s" onmouseover="this.style.background='#5a7a64'" onmouseout="this.style.background='#6a8b74'">Log out</a>
      </div>
      <div id="authPrompt" style="text-align:center;gap:8px;display:flex;flex-direction:column">
        <div>Not logged in</div>
        <a href="login.php" style="padding:8px 12px;background:#7c9d85;color:#fff;border-radius:8px;font-size:13px;font-weight:600">Log in</a>
        <a href="register.php" style="padding:8px 12px;background:#e8efe8;color:#1f2a1f;border-radius:8px;font-size:13px;font-weight:600">Sign up</a>
      </div>
    </div>
  </aside>

  <main class="main">
    <header>
      <div class="header-title" id="greeting">Good Afternoon</div>
      <div class="header-sub">How are you feeling today?</div>
    </header>

    <section style="margin-top:20px">
      <div class="detectors fade-in">
      <div class="detector-card card">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div class="section-title"><span class="icon">🌊</span><span>Live Detection</span></div>
          <div class="muted">Status: <span id="statusText">idle</span></div>
        </div>

        <div id="videoHolder" style="margin-top:12px">
          <video id="webcam" autoplay muted playsinline></video>
          <canvas id="overlay" style="position:absolute;left:0;top:0;pointer-events:none"></canvas>
        </div>

        <div class="controls">
          <button id="camToggle" class="toggle-btn"><span id="camIndicator" class="indicator"></span> Camera</button>
          <button id="micToggle" class="toggle-btn"><span id="micIndicator" class="indicator"></span> Microphone</button>
          <button id="saveBtn" class="toggle-btn" style="margin-left:auto">Save Now</button>
        </div>

        <div style="display:flex;gap:14px;margin-top:14px;align-items:center">
          <div class="score-card" style="padding:12px 16px">
            <div>
              <div style="font-size:13px;opacity:0.95">Combined Mood Score</div>
              <div id="scoreNum" class="score-num">—</div>
            </div>
            <div style="margin-left:auto;text-align:right">
              <div id="scoreText" class="small">No data</div>
            </div>
          </div>

          <div style="min-width:220px">
            <div class="muted">Face:</div>
            <div id="faceLabel" style="font-weight:700">—</div>
            <div class="muted" style="margin-top:6px">Audio:</div>
            <div id="audioLabel" style="font-weight:700">—</div>
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:12px">
        <div class="card detector-card fade-in">
          <div class="section-title"><span class="icon">📈</span><span>Today's Summary</span></div>
          <div id="todaySummary" style="margin-top:8px;font-size:13px;line-height:1.6;word-break:break-word" class="muted">Loading...</div>
        </div>

        <div class="card detector-card fade-in">
          <div class="section-title"><span class="icon">⚙️</span><span>Instructions & Safety</span></div>
          <div style="margin-top:8px;font-size:13px;line-height:1.6;word-break:break-word" class="muted">
            <ul style="margin-left:16px">
              <li>Camera & mic run locally in your browser. No camera frames/audio are uploaded — only small emotion metadata is saved.</li>
              <li>For hosting: use HTTPS (required for getUserMedia). Set correct DB credentials on server.</li>
              <li>We store only the detected labels and scores; no raw images/audio are sent to the server.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    </section>

    <section style="margin-top:16px">
      <div class="score-card">
        <div>
          <div class="label" style="color:#fff">Combined Mood Score</div>
          <div class="score-text">Calculated from all sources</div>
        </div>
        <div style="margin-left:auto;text-align:right">
          <div class="score-number">78</div>
          <div class="score-text">/ 100</div>
        </div>
      </div>
    </section>

    <section style="margin-top:18px">
      <div class="section-title"><span class="icon">✨</span><span>Quick Actions</span></div>
      <div class="quick-grid">
        <a class="quick-btn" href="daily-log.php"><div class="quick-icon">📖</div><span>Diary</span></a>
        <a class="quick-btn" href="calendar.php"><div class="quick-icon">📅</div><span>Calendar</span></a>
        <a class="quick-btn" href="calming-tools.php"><div class="quick-icon">💚</div><span>Calming Tools</span></a>
        <a class="quick-btn" href="flashcard.php"><div class="quick-icon">💬</div><span>Flashcards</span></a>
      </div>
    </section>

    <section style="margin-top:20px">
      <div class="section-title"><span class="icon">📈</span><span>Today's Insights</span></div>
      <div class="insights-wrap">
        <div class="summary-card">
          <div class="label">Daily Summary</div>
          <div class="summary-row"><strong>Main Emotion:</strong> Happy and energized</div>
          <div class="summary-row"><strong>Energy Level:</strong> Above average (72%)</div>
          <div class="summary-row"><strong>Environment:</strong> Calm and quiet surroundings</div>
          <div class="summary-row" style="margin-top:10px;border-top:1px solid #eef2ee;padding-top:10px">
            <strong>Insights:</strong> Your positive facial expressions match your calm voice tone. Great alignment today!
          </div>
          <div class="reminder">Smart Reminder: You usually feel stressed around 9 PM. Try calming tools at 8:30 PM tonight.</div>
        </div>
        <div>
          <div class="recommend">
            <div class="label" style="margin-bottom:8px">Recommended for You</div>
            <div class="rec-item"><div class="rec-icon">📷</div><div>Capture this happy moment with a photo or journal entry</div></div>
            <div class="rec-item"><div class="rec-icon">✨</div><div>Your energy is high - great time for a creative activity</div></div>
            <div class="rec-item"><div class="rec-icon">💬</div><div>Reflect on what made today feel good</div></div>
          </div>
          <div class="affirmation">"You're doing amazing today" — Keep nurturing these positive feelings</div>
        </div>
      </div>
    </section>
  </main>


<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.21.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/speech-commands@0.4.0/dist/speech-commands.min.js"></script>

<script>
    // UI elements
  const camToggle = document.getElementById('camToggle');
  const micToggle = document.getElementById('micToggle');
  const camIndicator = document.getElementById('camIndicator');
  const micIndicator = document.getElementById('micIndicator');
  const webcamEl = document.getElementById('webcam');
  const overlay = document.getElementById('overlay');
  const statusText = document.getElementById('statusText');
  const faceLabel = document.getElementById('faceLabel');
  const audioLabel = document.getElementById('audioLabel');
  const scoreNum = document.getElementById('scoreNum');
  const scoreText = document.getElementById('scoreText');
  const saveBtn = document.getElementById('saveBtn');
  const todaySummary = document.getElementById('todaySummary');

  let camStream = null;
  let micStream = null;
  let audioContext = null;
  let analyser = null;
  let audioDataArray = null;
  let audioInterval = null;
  let faceInterval = null;
  let modelsLoaded = false;

  // weights for combined score (adjustable)
  const FACE_WEIGHT = 0.7;
  const AUDIO_WEIGHT = 0.3;

  // helpers
  function setStatus(s) { statusText.textContent = s; }
  function setCamActive(active){
    camToggle.classList.toggle('on', active);
    camIndicator.classList.toggle('active', active);
  }
  function setMicActive(active, recording=false){
    micToggle.classList.toggle('on', active);
    micIndicator.classList.toggle('active', active);
    micIndicator.classList.toggle('recording', recording);
  }

  async function loadModels() {
  const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
  setStatus('loading models...');
  await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
  await faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL);
  modelsLoaded = true;
  setStatus('idle');
}

  // Camera start/stop
  async function startCamera(){
    if (!modelsLoaded) await loadModels();
    if (camStream) return;
    try {
      camStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 }, audio: false });
      webcamEl.srcObject = camStream;
      webcamEl.play();
      setCamActive(true);
      setStatus('camera on');
      startFaceLoop();
    } catch (e) {
      console.error(e);
      setStatus('camera blocked or not available');
    }
  }
  function stopCamera(){
    if (!camStream) return;
    const tracks = camStream.getTracks();
    tracks.forEach(t => t.stop());
    camStream = null;
    webcamEl.pause();
    webcamEl.srcObject = null;
    setCamActive(false);
    setStatus('camera off');
    stopFaceLoop();
    clearOverlay();
  }

  // Face detection loop
  function clearOverlay(){
    const ctx = overlay.getContext('2d');
    overlay.width = webcamEl.clientWidth;
    overlay.height = webcamEl.clientHeight;
    ctx.clearRect(0,0,overlay.width,overlay.height);
  }
  function startFaceLoop(){
    const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 160, scoreThreshold: 0.5 });
    faceInterval = setInterval(async () => {
      if (!webcamEl || webcamEl.readyState < 2) return;
      const detections = await faceapi.detectSingleFace(webcamEl, options).withFaceExpressions();
      drawOverlay(detections);
      if (detections && detections.expressions) {
        const expressions = detections.expressions;
        // find top expression and confidence
        let top = { name: null, score: 0 };
        Object.keys(expressions).forEach(k => {
          if (expressions[k] > top.score) top = { name: k, score: expressions[k] };
        });
        faceLabel.textContent = `${top.name} (${Math.round(top.score*100)}%)`;
        computeAndShowScore(top.name, top.score, null, null);
      } else {
        faceLabel.textContent = 'No face';
      }
    }, 700); // every 700ms
  }
  function stopFaceLoop(){
    if (faceInterval) clearInterval(faceInterval);
    faceInterval = null;
  }
  function drawOverlay(detection){
    const ctx = overlay.getContext('2d');
    overlay.width = webcamEl.clientWidth;
    overlay.height = webcamEl.clientHeight;
    ctx.clearRect(0,0,overlay.width,overlay.height);
    if (!detection) return;
    const ratioX = overlay.width / webcamEl.videoWidth;
    const ratioY = overlay.height / webcamEl.videoHeight;
    const box = detection.detection.box;
    ctx.strokeStyle = 'rgba(255,255,255,0.9)';
    ctx.lineWidth = 2;
    ctx.strokeRect(box.x * ratioX, box.y * ratioY, box.width * ratioX, box.height * ratioY);
  }

  // Audio start/stop & simple RMS-based classifier
  async function startMic(){
    try {
      micStream = await navigator.mediaDevices.getUserMedia({ audio: true, video:false });
      setMicActive(true, true);
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const source = audioContext.createMediaStreamSource(micStream);
      analyser = audioContext.createAnalyser();
      analyser.fftSize = 1024;
      source.connect(analyser);
      audioDataArray = new Uint8Array(analyser.fftSize);
      audioInterval = setInterval(sampleAudio, 300);
      setStatus('mic on');
    } catch (e) {
      console.error(e);
      setStatus('mic blocked or not available');
    }
  }
  function stopMic(){
    if (audioInterval) clearInterval(audioInterval);
    if (micStream) {
      micStream.getTracks().forEach(t => t.stop());
      micStream = null;
    }
    if (audioContext) {
      audioContext.close();
      audioContext = null;
    }
    setMicActive(false, false);
    setStatus('mic off');
  }

  function sampleAudio(){
    if (!analyser) return;
    analyser.getByteTimeDomainData(audioDataArray);
    // compute RMS
    let sum = 0;
    for (let i = 0; i < audioDataArray.length; i++) {
      const v = (audioDataArray[i] - 128) / 128;
      sum += v * v;
    }
    const rms = Math.sqrt(sum / audioDataArray.length); // 0..1
    // heuristic thresholds - tune for your environment
    let audioEmotion = 'neutral';
    let audioScore = Math.round(rms * 100);
    if (rms > 0.15) {
      audioEmotion = 'high_energy';
    } else if (rms > 0.06) {
      audioEmotion = 'neutral';
    } else {
      audioEmotion = 'calm';
    }
    audioLabel.textContent = `${audioEmotion} (${audioScore})`;
    computeAndShowScore(null, null, audioEmotion, audioScore);
  }

  // combine results: lastFace and lastAudio stored
  let lastFace = { name: null, score: 0 };
  let lastAudio = { name: null, score: 0 };
  let lastCombined = null;

  function computeAndShowScore(faceName, faceConf, audioName, audioScoreVal){
    if (faceName) { lastFace = { name: faceName, score: faceConf } }
    if (audioName) { lastAudio = { name: audioName, score: audioScoreVal } }

    // Map face expression to a 0-100 happiness-like metric (simple mapping)
    const faceMap = {
      happy: 90, neutral: 60, sad: 20, angry: 15, fearful: 10, disgusted: 20, surprised: 70
    };
    const faceVal = lastFace.name ? (faceMap[lastFace.name] || 50) * lastFace.score : null;

    // Map audio score (0..100) to 0..100, but calm should be mid-high
    const audioVal = lastAudio.name ? lastAudio.score : null;

    // If both present:
    let combined = 50;
    if (faceVal !== null && audioVal !== null) {
      combined = Math.round(( (faceVal * FACE_WEIGHT) + (audioVal * AUDIO_WEIGHT) ) / (FACE_WEIGHT + AUDIO_WEIGHT));
    } else if (faceVal !== null) {
      combined = Math.round(faceVal);
    } else if (audioVal !== null) {
      combined = Math.round(audioVal);
    } else {
      combined = null;
    }

    if (combined !== null) {
      animateScoreTo(combined);
      scoreText.textContent = `${combined} / 100`;
      lastCombined = combined;
      // live-update Today's Insights from current detections
      renderInsights({
        found: true,
        face_emotion: lastFace.name,
        face_confidence: Math.round((lastFace.score || 0) * 100),
        audio_emotion: lastAudio.name,
        audio_score: lastAudio.score,
        combined_score: lastCombined
      });
    }
  }

  // simple number animation
  let animInterval = null;
  function animateScoreTo(target){
    if (animInterval) clearInterval(animInterval);
    const start = parseInt(scoreNum.textContent) || 0;
    const stepCount = 12;
    let step = 0;
    const diff = target - start;
    animInterval = setInterval(() => {
      step++;
      const val = Math.round(start + diff * (step / stepCount));
      scoreNum.textContent = val;
      if (step >= stepCount) {
        clearInterval(animInterval);
        animInterval = null;
      }
    }, 30);
  }

  // save to backend
  async function saveNow(){
    const face = lastFace;
    const audio = lastAudio;
    const combined = parseInt(scoreNum.textContent) || 0;
    const payload = {
      face_emotion: face.name,
      face_confidence: face.score,
      audio_emotion: audio.name,
      audio_score: audio.score,
      combined_score: combined,
      meta: { userAgent: navigator.userAgent }
    };
    try {
      const res = await fetch('api/save_mood.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.ok) {
        setStatus('saved');
        loadTodaySummary();
      } else {
        console.error(json);
        setStatus('save failed');
      }
    } catch (e) {
      console.error(e);
      setStatus('save error');
    }
  }

  // load today's summary
  async function loadTodaySummary(){
    try {
      const res = await fetch('api/get_today_mood.php');
      if (res.status === 401) {
        todaySummary.innerHTML = '<div>Please <a href="/login.php">log in</a> to view saved data.</div>';
        return;
      }
      const j = await res.json();
      if (j.found && j.data) {
        const d = j.data;
        todaySummary.innerHTML = `
          <div><strong>${d.date} ${d.time}</strong></div>
          <div>Face: ${d.face_emotion} (${d.face_confidence ?? '—'})</div>
          <div>Audio: ${d.audio_emotion} (${d.audio_score ?? '—'})</div>
          <div>Combined: ${d.combined_score}</div>
        `;
      } else {
        todaySummary.innerHTML = '<div>No detections saved today.</div>';
      }
    } catch (e) {
      console.error(e);
      todaySummary.innerHTML = '<div>Unable to load summary.</div>';
    }
  }

  async function loadFullInsights() {
    try {
      const res = await fetch('api/get_insights.php');
      if (res.status === 401) {
        // not logged in — show prompt in summary card
        const summaryCard = document.querySelector('.summary-card');
        if (summaryCard) summaryCard.innerHTML = '<div class="muted">Please <a href="/login.php">log in</a> to view your insights.</div>';
        return;
      }
      const data = await res.json();
      // render insights (API fallback)
      renderInsights(data);

    } catch (e) {
        console.error("Insights load error:", e);
    }
}

  // Render insights UI from data object. Used for live updates and API data.
  function renderInsights(data) {
    if (!data || !data.found) return;

    // Update big combined score
    const bigScoreEl = document.querySelector('.score-number');
    if (bigScoreEl && data.combined_score != null) bigScoreEl.textContent = data.combined_score;

    // Update today's summary small card
    if (todaySummary) {
      todaySummary.innerHTML = `
        <div><strong>${new Date().toLocaleDateString()}</strong></div>
        <div>Face: ${data.face_emotion ?? '—'} (${data.face_confidence ?? '—'})</div>
        <div>Audio: ${data.audio_emotion ?? '—'} (${data.audio_score ?? '—'})</div>
        <div>Combined: ${data.combined_score ?? '—'}</div>
      `;
    }

    // Summary card
    const summaryCard = document.querySelector('.summary-card');
    if (summaryCard) {
      summaryCard.innerHTML = `
        <div class="label">Daily Summary</div>
        <div class="summary-row"><strong>Main Emotion:</strong> ${data.face_emotion ?? '—'}</div>
        <div class="summary-row"><strong>Energy Level:</strong> ${Math.min(100, data.audio_score ?? 0)}%</div>
        <div class="summary-row"><strong>Environment:</strong> Based on your audio levels</div>
        <div class="summary-row" style="margin-top:10px;border-top:1px solid #eef2ee;padding-top:10px">
          <strong>Insights:</strong> Your ${data.face_emotion ?? '—'} expression and ${data.audio_emotion ?? '—'} tone influenced your score.
        </div>
        <div class="reminder">Smart Reminder: Keep tracking your mood daily!</div>
      `;
    }

    // Recommendations
    const recommend = document.querySelector('.recommend');
    if (recommend) {
      let recHTML = '';
      const score = Number(data.combined_score) || 0;
      if (score >= 70) {
        recHTML += `<div class="rec-item"><div class="rec-icon">✨</div><div>You're doing great — keep the momentum!</div></div>`;
      } else if (score >= 40) {
        recHTML += `<div class="rec-item"><div class="rec-icon">🧘</div><div>Try a calming tool to stabilize your emotions.</div></div>`;
      } else {
        recHTML += `<div class="rec-item"><div class="rec-icon">🤝</div><div>Consider journaling or talking to someone you trust.</div></div>`;
      }
      recommend.innerHTML = `<div class="label" style="margin-bottom:8px">Recommended for You</div>${recHTML}`;
    }

    // Affirmation
    const aff = document.querySelector('.affirmation');
    if (aff) {
      const score = Number(data.combined_score) || 0;
      if (score >= 70) aff.textContent = `"You're shining today — keep going!"`;
      else if (score >= 40) aff.textContent = `"You're doing your best — and that’s enough."`;
      else aff.textContent = `"Take it slow — better days are coming."`;
    }
  }


  // Set dynamic greeting based on time of day
  function updateGreeting() {
    const hour = new Date().getHours();
    const greetingEl = document.getElementById('greeting');
    if (hour >= 5 && hour < 12) {
      greetingEl.textContent = 'Good Morning';
    } else if (hour >= 12 && hour < 17) {
      greetingEl.textContent = 'Good Afternoon';
    } else {
      greetingEl.textContent = 'Good Evening';
    }
  }
  updateGreeting();
  // Update greeting every minute
  setInterval(updateGreeting, 60000);

  // event listeners
  camToggle.addEventListener('click', () => {
    if (camStream) stopCamera(); else startCamera();
  });
  micToggle.addEventListener('click', () => {
    if (micStream) stopMic(); else startMic();
  });
  saveBtn.addEventListener('click', saveNow);

  // when face detection changes, update lastFace variable inside the detection loop
  // We'll make sure computeAndShowScore updates combined.

  // on unload: stop streams
  window.addEventListener('beforeunload', () => {
    stopCamera();
    stopMic();
  });

  // initial
  (async () => {
    await loadModels();
    // optionally auto-start camera/mic
    // startCamera();
    // startMic();
    // check auth status, then load saved insights if logged in
    try {
      const auth = await fetch('api/auth_status.php');
      const authJson = await auth.json();
      const userInfo = document.getElementById('userInfo');
      const authPrompt = document.getElementById('authPrompt');
      if (!authJson.logged_in) {
        // disable save button when not logged in
        saveBtn.disabled = true;
        saveBtn.textContent = 'Login to save';
        statusText.textContent = 'not logged in';
        authPrompt.style.display = 'flex';
        userInfo.style.display = 'none';
      } else {
        // show logged-in user info
        document.getElementById('username').textContent = authJson.user.username;
        userInfo.style.display = 'flex';
        authPrompt.style.display = 'none';
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Now';
        // load user-specific saved data
        loadTodaySummary();
        loadFullInsights();
      }
    } catch (e) {
      // if auth check fails, show login prompt
      console.error('Auth check error:', e);
      document.getElementById('authPrompt').style.display = 'flex';
      document.getElementById('userInfo').style.display = 'none';
      saveBtn.disabled = true;
      saveBtn.textContent = 'Login to save';
    }
    setStatus('idle');
  })();

</script>

</body>
</html>

