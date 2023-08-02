<script>
    function isChecked(id, el) {
        if (el.checked) {
            document.getElementById(id).style.display = 'block';
        } else {
            document.getElementById(id).style.display = 'none';
        }
    }
</script>

<?php if (count($products) > 0) : ?>
    <h2 style="display: inline">Elenco magazzino </h2>
    <input class="form-control" style="display: inline; float: right;" type="text" id="search" onkeyup="search('products_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">
    <div class="row">
        <div class="col-md-12">
            <table class="table" id="products_table">
                <thead>
                    <tr>
                        <th>
                            <a style="position: relative;float: right;" href="#exampleModal" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-info btn-sm" alt="aggiungi" title="aggiungi">
                                Nuovo prodotto
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) : ?>
                        <form action="/magazzino" id="magazzino_form" name="magazzino_form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <tr id="<?= $product['id'] ?>">
                                <td>
                                    <input hidden value="<?= $product['id'] ?>" id="id" name="id">
                                    <div class="row" style="padding: 25px 0">
                                        <div class="col-1">
                                            <strong>#</strong>
                                            <?= $product['id'] ?>
                                        </div>

                                        <div class="col-2">
                                            <div>
                                                <strong>Immagine</strong>
                                            </div>
                                            <img src="<?= $product['image'] ?>" />
                                            <input hidden value="<?= $product['image'] ?>" id="oldimage" name="oldimage">
                                            <div>
                                                <input class="form-control form-control-sm" type="file" id="image" name="image" accept="image/*">
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-7">
                                                    <div>
                                                        <strong>Nome</strong>
                                                        <input class="form-control" id="name" name="name" type="text" value="<?= $product['name'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div>
                                                        <input class="form-check-input" type="checkbox" value="1" id="is_quantified" name="is_quantified" <?= $product['is_quantified'] ? "checked" : "" ?> onclick="isChecked('<?= 'quantity' . $product['id'] ?>', this)">
                                                        <strong>Quantificabile?</strong>
                                                    </div>
                                                    <span class="input-number">
                                                        <input class="form-control" id="<?= 'quantity' . $product['id'] ?>" type="number" data-bs-input name="quantity" value="<?= $product['quantity'] ?>" min="0" <?= $product['is_quantified'] ? "" : "disabled" ?> />
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 20px;">
                                                <div class="col" style="display: flex; justify-content: center;align-items: center;">
                                                    <div style="margin-right: 20px;">
                                                        <strong>Addons</strong>
                                                    </div>
                                                    <select id="addons[]" name="addons[]" multiple>
                                                        <option value="0" <?= !$product['has_addons'] ? "selected" : "" ?>>Scegli un'opzione</option>
                                                        <?php foreach ($addons as $addon) : ?>
                                                            <option value="<?= $addon['id'] ?>" <?= in_array($addon['id'], array_column($product['addons'], 'id')) ? "selected" : "" ?>><?= $addon['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="row" style="margin: 3px 0">
                                                <div class="col">
                                                    <div>
                                                        <strong>Categoria</strong>
                                                    </div>
                                                    <select required id="category_id" class="form-control" name="category_id">
                                                        <option value="0" <?= !$product['category_id'] ? "selected" : "" ?>>Scegli un'opzione</option>
                                                        <?php foreach ($categories as $category) : ?>
                                                            <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? "selected" : "" ?> <?= $category['has_subcategories'] ? "disabled" : "" ?>><?= $category['name'] ?></option>
                                                            <?php foreach ($category['subcategories'] as $subcat) : ?>
                                                                <option value="<?= $subcat['id'] ?>" <?= $subcat['id'] == $product['category_id'] ? "selected" : "" ?>> - <?= $subcat['name'] ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" style="margin: 3px 0">
                                                <div class="col">
                                                    <div>
                                                        <strong>Prezzo</strong>
                                                    </div>
                                                    <input class="form-control" type="num" value="<?= $product['price'] ?>" id="price" name="price" />
                                                </div>
                                            </div>
                                            <div class="row" style="margin: 3px 0">
                                                <div class="col">
                                                    <div>
                                                        <strong>Magazzino</strong>
                                                    </div>
                                                    <select id="magazzino_id" class="form-control" name="magazzino_id">
                                                        <option value="0" <?= !$product['magazzino_id'] ? "selected" : "" ?>>Scegli un'opzione</option>
                                                        <?php foreach ($magazzini as $magazzino) : ?>
                                                            <option value="<?= $magazzino['id'] ?>" <?= $magazzino['id'] == $product['magazzino_id'] ? "selected" : "" ?>><?= $magazzino['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1" style="display: flex;flex-direction: column;justify-content: space-evenly;align-items: center;">
                                            <div>
                                                <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#product<?= $product['id'] ?>" aria-expanded="false" aria-controls="<?= $product['id'] ?>" title="espandi">
                                                    <i class="far fa-eye fa-fw"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <a href="/deleteproduct/<?= $product['id'] ?>" onclick="javascript:return confirm('Sei sicuro di voler eliminare questo elemento?');" alt="elimina" title="Elimina" class="btn btn-danger btn-sm"><i class="fas fa-minus fa-fw"></i></a>
                                            </div>
                                            <div>
                                                <button class="btn btn-success btn-sm" type="submit" value="post" name="aggiorna" id="aggiorna" alt="salva" title="salva"><i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                            <tr class="collapse" id="product<?= $product['id'] ?>">
                                <td>
                                    <div class="row">
                                        <!-- <div class="col-auto">
                                                <label for="price">
                                                    Prezzo €
                                                </label>
                                            </div>
                                            <div class="col-auto">
                                                <input class="form-control" type="num" value="<?= $product['price'] ?>" id="price" name="price" />
                                            </div> -->
                                        <div class="col-1">

                                        </div>
                                        <div class="col">
                                            <div>
                                                <input class="form-check-input" type="checkbox" value="1" id="is_vegan" name="is_vegan" <?= $product['is_vegan'] ? "checked" : "" ?>>
                                                <label for="is_vegan">è vegano?</label>
                                            </div>
                                            <div>
                                                <input class="form-check-input" type="checkbox" value="1" id="is_freezed" name="is_freezed" <?= $product['is_freezed'] ? "checked" : "" ?>>
                                                <label for="is_freezed">è congelato?</label>
                                            </div>
                                            <div>
                                                <input class="form-check-input" type="checkbox" value="1" id="is_vegetarian" name="is_vegetarian" <?= $product['is_vegetarian'] ? "checked" : "" ?>>
                                                <label for="is_vegetarian">è vegetariano?</label>
                                            </div>
                                            <div>
                                                <input class="form-check-input" type="checkbox" value="1" id="is_addon" name="is_addon" <?= $product['is_addon'] ? "checked" : "" ?>>
                                                <label for="is_addon">è un addon?</label>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <label for="description"><strong>Descrizione</strong></label>
                                            <textarea class="form-control" type="text" id="description" rows="3" name="description" value="<?= $product['description'] ?>"><?= $product['description'] ?></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </form>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else : ?>
    <h2>Inserisci almeno un prodotto!!</h2>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <a style="text-align:center" href="#exampleModal" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-info btn-sm" alt="aggiungi" title="aggiungi">
            Nuovo prodotto
        </a>
    </div>
<?php endif; ?>



<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Inserisci nuovo prodotto</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/magazzino" id="magazzino_form" name="magazzino_form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="row">
                                <div class="col">
                                    <div>
                                        <strong>Nome</strong>
                                        <input class="form-control" id="name" name="name" type="text" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px">
                                <div class="col">
                                    <div>
                                        <strong>Immagine</strong>
                                    </div>
                                    <div>
                                        <input class="form-control form-control-sm" type="file" id="image" name="image" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="row">
                                <div class="col">
                                    <div>
                                        <input class="form-check-input" type="checkbox" value="1" id="is_quantified" name="is_quantified" onclick="isChecked('<?= 'quantity' ?>', this)">
                                        <strong>Quantificabile?</strong>
                                    </div>
                                    <span class="input-number">
                                        <input class="form-control" id="quantity" type="number" data-bs-input name="quantity" value="" min="0" />
                                    </span>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 10px">
                                <div class="col">
                                    <div style="margin-right: 20px;">
                                        <strong>Addons</strong>
                                    </div>
                                    <select id="addons[]" name="addons[]" multiple>
                                        <option value="0" selected>Scegli un'opzione</option>
                                        <?php foreach ($addons as $addon) : ?>
                                            <option value="<?= $addon['id'] ?>"><?= $addon['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="row" style="margin: 3px 0">
                                <div class="col">
                                    <div>
                                        <strong>Categoria</strong>
                                    </div>
                                    <select required id="category_id" class="form-control" name="category_id">
                                        <option value="0" selected>Scegli un'opzione</option>
                                        <?php foreach ($categories as $category) : ?>
                                            <option value="<?= $category['id'] ?>" <?= $category['has_subcategories'] ? "disabled" : "" ?>><?= $category['name'] ?></option>
                                            <?php foreach ($category['subcategories'] as $subcat) : ?>
                                                <option value="<?= $subcat['id'] ?>"> - <?= $subcat['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin: 3px 0">
                                <div class="col">
                                    <div>
                                        <strong>Prezzo</strong>
                                    </div>
                                    <input class="form-control" type="num" value="" id="price" name="price" />
                                </div>
                            </div>
                            <div class="row" style="margin: 3px 0">
                                <div class="col">
                                    <div>
                                        <strong>Magazzino</strong>
                                    </div>
                                    <select id="magazzino_id" class="form-control" name="magazzino_id">
                                        <option value="0" selected>Scegli un'opzione</option>
                                        <?php foreach ($magazzini as $magazzino) : ?>
                                            <option value="<?= $magazzino['id'] ?>"><?= $magazzino['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- <div class="col-auto">
                                                <label for="price">
                                                    Prezzo €
                                                </label>
                                            </div>
                                            <div class="col-auto">
                                                <input class="form-control" type="num" value="serve il prezzo" id="price" name="price" />
                                            </div> -->
                        <div class="col-1">

                        </div>
                        <div class="col">
                            <div>
                                <input class="form-check-input" type="checkbox" value="1" id="is_vegan" name="is_vegan">
                                <label for="is_vegan">è vegano?</label>
                            </div>
                            <div>
                                <input class="form-check-input" type="checkbox" value="1" id="is_freezed" name="is_freezed">
                                <label for="is_freezed">è congelato?</label>
                            </div>
                            <div>
                                <input class="form-check-input" type="checkbox" value="1" id="is_vegetarian" name="is_vegetarian">
                                <label for="is_vegetarian">è vegetariano?</label>
                            </div>
                            <div>
                                <input class="form-check-input" type="checkbox" value="1" id="is_addon" name="is_addon">
                                <label for="is_addon">è un addon?</label>
                            </div>
                        </div>
                        <div class="col-8">
                            <label for="description"><strong>Descrizione</strong></label>
                            <textarea class="form-control" type="text" id="description" rows="3" name="description" value=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="justify-content: center">
                        <button id="nuovo" name="nuovo" class="btn btn-success btn-sm" type="submit" alt="salva" title="salva" value="post">Aggiungi prodotto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>