<script>
    // impostiamo una variabile per tenere traccia del timer
    var timer;

    // quando il documento è pronto, impostiamo il timer per 2 minuti
    document.addEventListener('DOMContentLoaded', function() {
        timer = setTimeout(function() {
            location.reload();
        }, 120000); // 2 minuti in millisecondi
    });

    // reimpostiamo il timer ad ogni click sull'intera pagina
    document.addEventListener('click', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            location.reload();
        }, 120000);
    });
</script>

<?php if (count($orders) > 0) : ?>
    <h2 style="display: inline">Elenco Ordini </h2>
    <italic style="display: inline"> (in ordine cronologico)</italic>
    <input class="form-control" style="display: inline; float: right" type="text" id="search" onkeyup="search('orders_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">
    <div class="row">
        <div class="col-md-12">
            <table class="table" id="orders_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tavolo</th>
                        <th>Prezzo totale</th>
                        <th>Stato dell'ordine</th>
                        <th>Metodo di pagamento</th>
                        <th>Orario</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <form id="orders_form" name="orders_form" action='/orders' method="post">
                            <input hidden value="<?= $order['id'] ?>" id="order_id" name="order_id">
                            <tr id=<?= $order['id'] ?> <?= $order['status_id'] == 1 ? "style='background-color: lightgoldenrodyellow'" : "" ?> <?= $order['status_id'] == 5 ? "style='background-color: lightgreen'" : "" ?> data-bs-toggle="collapse" data-bs-target="#product<?= $order['id'] ?>" aria-expanded="false" aria-controls="<?= $order['id'] ?>" title="espandi">
                                <td><?= $order['id'] ?></td>
                                <td><?= $order['table_name'] ?></td>
                                <td>€ <?= $order['total_price'] ?></td>
                                <td>
                                    <select required id="status_id" class="form-control" name="status_id">
                                        <?php foreach ($status as $state) : ?>
                                            <option value="<?= $state['id'] ?>" <?= $state['id'] == $order['status_id'] ? "selected" : "" ?>><?= $state['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><?= $order['payment_method_name'] ?></td>
                                <td><?= $order['created'] ?></td>
                                <td><button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#product<?= $order['id'] ?>" aria-expanded="false" aria-controls="<?= $order['id'] ?>" title="espandi">
                                        <i class="far fa-eye fa-fw"></i>
                                    </button>
                                    <!-- <a href="/orders/deleteorder/<?= "" //$order['id'] 
                                                                        ?>" onclick="javascript:return confirm('Sei sicuro di voler eliminare questo elemento?');" alt="elimina" title="Elimina" class="btn btn-danger btn-sm"><i class="fas fa-minus fa-fw"></i></a> -->
                                    <button class="btn btn-success btn-sm" type="submit" alt="salva" title="salva"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <tr class="collapse" id="product<?= $order['id'] ?>">
                                <td colspan="5">
                                    <table class="table" id="orderedProductList">
                                        <tbody>
                                            <?php foreach ($order['orderedProducts'] as $orderedProduct) : ?>
                                                <tr>
                                                    <td><?= $orderedProduct['quantity'] ?></td>
                                                    <td>
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <?= $orderedProduct['product_name'] ?>
                                                                </div>
                                                                <div class="col">
                                                                    € <?= $orderedProduct['product_price'] ?>
                                                                </div>
                                                            </div>
                                                            <?php foreach ($orderedProduct['addons'] as $addon) : ?>
                                                                <div class="row">
                                                                    <div class="col">
                                                                         <i>- <?= $addon['addon_name'] ?></i>
                                                                    </div>
                                                                    <div class="col">
                                                                        <i><?= $addon['addon_price'] ? "€ " . $addon['addon_price'] : "" ?></i>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </td>
                                                    <td>€ <?= $orderedProduct['price'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td><!-- <button class="btn btn-info btn-sm"><i class="far fa-edit fa-fw"></i></button> --></td>
                            </tr>
                        </form>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else : ?>
    <h2>In attesa del tuo primo ordine!!</h2>
<?php endif; ?>