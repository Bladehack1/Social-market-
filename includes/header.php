<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * FILE: header.php
 * DEVELOPER: BLADE
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>SocialMarket | Digital Growth</title>
    <meta name="description" content="Plateforme de services digitaux premium par Blade.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-rocket"></i> SOCIAL<span>MARKET</span>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
            <div class="user-info">
                <p class="user-name"><?php echo e($_SESSION['username']); ?></p>
                <p class="user-role"><?php echo ucfirst($_SESSION['role']); ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <small class="nav-label">MENU PRINCIPAL</small
