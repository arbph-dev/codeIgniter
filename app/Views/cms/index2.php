<?php
// app/Views/cms/index2.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workbench Test - Portal</title>
    
    <?= view('cms/libs') ?>   <!-- Garde tes CDN + CSS existants -->
    
    <style>
        body { margin:0; padding:0; background:#f4f7fb; }
    </style>
</head>
<body>

<div id="wb-container" style="min-height:100vh;"></div>

<script type="module">
    import { createPortalWorkbench } from '/assets/js/ui/workbench/layouts/PortalWorkbench.js';
    
    document.addEventListener('DOMContentLoaded', () => {
        createPortalWorkbench('#wb-container');
    });
</script>

</body>
</html>