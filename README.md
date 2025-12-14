# Mood Diary – ML-Powered Mood Tracker

Important Instructions — Full Site Overview

Short description: A personal mood-tracking web app — detect mood via camera/microphone or select it manually, attach diary entries and media, tag moods, view and edit entries on a monthly calendar, and track daily mood trends and summaries.

## Features

- Facial Mood Detection: client-side face-expression models are included under `models/` and used by the UI to detect emotions such as Happy, Sad, Angry, Tired, and Neutral.
- Audio Mood Detection: audio analysis is integrated and contributes to the combined mood score (see `home.php` / `daily-log.php`).
- Diary + Media Logging: write diary entries and attach photos/videos (uploads stored under `uploads/`).
- Calendar Mood Tracking: full-month calendar with per-day mood indicators and modal diary previews (`calendar.php`).
- Emotion Progress Charts: weekly and emotion-frequency charts are implemented in `progress.php` (uses Chart.js).
- Quick Actions & Navigation: quick access to diary, calendar, flashcards, and calming tools in the UI.
- Emotion Flashcards: interactive flashcards and emotion descriptions are provided in `flashcard.php`.
- Calming Tools: breathing exercises, sounds, and grounding techniques available in `calming-tools.php`.
- Speech-to-Text (STT): the app uses the Web Speech API (`SpeechRecognition`) where supported by the browser (support varies by browser).

## Tech-Stack

- Backend: PHP with PDO for DB access.
- Machine Learning: `php-ai/php-ml` is included under `vendor/` and lightweight face-expression model shards are bundled in `models/` for client-side inference.
- Frontend: HTML, CSS, JavaScript; charts use Chart.js (`progress.php`).
- DB: MySQL / MariaDB schema is available in `database/mood_tracker.sql`.


This repository contains a small mood-tracking web app built with PHP (PDO), vanilla JavaScript, and a MariaDB/MySQL database. Core pages are:

- `home.php` — quick detection and save UI
- `daily-log.php` — full editor with live detection, diary, tags and media upload
- `calendar.php` — monthly overview and modal diary previews


# Live Site Link 

```bash
moodtracker-production-e1c4.up.railway.app
```

# Video Presentation Link 

```bash

```

# Canva Presentation Link 

```bash
https://www.canva.com/design/DAG7fig4MNM/ZQp9FnZyPqO7l7OZAp0YLg/edit?utm_content=DAG7fig4MNM&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton
```

Quick start (local dev)

1. Copy the DB dump and import into your local MariaDB/MySQL instance (phpMyAdmin or CLI):

```bash
mysql -u root -p mood_tracker < database/mood_tracker.sql
```

2. Configure DB credentials in `config/db.php`.
3. Ensure PHP 8.0+ and Composer are available. Install vendor packages (if any change):

```bash
composer install
```

4. Run the normalization and (optionally) dedupe scripts before heavy testing:

```bash
php scripts/normalize_mood_logs.php
php scripts/dedupe_mood_logs.php    # optional, keeps the most recent row per user/date
```

Developer commands

- Quick no-browser test (inserts/updates today's row for user 1 and prints API outputs):

```bash
php scripts/test_end_to_end.php
```

- Check today's rows per user:

```bash
php scripts/check_today_rows.php
```

Data model & important behavior

- Canonical mood rows: `mood_logs` is the source-of-truth for a date's mood. `mood_tags` is for free-form tags.
- `api/save_mood.php`: now behaves upsert-like — it updates the most recent `mood_logs` row for the user/date (merging `meta` fields) or inserts a new row if none exists.
- `api/get_daily_log.php` and `api/get_today_mood.php`: these endpoints normalize mood names (TitleCase) and convert fractional confidences/scores (0..1) to percentages (0..100) for consistent UI display.
- `api/get_month_moods.php`: returns the most recent mood row per date and exposes `selected_mood` (derived from `meta`) so the calendar can prefer that when choosing the day's emoji.

UI sync

- Same-tab notifications: `window.dispatchEvent(new CustomEvent('dayUpdated', { detail: { date } }))`.
- Cross-tab notifications: `localStorage.setItem('dayUpdated', JSON.stringify({ date, ts: Date.now() }))` + `window.addEventListener('storage', ...)`.

Security & production notes

- Always run migrations and dedupe on a copy of production data first; take a full DB backup before running any script.
- Use HTTPS in production, restrict file upload directories (`uploads/`) and set appropriate permissions.
- Turn off error display on production (use `display_errors = Off` in php.ini) and rely on logs.

Suggested next steps (optional)

- Add a DB migration to create a `UNIQUE KEY (user_id, date)` and a safe rollback strategy (requires handling existing duplicates first).
- Add automated tests for `api/*` endpoints (simple PHPUnit or integration scripts).

Troubleshooting

- If the calendar or daily log shows stale values: run `php scripts/normalize_mood_logs.php` and, if needed, `php scripts/dedupe_mood_logs.php` and verify `api/get_month_moods.php` returns the most recent row per date.
- Use DevTools console to trace the `dayUpdated` CustomEvent and `storage` events for cross-tab sync. Look for these logs in the console:
  - `calendar: received dayUpdated for <date>`
  - `daily-log: refreshed due to dayUpdated event`



---

Last updated: December 14, 2025
