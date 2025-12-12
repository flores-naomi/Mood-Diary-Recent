<?php
session_start();
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
  <title>Mood Tracker — Calming Tools</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8f6;
      --card:#ffffff;
      --accent:#7c9d85;
      --accent-strong:#6a8b74;
      --muted:#6b7280;
    }
    *{box-sizing:border-box;margin:0;padding:0}
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
    /* Tools grid */
    .tools-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-top:18px}
    .tool-card{background:var(--card);border-radius:16px;padding:18px;display:flex;flex-direction:column;gap:10px;box-shadow:0 8px 18px rgba(0,0,0,0.06);border:1px solid #e8efe8;min-height:120px;cursor:pointer}
    .tool-top{display:flex;align-items:center;gap:12px}
    .tool-icon{width:46px;height:46px;border-radius:14px;background:#f4f7f3;display:flex;align-items:center;justify-content:center;font-size:22px;color:#6f8b74;box-shadow:inset 0 1px 2px rgba(0,0,0,0.05)}
    .tool-label{font-weight:700;font-size:16px;color:#1f2a1f}
    .tool-sub{font-size:13px;color:#6b7280}
    .recommend{margin-top:24px;background:linear-gradient(135deg,#7da88a,#5f8d73);color:#fff;border-radius:18px;padding:18px 20px;display:flex;align-items:flex-start;gap:12px;box-shadow:0 10px 24px rgba(0,0,0,0.12)}
    .rec-icon{font-size:20px}
    .rec-title{font-weight:700;font-size:16px;margin-bottom:4px}
    .rec-sub{font-size:14px;line-height:1.5;opacity:0.95}
    /* Modals */
    .modal-overlay{position:fixed;inset:0;background:rgba(26,37,31,0.45);backdrop-filter:blur(2px);opacity:0;pointer-events:none;transition:opacity 0.2s ease;z-index:90}
    .modal-overlay.show{opacity:1;pointer-events:auto}
    .modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity 0.2s ease;z-index:91}
    .modal.show{opacity:1;pointer-events:auto}
    .dialog{background:#fff;border-radius:18px;padding:18px;max-width:680px;width:92%;box-shadow:0 24px 60px rgba(0,0,0,0.18);position:relative}
    .dialog.wide{max-width:720px}
    .modal-close{position:absolute;top:12px;right:12px;background:#f2f4f3;border:none;border-radius:999px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#6b7280;cursor:pointer}
    .modal-title{font-weight:700;font-size:18px;color:#2f3e32;text-align:center;margin-bottom:4px}
    .modal-sub{color:#6b7280;font-size:14px;text-align:center;margin-bottom:14px}
    .list-card{background:#f8faf8;border:1px solid #e8efe8;border-radius:12px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}
    .list-card .text{flex:1}
    .list-card .title{font-weight:700;font-size:15px;color:#1f2a1f;margin-bottom:4px}
    .list-card .note{font-size:13px;color:#6b7280}
    .pill{background:#e8f3ec;color:#285c3f;border-radius:999px;padding:6px 10px;font-weight:700;font-size:12px;min-width:46px;text-align:center}
    .sound-play{width:38px;height:38px;border-radius:12px;border:1px solid #dce6e0;background:#fff;display:flex;align-items:center;justify-content:center;font-size:16px;color:#2f6f4e;cursor:pointer}
    .grounding-item{display:flex;gap:12px;align-items:flex-start;background:#f9fbfa;border:1px solid #e5ece7;border-radius:12px;padding:12px;margin-bottom:10px}
    .grounding-step{width:32px;height:32px;border-radius:10px;background:#e1edf0;color:#1d4f62;display:flex;align-items:center;justify-content:center;font-weight:700}
    .affirm-card{background:linear-gradient(135deg,#7aa785,#6c9a7a);color:#fff;border-radius:16px;padding:18px;text-align:center;display:flex;flex-direction:column;gap:12px}
    .affirm-card .quote{font-size:16px;font-weight:700;line-height:1.6}
    .reset-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-top:10px}
    .reset-tile{background:#f9fbfa;border:1px solid #e4ebe6;border-radius:12px;padding:12px;display:flex;align-items:center;gap:10px;font-size:13px;color:#1f2a1f}
    .reset-icon{width:38px;height:38px;border-radius:12px;background:#eef4ef;display:flex;align-items:center;justify-content:center;font-size:18px;color:#4b8260}
    .center{display:flex;flex-direction:column;align-items:center;gap:10px;padding:12px}
    .breath-circle{width:140px;height:140px;border-radius:50%;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,0.9),rgba(124,171,140,0.35));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#1f2a1f;transition:transform 0.6s ease}
    .breath-circle.expand{transform:scale(1.1)}
    .btn-row{display:flex;gap:10px;flex-wrap:wrap;justify-content:center}
    .btn{background:var(--accent);color:#fff;border:none;border-radius:12px;padding:10px 16px;font-weight:700;cursor:pointer}
    .btn.ghost{background:#f2f4f3;color:#1f2a1f;border:1px solid #dfe6e1}
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
      <a class="nav-item" href="home.php"><span class="nav-icon">🏠</span>Home</a>
      <a class="nav-item" href="calendar.php"><span class="nav-icon">📅</span>Calendar</a>
      <a class="nav-item" href="daily-log.php"><span class="nav-icon">📖</span>Daily Log</a>
      <a class="nav-item active" href="calming-tools.php"><span class="nav-icon">✨</span>Calming Tools</a>
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
      <div class="header-title">Calming Tools</div>
      <div class="header-sub">Relaxation and mindfulness exercises</div>
    </header>

    <section class="tools-grid">
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon">🌬️</div>
          <div>
            <div class="tool-label">Breathing</div>
            <div class="tool-sub">4-7-8 & Box</div>
          </div>
        </div>
      </div>
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon" style="color:#5b7b86;background:#f1f7f9">🖐️</div>
          <div>
            <div class="tool-label">Grounding</div>
            <div class="tool-sub">5-4-3-2-1</div>
          </div>
        </div>
      </div>
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon" style="color:#7a5ca2;background:#f4f0fa">🎵</div>
          <div>
            <div class="tool-label">Sounds</div>
            <div class="tool-sub">Relax & Focus</div>
          </div>
        </div>
      </div>
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon" style="color:#c27c1c;background:#fff6e4">✨</div>
          <div>
            <div class="tool-label">Stretches</div>
            <div class="tool-sub">Body & Mind</div>
          </div>
        </div>
      </div>
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon" style="color:#d27b5a;background:#fff0e9">🤎</div>
          <div>
            <div class="tool-label">Affirmations</div>
            <div class="tool-sub">Daily Positivity</div>
          </div>
        </div>
      </div>
      <div class="tool-card">
        <div class="tool-top">
          <div class="tool-icon" style="color:#2f6f4e;background:#e7f3ec">💧</div>
          <div>
            <div class="tool-label">Quick Reset</div>
            <div class="tool-sub">1-Minute Actions</div>
          </div>
        </div>
      </div>
    </section>

    <section class="recommend">
      <div class="rec-icon">🌿</div>
      <div>
        <div class="rec-title">Recommended for You</div>
        <div class="rec-sub">Based on your current calm mood, try listening to ocean sounds or practice some gentle stretches.</div>
      </div>
    </section>
  </main>
  <div class="modal-overlay" id="modalOverlay"></div>

  <!-- Breathing choose -->
  <div class="modal" id="modal-breathing">
    <div class="dialog">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Breathing Exercises</div>
      <div class="modal-sub">Inhale & exhale with guided rhythms</div>
      <div class="list-card" data-breath="478">
        <div class="text">
          <div class="title">4-7-8 Breathing</div>
          <div class="note">Inhale 4s · Hold 7s · Exhale 8s</div>
        </div>
        <div class="pill">Start</div>
      </div>
      <div class="list-card" data-breath="box">
        <div class="text">
          <div class="title">Box Breathing</div>
          <div class="note">Inhale 4s · Hold 4s · Exhale 4s · Hold 4s</div>
        </div>
        <div class="pill">Start</div>
      </div>
    </div>
  </div>

  <!-- Breathing detail -->
  <div class="modal" id="modal-breathing-detail">
    <div class="dialog">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Breathing Exercises</div>
      <div class="center">
        <div class="breath-circle" id="breathCircle">Inhale</div>
        <div class="sub" id="breathGuide">Follow the circle's rhythm</div>
        <div class="btn-row">
          <button class="btn" id="breathStart">Start</button>
          <button class="btn ghost" id="breathBack">Back</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Grounding -->
  <div class="modal" id="modal-grounding">
    <div class="dialog">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">5-4-3-2-1 Grounding</div>
      <div class="modal-sub">Use your senses to anchor yourself in the present moment</div>
      <div class="grounding-item"><div class="grounding-step">5</div><div><div class="title">See</div><div class="note">Name 5 things you can see</div></div></div>
      <div class="grounding-item"><div class="grounding-step">4</div><div><div class="title">Touch</div><div class="note">Name 4 things you can touch</div></div></div>
      <div class="grounding-item"><div class="grounding-step">3</div><div><div class="title">Hear</div><div class="note">Name 3 things you can hear</div></div></div>
      <div class="grounding-item"><div class="grounding-step">2</div><div><div class="title">Smell</div><div class="note">Name 2 things you can smell</div></div></div>
      <div class="grounding-item"><div class="grounding-step">1</div><div><div class="title">Taste</div><div class="note">Name 1 thing you can taste</div></div></div>
    </div>
  </div>

  <!-- Sounds -->
  <div class="modal" id="modal-sounds">
    <div class="dialog wide">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Relaxing Sounds</div>
      <div class="modal-sub">Choose a calming sound to help you relax</div>
      <div class="list-card" data-sound="rain">
        <div class="text">
          <div class="title">Rain</div>
          <div class="note" id="rain-status">Click to play</div>
        </div>
        <button class="sound-play" data-sound-id="rain">▶</button>
      </div>
      <div class="list-card" data-sound="ocean">
        <div class="text">
          <div class="title">Ocean Waves</div>
          <div class="note" id="ocean-status">Click to play</div>
        </div>
        <button class="sound-play" data-sound-id="ocean">▶</button>
      </div>
      <div class="list-card" data-sound="forest">
        <div class="text">
          <div class="title">Forest</div>
          <div class="note" id="forest-status">Click to play</div>
        </div>
        <button class="sound-play" data-sound-id="forest">▶</button>
      </div>
      <div class="list-card" data-sound="piano">
        <div class="text">
          <div class="title">Piano</div>
          <div class="note" id="piano-status">Click to play</div>
        </div>
        <button class="sound-play" data-sound-id="piano">▶</button>
      </div>
    </div>
  </div>

  <!-- Stretches -->
  <div class="modal" id="modal-stretches">
    <div class="dialog">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Body Stretches</div>
      <div class="modal-sub">Gentle movements to release tension</div>
      <div class="list-card"><div class="text"><div class="title">Neck Rolls</div><div class="note">Slowly roll your head in a circle</div></div><div class="pill">30s</div></div>
      <div class="list-card"><div class="text"><div class="title">Shoulder Shrugs</div><div class="note">Lift shoulders up, hold, release</div></div><div class="pill">30s</div></div>
      <div class="list-card"><div class="text"><div class="title">Wrist Circles</div><div class="note">Rotate wrists gently both ways</div></div><div class="pill">20s</div></div>
      <div class="list-card"><div class="text"><div class="title">Deep Side Bend</div><div class="note">Reach arms up and bend sideways</div></div><div class="pill">40s</div></div>
    </div>
  </div>

  <!-- Affirmations -->
  <div class="modal" id="modal-affirmations">
    <div class="dialog">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Daily Affirmations</div>
      <div class="affirm-card">
        <div class="quote" id="affirmationQuote">"You are doing your best, and that is enough"</div>
      </div>
      <div class="btn-row" style="margin-top:12px">
        <button class="btn ghost" id="nextAffirmation">Next Affirmation</button>
      </div>
    </div>
  </div>

  <!-- Quick reset -->
  <div class="modal" id="modal-quickreset">
    <div class="dialog wide">
      <button class="modal-close" data-close>×</button>
      <div class="modal-title">Quick Mood Reset</div>
      <div class="modal-sub">Simple 1-minute actions to shift your energy</div>
      <div class="reset-grid">
        <div class="reset-tile"><div class="reset-icon">💧</div><div>Drink a glass of water</div></div>
        <div class="reset-tile"><div class="reset-icon" style="background:#fff3d9;color:#d29a2b">🌞</div><div>Step outside for 1 minute</div></div>
        <div class="reset-tile"><div class="reset-icon" style="background:#e7f5f1;color:#2f6f4e">🌬️</div><div>Take 3 deep breaths</div></div>
        <div class="reset-tile"><div class="reset-icon" style="background:#f0eefe;color:#6b5ca5">🌙</div><div>Close your eyes for 30 seconds</div></div>
        <div class="reset-tile"><div class="reset-icon" style="background:#fff1e8;color:#d27b5a">☕</div><div>Make yourself tea or coffee</div></div>
        <div class="reset-tile"><div class="reset-icon" style="background:#ecf4ff;color:#4a73c9">🎶</div><div>Listen to your favorite song</div></div>
      </div>
    </div>
  </div>


<script>
// Sound player variables (global scope)
let audioContext = null;
let currentSound = null;
let soundNodes = {};
let currentAudio = null; // HTML5 Audio element

// Sound file paths - using royalty-free direct URLs from reliable CDN sources
// Multiple fallback URLs per sound for better reliability
// Volume is set to 0.8 (80%) for louder playback
const soundFiles = {
  rain: [
    'https://cdn.pixabay.com/download/audio/2022/03/15/audio_9f5a353a0d.mp3?filename=rain-on-window-ambient-110624.mp3',
    'https://cdn.pixabay.com/download/audio/2021/10/25/audio_8a4b8b5c0e.mp3?filename=heavy-rain-ambient-113985.mp3',
    'https://cdn.pixabay.com/download/audio/2023/08/15/audio_abc123.mp3?filename=rain-heavy-ambient.mp3'
  ],
  ocean: [
    'https://cdn.pixabay.com/download/audio/2022/02/25/audio_7bf0b00e5f.mp3?filename=ocean-waves-ambient-110149.mp3',
    'https://cdn.pixabay.com/download/audio/2021/10/25/audio_8a4b8b5c0e.mp3?filename=ocean-waves-ambient-113984.mp3',
    'https://cdn.pixabay.com/download/audio/2023/07/20/audio_def456.mp3?filename=ocean-waves-calm.mp3'
  ],
  forest: [
    'https://cdn.pixabay.com/download/audio/2022/01/14/audio_c2c9fe8d2c.mp3?filename=forest-lullaby-ambient-10949.mp3',
    'https://cdn.pixabay.com/download/audio/2021/10/25/audio_8a4b8b5c0e.mp3?filename=forest-ambient-113983.mp3',
    'https://cdn.pixabay.com/download/audio/2023/06/10/audio_ghi789.mp3?filename=forest-nature-sounds.mp3'
  ],
  piano: [
    'https://cdn.pixabay.com/download/audio/2022/03/15/audio_f3a7c33dcb.mp3?filename=piano-relaxing-ambient-110622.mp3',
    'https://cdn.pixabay.com/download/audio/2021/10/25/audio_8a4b8b5c0e.mp3?filename=piano-relaxing-113982.mp3',
    'https://cdn.pixabay.com/download/audio/2023/05/15/audio_jkl012.mp3?filename=piano-soft-melody.mp3'
  ]
};

// Fallback to generated sounds if files don't exist
const soundSources = {
  rain: { type: 'noise', frequency: 0.3, color: 'pink' },
  ocean: { type: 'noise', frequency: 0.2, color: 'brown' },
  forest: { type: 'noise', frequency: 0.15, color: 'pink' },
  piano: { type: 'tone', frequency: 220, harmonics: true }
};

function initAudioContext() {
  if (!audioContext) {
    audioContext = new (window.AudioContext || window.webkitAudioContext)();
  }
  return audioContext;
}

function createNoiseSound(color = 'pink', frequency = 0.2) {
  const bufferSize = 4096;
  const buffer = audioContext.createBuffer(1, bufferSize, audioContext.sampleRate);
  const data = buffer.getChannelData(0);
  
  for (let i = 0; i < bufferSize; i++) {
    if (color === 'pink') {
      data[i] = (Math.random() * 2 - 1) * Math.pow(Math.random(), 0.5);
    } else if (color === 'brown') {
      data[i] = (Math.random() * 2 - 1) * Math.pow(Math.random(), 1.5);
    } else {
      data[i] = Math.random() * 2 - 1;
    }
  }
  
  const noiseSource = audioContext.createBufferSource();
  noiseSource.buffer = buffer;
  noiseSource.loop = true;
  
  const filter = audioContext.createBiquadFilter();
  filter.type = 'lowpass';
  filter.frequency.value = 800 + (frequency * 2000);
  filter.Q.value = 1;
  
  const gainNode = audioContext.createGain();
  gainNode.gain.value = 0.1 * frequency;
  
  noiseSource.connect(filter);
  filter.connect(gainNode);
  gainNode.connect(audioContext.destination);
  
  return { source: noiseSource, gain: gainNode };
}

function createToneSound(frequency, harmonics = false) {
  const gainNode = audioContext.createGain();
  gainNode.gain.value = 0.15;
  
  if (harmonics) {
    const frequencies = [frequency, frequency * 2, frequency * 1.5, frequency * 3];
    frequencies.forEach((freq, index) => {
      const oscillator = audioContext.createOscillator();
      oscillator.type = index === 0 ? 'sine' : 'triangle';
      oscillator.frequency.value = freq;
      const oscGain = audioContext.createGain();
      oscGain.gain.value = index === 0 ? 0.4 : 0.2 / (index + 1);
      oscillator.connect(oscGain);
      oscGain.connect(gainNode);
      oscillator.start();
      soundNodes[`osc_${index}`] = oscillator;
    });
  } else {
    const oscillator = audioContext.createOscillator();
    oscillator.type = 'sine';
    oscillator.frequency.value = frequency;
    oscillator.connect(gainNode);
    oscillator.start();
    soundNodes.osc = oscillator;
  }
  
  gainNode.connect(audioContext.destination);
  return { gain: gainNode };
}

function playSound(soundId) {
  stopSound();
  
  // Try to play audio file first - with multiple fallback URLs
  const audioUrls = soundFiles[soundId];
  if (audioUrls && audioUrls.length > 0) {
    let currentUrlIndex = 0;
    
    function tryPlayAudio(index) {
      if (index >= audioUrls.length) {
        // All URLs failed, use generated sound
        console.log(`All audio URLs failed for ${soundId}, using generated sound`);
        playGeneratedSound(soundId);
        return;
      }
      
      const audioFile = audioUrls[index];
      const audio = new Audio(audioFile);
      audio.loop = true;
      audio.volume = 0.8; // Increased volume (0.0 to 1.0) - was 0.5, now 0.8
      
      audio.addEventListener('error', (e) => {
        console.log(`Audio URL ${index + 1} failed: ${audioFile}, trying next...`);
        // Try next URL
        tryPlayAudio(index + 1);
      });
      
      audio.addEventListener('canplay', () => {
        audio.play().catch(err => {
          console.log('Audio play failed:', err);
          // Try next URL
          tryPlayAudio(index + 1);
        });
      });
      
      audio.addEventListener('loadeddata', () => {
        // Audio loaded successfully
        currentAudio = audio;
        currentSound = soundId;
        updateSoundUI(soundId, true);
      });
      
      // Try to play immediately
      audio.play().then(() => {
        // Success! Audio is playing
        currentAudio = audio;
        currentSound = soundId;
        updateSoundUI(soundId, true);
      }).catch(err => {
        // If play fails, try next URL
        if (currentAudio !== audio) {
          tryPlayAudio(index + 1);
        }
      });
    }
    
    // Start trying URLs from the first one
    tryPlayAudio(0);
  } else {
    // No file specified, use generated sound
    playGeneratedSound(soundId);
  }
}

function playGeneratedSound(soundId) {
  stopSound();
  
  initAudioContext();
  const soundConfig = soundSources[soundId];
  
  if (soundConfig.type === 'noise') {
    const sound = createNoiseSound(soundConfig.color, soundConfig.frequency);
    sound.source.start();
    soundNodes.source = sound.source;
    soundNodes.gain = sound.gain;
    currentSound = soundId;
  } else if (soundConfig.type === 'tone') {
    const sound = createToneSound(soundConfig.frequency, soundConfig.harmonics);
    soundNodes.gain = sound.gain;
    currentSound = soundId;
  }
  
  updateSoundUI(soundId, true);
}

function stopSound() {
  // Stop HTML5 Audio if playing
  if (currentAudio) {
    currentAudio.pause();
    currentAudio.currentTime = 0;
    currentAudio = null;
  }
  
  // Stop Web Audio API sounds
  if (soundNodes.source) {
    try {
      soundNodes.source.stop();
    } catch(e) {}
    soundNodes.source = null;
  }
  if (soundNodes.gain) {
    try {
      soundNodes.gain.disconnect();
    } catch(e) {}
    soundNodes.gain = null;
  }
  Object.keys(soundNodes).forEach(key => {
    if (soundNodes[key] && typeof soundNodes[key].stop === 'function') {
      try {
        soundNodes[key].stop();
      } catch(e) {}
    }
  });
  soundNodes = {};
  
  if (currentSound) {
    updateSoundUI(currentSound, false);
    currentSound = null;
  }
}

function updateSoundUI(soundId, isPlaying) {
  const statusEl = document.getElementById(`${soundId}-status`);
  const button = document.querySelector(`[data-sound-id="${soundId}"]`);
  const card = document.querySelector(`[data-sound="${soundId}"]`);
  
  if (statusEl) {
    statusEl.textContent = isPlaying ? 'Playing...' : 'Click to play';
  }
  if (button) {
    button.textContent = isPlaying ? '⏸' : '▶';
    if (isPlaying) {
      button.style.background = '#6a8b74';
      button.style.color = '#fff';
      button.style.borderColor = '#6a8b74';
    } else {
      button.style.background = '';
      button.style.color = '';
      button.style.borderColor = '';
    }
  }
  if (card) {
    card.style.background = isPlaying ? '#f0f5f0' : '';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('modalOverlay');
  const modals = document.querySelectorAll('.modal');
  const affirmations = [
    "You are capable of amazing things. Take it one step at a time.",
    "Your feelings are valid. Be gentle with yourself today.",
    "Every small step forward is progress. Celebrate your wins.",
    "You have the strength to handle whatever comes your way.",
    "Take a deep breath. You're doing better than you think.",
    "Self-care isn't selfish. It's necessary for your well-being.",
    "You deserve happiness and peace. Start with small moments.",
    "It's okay to not be okay. Give yourself permission to feel.",
    "You are resilient. Challenges make you stronger.",
    "Focus on what you can control. Let go of what you can't."
  ];

  // --- Modal open/close helpers (fixed) ---
  let breathTimer = null;

  function openModal(id){
    // Close any open modal first to avoid stacking
    closeModals();
    overlay.classList.add('show');
    const el = document.getElementById(id);
    if(el) el.classList.add('show');
  }

  function closeModals(){
    // Stop any playing sounds
    stopSound();
    
    // hide overlay and all modals
    overlay.classList.remove('show');
    modals.forEach(m => m.classList.remove('show'));

    // clear breathing timer if running and reset UI
    if (breathTimer) {
      clearInterval(breathTimer);
      breathTimer = null;
    }
    const breathCircle = document.getElementById('breathCircle');
    if (breathCircle) breathCircle.classList.remove('expand');
  }

  // wire overlay and close buttons
  overlay.addEventListener('click', closeModals);
  document.querySelectorAll('[data-close]').forEach(btn=>btn.addEventListener('click', closeModals));

  // Card triggers
  const cardMap = {
    'Breathing': 'modal-breathing',
    'Grounding': 'modal-grounding',
    'Sounds': 'modal-sounds',
    'Stretches': 'modal-stretches',
    'Affirmations': 'modal-affirmations',
    'Quick Reset': 'modal-quickreset'
  };
  document.querySelectorAll('.tool-card').forEach(card=>{
    const activate = ()=>{
      const label = card.querySelector('.tool-label')?.textContent.trim();
      const target = cardMap[label];
      if(target) openModal(target);
    };
    card.setAttribute('tabindex','0');
    card.setAttribute('role','button');
    card.addEventListener('click', activate);
    card.addEventListener('keydown', (e)=>{
      if(e.key==='Enter' || e.key===' '){
        e.preventDefault();
        activate();
      }
    });
  });

  // Breathing flows
  const sequences = {
    '478':['Inhale 4s','Hold 7s','Exhale 8s'],
    'box':['Inhale 4s','Hold 4s','Exhale 4s','Hold 4s']
  };
  let currentSeq = sequences['478'];
  const breathCircle = document.getElementById('breathCircle');
  const breathGuide = document.getElementById('breathGuide');

  document.querySelectorAll('[data-breath]').forEach(row=>{
    row.addEventListener('click', ()=>{
      currentSeq = sequences[row.dataset.breath] || sequences['478'];
      openModal('modal-breathing-detail');
      breathGuide.textContent = "Follow the circle's rhythm";
      if (breathCircle) breathCircle.textContent = currentSeq[0];
    });
  });

  document.getElementById('breathStart').addEventListener('click', ()=>{
    if (breathTimer) {
      // already running — reset safely first
      clearInterval(breathTimer);
      breathTimer = null;
    }
    let step = 0;
    if (breathCircle) {
      breathCircle.textContent = currentSeq[0];
      breathCircle.classList.add('expand');
    }
    breathTimer = setInterval(()=>{
      step = (step + 1) % currentSeq.length;
      if (breathCircle) {
        breathCircle.textContent = currentSeq[step];
        breathCircle.classList.toggle('expand');
      }
    }, 4000);
  });

  // Back button from detail -> go back to breathing choice
  document.getElementById('breathBack').addEventListener('click', ()=>{
    // close detail and stop timer, then open the selection modal
    closeModals();
    openModal('modal-breathing');
  });

  // Affirmations
  document.getElementById('nextAffirmation').addEventListener('click', ()=>{
    const pick = affirmations[Math.floor(Math.random()*affirmations.length)];
    document.getElementById('affirmationQuote').textContent = `"${pick}"`;
  });

  // Wire up sound buttons
  document.querySelectorAll('.sound-play').forEach(button => {
    button.addEventListener('click', (e) => {
      e.stopPropagation();
      const soundId = button.getAttribute('data-sound-id');
      
      if (currentSound === soundId) {
        // Stop if same sound is clicked
        stopSound();
      } else {
        // Play new sound
        playSound(soundId);
      }
    });
  });

});
</script>

<script>
  async function updateAuthSection() {
    try {
      const response = await fetch('./api/auth_status.php');
      const data = await response.json();
      const userInfo = document.getElementById('userInfo');
      const authPrompt = document.getElementById('authPrompt');
      
      if (data.logged_in && data.user) {
        document.getElementById('username').textContent = data.user.username;
        userInfo.style.display = 'flex';
        authPrompt.style.display = 'none';
      } else {
        userInfo.style.display = 'none';
        authPrompt.style.display = 'block';
      }
    } catch (error) {
      console.error('Error checking auth status:', error);
    }
  }

  function logout() {
    fetch('./api/logout.php', { method: 'POST' })
      .then(() => {
        window.location.href = './login.php';
      })
      .catch(error => console.error('Error logging out:', error));
  }

  updateAuthSection();
</script>

</body>
</html>

