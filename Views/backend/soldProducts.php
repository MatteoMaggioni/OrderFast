<input class="form-control" style="display: inline; float: right;" type="text" id="search" onkeyup="search('products_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">

<table class="table" id="products_table">
    <thead>
        <th>Nome prodotto</th>
        <th># vendite</th>
        <th>Tot vendite</th>
        <th>Categoria</th>
        <th>Magazzino</th>
    </thead>
    <tbody>
        <?php foreach ($products as $p) : ?>
            <tr>
                <td><?= $p['product_name'] ?></td>
                <td><?= $p['quantity_sold'] ?></td>
                <td><?= $p['total'] ?></td>
                <td><?= $p['category'] ?></td>
                <td><?= $p['magazzino'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    var table = document.getElementById("products_table");
    var headers = table.getElementsByTagName("th");
    var rows = Array.from(table.rows);

    // Aggiungi l'evento di click a ogni intestazione
    for (var i = 0; i < headers.length; i++) {
        headers[i].addEventListener("click", function() {
            var columnIndex = Array.from(this.parentNode.children).indexOf(this);
            sortTable(columnIndex);
        });
    }

    function sortTable(columnIndex) {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("products_table");
        switching = true;
        /*Make a loop that will continue until
        no switching has been done:*/
        while (switching) {
            //start by saying: no switching is done:
            switching = false;
            rows = table.rows;
            /*Loop through all table rows (except the
            first, which contains table headers):*/
            for (i = 1; i < (rows.length - 1); i++) {
                //start by saying there should be no switching:
                shouldSwitch = false;
                /*Get the two elements you want to compare,
                one from current row and one from the next:*/
                x = rows[i].getElementsByTagName("TD")[columnIndex];
                y = rows[i + 1].getElementsByTagName("TD")[columnIndex];
                //check if the two rows should switch place:
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                /*If a switch has been marked, make the switch
                and mark that a switch has been done:*/
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }
</script>




<title>Grafico a Barre</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="barChart"></canvas>

<script>
    <?php

    // Trasferisci i dati dall'array PHP a JavaScript utilizzando json_encode
    $jsonData = json_encode($products);
    ?>

    var data = JSON.parse('<?php echo addslashes($jsonData); ?>');

    var labels = data.map(function(item) {
        return item.product_name;
    });

    var redData = data.map(function(item) {
        return item.currYear;
    });

    var grayData = data.map(function(item) {
        return item.partYear;
    });

    var ctx = document.getElementById("barChart").getContext("2d");
    var barChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                    label: "Anno corrente",
                    backgroundColor: "red",
                    data: redData
                },
                {
                    label: "Anno passato",
                    backgroundColor: "gray",
                    data: grayData
                }
            ]
        },
        options: {
            responsive: true,
        }
    });
</script>