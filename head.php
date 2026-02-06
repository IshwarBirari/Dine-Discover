<?php
/** @var string $title */
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="assets/style.css"/>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo">DD</div>
      <div>
        <h1><?= e(APP_NAME) ?></h1>
        <div class="small">Restaurant search using Yelp Fusion API</div>
      </div>
    </div>
    <div class="nav">
      <a href="index.php">Search</a>
      <a href="favorites.php">Favorites</a>
    </div>
  </div>
