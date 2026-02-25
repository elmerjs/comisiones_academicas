<?php require('include/headerz.php'); ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrÁficos de Cupos Comisiones</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <!-- Estilos CSS personalizados -->
    <link rel="stylesheet" media="screen" href="../css/cssprueban2.css">
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" media="screen" href="css/cssprueban2.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Espaciado para el menú, ajusta el valor de 70px según el alto de tu menú */
        #contenido {
            position: absolute;
            top: 70px; /* Ajusta este valor según el alto del menú */
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>

<body>
    <div id="contenido">
        <iframe title="APP comisiones academicas" 
                src="https://app.powerbi.com/view?r=eyJrIjoiMjA2ZjBjMTItZTJhNi00NDNkLWJkOGYtOTlhMjE2ZTI0NmIzIiwidCI6ImU4MjE0OTM3LTIzM2ItNGIzNi04NmJmLTBiNWYzMzM3YmVlMSIsImMiOjF9" 
                allowfullscreen="true">
        </iframe>
    </div>

    <script>
        $(document).ready(function() {
            $('#reporteTable').DataTable();
        });
    </script>
</body>
</html>
