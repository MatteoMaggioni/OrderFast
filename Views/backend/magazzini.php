<h2 style="display: inline">Elenco Magazzini </h2>
<input style="display: inline; float: right" type="text" id="search" onkeyup="search('orders_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">
<div class="row">
    <div class="col-md-12">
        <table class="table" id="orders_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Magazzino</th>
                    <th style="text-align: center;">Visibilità</th>
                    <th>
                        <a href="#exampleModal" data-bs-toggle="collapse" data-bs-target="#headingOne" aria-expanded="false" aria-controls="headingOne" class="btn btn-info btn-sm" alt="aggiungi" title="aggiungi">
                            Nuovo magazzino
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr id="headingOne" class="collapse" aria-labelledby="headingOne" data-bs-parent="#accordion">
                    <form id="magazzino" name="magazzino" action='/magazzini' method="post">
                        <input hidden value="<?= (int)session()->get('restaurant_id') ?>" id="restaurant_id" name="restaurant_id">
                        <td>
                            #
                        </td>
                        <td>
                            <input type="text" class="form-control" name="name" id="name" value="">
                        </td>
                        <td>
                            <div style="justify-content: center;display: flex;">
                                <input hidden class="form-check-input" type="checkbox" value="0" id="visibility" name="visibility" checked>
                                <input class="form-check-input" type="checkbox" value="1" id="visibility" name="visibility">
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm" type="submit" alt="salva" title="salva"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
                        </td>
                    </form>
                </tr>
                <?php foreach ($magazzini as $magazzino) : ?>
                    <form id="magazzino" name="magazzino" action='/magazzini' method="post">
                        <input hidden value="<?= $magazzino['id'] ?>" id="id" name="id">
                        <input hidden value="<?= (int)session()->get('restaurant_id') ?>" id="restaurant_id" name="restaurant_id">
                        <tr>
                            <td>
                                <?= $magazzino['id'] ?>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="name" id="name" value="<?= $magazzino['name'] ?>">
                            </td>
                            <td>
                                <div style="justify-content: center;display: flex;">
                                    <input hidden class="form-check-input" type="checkbox" value="0" id="visibility" name="visibility" checked>
                                    <input class="form-check-input" type="checkbox" value="1" id="visibility" name="visibility" <?= $magazzino['visibility'] ? "checked" : "" ?>>
                                </div>
                            </td>
                            <td>
                                <a href="/deletemagazzino/<?= $magazzino['id'] ?>" onclick="javascript:return confirm('Sei sicuro di voler eliminare questo elemento?');" alt="elimina" title="Elimina" class="btn btn-danger btn-sm"><i class="fas fa-minus fa-fw"></i></a>
                                <button class="btn btn-success btn-sm" type="submit" alt="salva" title="salva"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    </form>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>