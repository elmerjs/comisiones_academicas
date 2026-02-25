<?php

require('include/headerz.php');
?>
<!doctype html>
<html lang="en">
<head>
    <title>Gestión de Directivos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
</head>
<body>
    <br>
<div class="container">
    <h1 class="mt-4">Gestión de Directivos</h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="rector-tab" data-toggle="tab" href="#rector" role="tab" aria-controls="rector" aria-selected="false">Rectores</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="vicerrector-tab" data-toggle="tab" href="#vicerrector" role="tab" aria-controls="vicerrector" aria-selected="false">Vicerrectores</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="revisa-tab" data-toggle="tab" href="#revisa" role="tab" aria-controls="revisa" aria-selected="false">Revisores</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="rector" role="tabpanel" aria-labelledby="rector-tab">
            <?php include 'gestion_rectores.php'; ?>
        </div>
        <div class="tab-pane fade" id="vicerrector" role="tabpanel" aria-labelledby="vicerrector-tab">
            <?php include 'gestion_vicerrectores.php'; ?>
        </div>
        <div class="tab-pane fade" id="revisa" role="tabpanel" aria-labelledby="revisa-tab">
            <?php include 'gestion_revisores.php'; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    // Restaurar la pestaña activa desde el almacenamiento local
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('#myTab a[href="' + activeTab + '"]').tab('show');
    } else {
        $('#myTab a:first').tab('show');
    }

    // Cambiar la pestaña activa en el almacenamiento local cuando se hace clic en una pestaña
    $('#myTab a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        var activeTab = $(this).attr('href');
        localStorage.setItem('activeTab', activeTab);
    });
});
</script>
</body>
</html>
