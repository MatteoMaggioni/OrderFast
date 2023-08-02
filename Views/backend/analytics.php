<form id="analytics" name="analytics" action='/analytics' method="post">

    <div class="row justify-content-around" style="text-align:center">
        <div class="col-4">
            <label for="startDate">Inizio intervallo</label>
            <input required id="startDate" name="startDate" class="form-control" type="date" />
        </div>
        <div class="col-4">
            <label for="endDate">Fine intervallo</label>
            <input required id="endDate" name="endDate" class="form-control" type="date" />
        </div>
        <div class="col-1 align-self-center">
            <button class="btn btn-success btn-sm" type="submit" alt="salva" title="salva"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
        </div>
    </div>
    <br>

    <h3>Le date selezionate sono: <?= $startDate ?> e <?= $endDate ?></h3>


</form>

<div class="analyticsCards">
    <h2 class="analyticstitles">Leadboard</h2>
    <div class="row">
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Vendite</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            € <?= $totalRevenew ?>
                        </div>
                        <div class="col">
                            <?= $revenewPercentage ?>% <?php if ($revenewPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <a href="/soldProducts/<?= $startDate ?>/<?= $endDate ?>" class="btn btn-info btn-sm" style="position:absolute; right:0" type="submit" alt="salva" title="salva"><i class="far fa-eye fa-fw" aria-hidden="true"></i></a>
                <div class="card-body">
                    <h5 class="card-title">Prodotti</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $totalProducts ?>
                        </div>
                        <div class="col">
                            <?= $productsPercentage ?>% <?php if ($productsPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Ordini</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $totalOrders ?>
                        </div>
                        <div class="col">
                            <?= $ordersPercentage ?>% <?php if ($ordersPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
            <a href="/soldTables/<?= $startDate ?>/<?= $endDate ?>" class="btn btn-info btn-sm" style="position:absolute; right:0" type="submit" alt="salva" title="salva"><i class="far fa-eye fa-fw" aria-hidden="true"></i></a>
                <div class="card-body">
                    <h5 class="card-title">Tavoli</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $totalTables ?>
                        </div>
                        <div class="col">
                            <?= $tablesPercentage ?>% <?php if ($tablesPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="analyticsCards">
    <h2 class="analyticstitles">Prodotti venduti</h2>
    <div class="row justify-content-around">
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Più venduto</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $mostSold['product_name'] ?>
                        </div>
                        <div class="col">
                            <?= $mostSold['quantity'] ?>
                        </div>
                        <div class="col">
                            <?= $mostSoldPercentage ?>% <?php if ($mostSoldPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Meno venduto</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $lessSold['product_name'] ?>
                        </div>
                        <div class="col">
                            <?= $lessSold['quantity'] ?>
                        </div>
                        <div class="col">
                            <?= $lessSoldPercentage ?>% <?php if ($lessSoldPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="analyticsCards">
    <h2 class="analyticstitles">Ordini</h2>
    <div class="row justify-content-around">
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Valore medio dell'ordine</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            € <?= $avgOrder ?>
                        </div>
                        <div class="col">
                            <?= $avgPercentage ?>% <?php if ($avgPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Prodotti medi dell'ordine</h5>
                    <div class="row justify-content-between">
                        <div class="col">
                            <?= $avgOrderProducts ?>
                        </div>
                        <div class="col">
                            <?= $avgProductsPercentage ?>% <?php if ($avgProductsPercentage > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($metodi) : ?>

    <div class="analyticsCards">
        <h2 class="analyticstitles">Metodi di pagamento</h2>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            canvas {
                display: block;
                margin: 0 auto;
            }
        </style>

        <div class="row justify-content-around">
            <div class="col-sm-12 col-md-3">
                <h3>Numero ordini effettuati</h3>
                <canvas id="pieChartquantity"></canvas>
            </div>
            <div class="col-sm-12 col-md-5 align-self-center">
                <div class="card text-center">
                    <div class="container card-body">
                        <div class="row justify-content-between" style="font-weight: bold">
                            <div class="col">
                                Metodo
                            </div>
                            <div class="col">
                                Numero di ordini
                            </div>
                            <div class="col">
                                Prezzo totale
                            </div>
                        </div>
                        <?php foreach ($metodi as $metodo) : ?>
                            <div class="row justify-content-between">
                                <div class="col">
                                    <?= $metodo['method'] ?>
                                </div>
                                <div class="col">
                                    <?= $metodo['quantity'] ?> -
                                    <?= $metodo['qpercentage'] ?>% <?php if ($metodo['qpercentage'] > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                                </div>
                                <div class="col">
                                    <?= $metodo['total'] ?> -
                                    <?= $metodo['tpercentage'] ?>% <?php if ($metodo['qpercentage'] > 0) : ?><i class="fa fa-long-arrow-up" aria-hidden="true"></i><?php else : ?><i class="fa fa-long-arrow-down" aria-hidden="true"></i><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <h3>Prezzo totale</h3>
                <canvas id="pieCharttotal"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Recupera i dati dal PHP nell'array JavaScript
        var data = <?php echo json_encode($metodi); ?>;

        // numero di ordini
        var canvasQ = document.getElementById('pieChartquantity');
        // Crea il grafico a torta
        var ctx = canvasQ.getContext('2d');
        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                datasets: [{
                    data: data.map(item => item.quantity),
                    backgroundColor: ['red', 'blue', 'green'] // Colori delle fette del grafico
                }],
                labels: data.map(item => item.method) // Nomi delle fette del grafico
            },
        });

        // prezzo totale
        var canvasT = document.getElementById('pieCharttotal');
        // Crea il grafico a torta
        var ctx = canvasT.getContext('2d');
        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                datasets: [{
                    data: data.map(item => item.total),
                    backgroundColor: ['red', 'blue', 'green'] // Colori delle fette del grafico
                }],
                labels: data.map(item => item.method) // Nomi delle fette del grafico
            },
        });
    </script>

<?php endif; ?>