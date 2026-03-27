<?php
/**
 * DelkaAI — Public landing page.
 */
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DelkaAI — Build with AI APIs</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<nav class="topnav">
  <a href="/" class="topnav-logo">
    <div class="topnav-logo-icon">D</div>
    <span class="topnav-logo-text">DelkaAI</span>
  </a>
  <div class="topnav-actions">
    <a href="/login.php" class="btn btn-ghost btn-sm">Sign In</a>
    <a href="/register.php" class="btn btn-primary btn-sm">Get Started</a>
  </div>
</nav>

<section class="hero">
  <div class="hero-eyebrow">Now in Beta</div>
  <h1 class="hero-title">Build with<br><span>DelkaAI</span> API</h1>
  <p class="hero-sub">
    Powerful AI-powered endpoints for CV generation, cover letter writing,
    conversational support, and visual search — production-ready and easy to integrate.
  </p>
  <div class="hero-actions">
    <a href="/register.php" class="btn btn-primary">Get started for free</a>
    <a href="/docs.php" class="btn btn-ghost">View docs</a>
  </div>
</section>

<section class="features-section">
  <h2>Everything you need to build</h2>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">📄</div>
      <h3>CV Generation</h3>
      <p>Produce professional, ATS-optimised CVs from structured input in seconds using our AI pipeline.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">✉️</div>
      <h3>Cover Letters</h3>
      <p>Generate tailored, compelling cover letters matched to job descriptions and the applicant's experience.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">💬</div>
      <h3>Support Chat</h3>
      <p>Conversational AI chat endpoint for career guidance, HR support, and application assistance.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔍</div>
      <h3>Visual Search</h3>
      <p>Upload images and extract structured insights — analyse screenshots, certificates, or ID documents.</p>
    </div>
  </div>
</section>

<footer class="landing-footer">
  <p>&copy; <?= date('Y') ?> DelkaAI by Frank Dela Nutsukpuie. All rights reserved.</p>
</footer>

<script src="/js/app.js"></script>
</body>
</html>
