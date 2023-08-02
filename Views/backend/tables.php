<script>
    function calculate(persons, cost_per_person, fixed_cost, id) {
        var value = persons * cost_per_person + fixed_cost;
        console.log(id);
        document.getElementById['min_price1'].innerHTML = '€ ';
    };
</script>

    <h2 style="display: inline">Elenco tavoli </h2>
    <input class="form-control row-g3" style="display: inline; float: right;" type="text" id="search" onkeyup="search('tables_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">
    <div class="row">
        <div class="col-md-12">
            <table class="table" id="tables_table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Numero di persone</th>
                        <th>Costo per persona</th>
                        <th>Costo fisso</th>
                        <th>Prezzo totale minimo</th>
                        <th>
                            <a href="#exampleModal" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-info btn-sm" alt="aggiungi" title="aggiungi">
                                Aggiungi tavolo</i>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table) : ?>
                        <tr id="<?= $table['id'] ?>">
                            <form id="tablesform" name="tablesform" action='/tables' method="post">
                                <input hidden value="<?= $table['id'] ?>" id="id" name="id">
                                <td><?= $table['id'] ?></td>
                                <td><input class="form-control" required id="name" name="name" type="text" value="<?= $table['name'] ?>"></td>
                                <td>
                                    <span class="input-number">
                                        <input required class="form-control" id="num_persons" type="number" data-bs-input name="num_persons" value="<?= $table['num_persons'] ? $table['num_persons'] : 0 ?>" min="0" max="30" onkeyup="calculate(<?= (int)$table['num_persons'] ?>, <?= (float)$table['cost_per_person'] ?>, <?= (float)$table['fixed_cost'] ?>, '<?= 'min_price' . $table['id'] ?>')" />
                                    </span>
                                </td>
                                <td>
                                    <span class="input-number">
                                        <input required class="form-control" id="cost_per_person" type="number" data-bs-input name="cost_per_person" value="<?= $table['cost_per_person'] ? $table['cost_per_person'] : 0  ?>" min="0" max="500" step="any" />
                                    </span>
                                </td>
                                <td>
                                    <span class="input-number">
                                        <input required class="form-control" id="fixed_cost" type="number" data-bs-input name="fixed_cost" value="<?= $table['fixed_cost'] ? $table['fixed_cost'] : 0 ?>" min="0" max="1000" step="any" />
                                    </span>
                                </td>
                                <td id="min_price">
                                    € <?= $table['min_price'] ?>
                                </td>
                                <td>
                                    <button id="tablesform" name="tablesform" class="btn btn-success btn-sm" type="submit" alt="salva" title="salva" value="post"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
                                    <a href="/deletetable/<?= $table['id'] ?>" onclick="javascript:return confirm('Sei sicuro di voler eliminare questo elemento? Cancellandolo il QR che stavi utilizzando non sarà più valido');" alt="elimina" title="Elimina" class="btn btn-danger btn-sm"><i class="fas fa-minus fa-fw"></i></a>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (!count($tables)) : ?>
                <h2>Aggiungi il tuo primo tavolo</h2>
            <?php endif; ?>
        </div>
    </div>



<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Inserisci nuovo tavolo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newtable" name="newtable" action='/tables' method="post">
                <div class="modal-body">
                    <div>
                        <div class="row g-3 align-items-center" style="justify-content: center">
                            <div class="col-auto">
                                <label for="name" class="col-form-label">Nome del tavolo</label>
                            </div>
                            <div class="col-auto">
                                <input class="form-control" type="text" id="name" name="name" class="form-control" aria-describedby="name" placeholder="Inserisci il nome" required>
                            </div>
                        </div>
                        <div><strong>Inserisci:</strong></div>
                        <div class="container">
                            <div class="row g-3 align-items-center">
                                <div class="col-4">
                                    <label for="num_persons" class="col-form-label">il numero di persone</label>
                                </div>
                                <div class="col-auto">
                                    <input class="form-control" id="num_persons" type="number" class="form-control" data-bs-input name="num_persons" value="0" min="0" max="30" />
                                </div>
                            </div>
                            <div class="row g-3 align-items-center">
                                <div class="col-4">
                                    <label for="cost_per_person" class="col-form-label">il costo per persona €</label>
                                </div>
                                <div class="col-auto">
                                    <input class="form-control" id="cost_per_person" type="number" class="form-control" data-bs-input name="cost_per_person" value="0" min="0" max="50" step="any" />
                                </div>
                            </div>
                            <div class="row g-3 align-items-center">
                                <div class="col-4">
                                    <label for="fixed_cost" class="col-form-label">il costo fisso del tavolo €</label>
                                </div>
                                <div class="col-auto">
                                    <input class="form-control" id="fixed_cost" type="number" class="form-control" data-bs-input name="fixed_cost" value="0" min="0" max="1000" step="any" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="justify-content: center">
                        <button id="newtable" name="newtable" class="btn btn-success btn-sm" type="submit" alt="salva" title="salva" value="post">Aggiungi tavolo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>