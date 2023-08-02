<?php if (count($categories) > 0) : ?>
    <h2 style="display: inline">Elenco categorie </h2>
    <input class="form-control" style="display: inline; float: right;" type="text" id="search" onkeyup="search('categories_table', this)" placeholder="Inizia a digitare..." title="Inizia a digitare per ininziare la ricerca">
    <div class="row">
        <div class="col-md-12">
            <table class="table" id="category_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Immagine</th>
                        <th>Sottocategorie</th>
                        <th>
                            <a href="#exampleModal" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-info btn-sm" alt="aggiungi" title="aggiungi">
                                Nuova categoria
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <form action="/categories" id="categories_form" name="categories_form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                            <tr id="<?= $category['id'] ?>">
                                <input hidden value="<?= $category['id'] ?>" id="id" name="id">
                                <td><?= $category['id'] ?></td>
                                <td><input class="form-control" id="name" name="name" type="text" value="<?= $category['name'] ?>"></td>
                                <td><img src="<?= $category['image'] ?>" />
                                    <input hidden value="<?= $category['image'] ?>" id="oldimage" name="oldimage">
                                    <div class="mb-3">
                                        <input class="form-control form-control-sm" type="file" id="image" name="image" accept="image/*">
                                    </div>
                                </td>
                                <td>
                                    <select id="subcategories[]" name="subcategories[]" multiple>
                                        <option value="0" <?= !$category['has_subcategories'] ? "selected" : "" ?>>Scegli un'opzione</option>
                                        <?php foreach ($categories as $sub_cat) : ?>
                                            <?php if (!$sub_cat['has_subcategories'] && $sub_cat['id'] != $category['id']) : ?>
                                                <option value="<?= $sub_cat['id'] ?>" <?= $sub_cat['is_subcategory'] == $category['id'] ? "selected" : "" ?>><?= $sub_cat['name'] ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <a href="/deletecategory/<?= $category['id'] ?>" onclick="javascript:return confirm('Sei sicuro di voler eliminare questo elemento?');" alt="elimina" title="Elimina" class="btn btn-danger btn-sm"><i class="fas fa-minus fa-fw"></i></a>
                                    <button class="btn btn-success btn-sm" type="submit" value="post" name="aggiorna" id="aggiorna" alt="salva" title="salva"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <!-- Collapse per la descrizione della categoria, attualmente rimane tutto a null...uguale nella modal -->
                            <tr class="collapse" id="category<?= $category['id'] ?>">
                                <td colspan="5">
                                    <div class="form-group">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" type="text" id="description" rows="3" name="description" value="<?= $category['description'] ?>"><?= $category['description'] ?></textarea>
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
<?php endif; ?>



<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Inserisci nuova categoria</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/categories" id="categories_form" name="categories_form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <table class="table" style="border-bottom: white;">
                    <tr>
                        <td>#</td>
                        <td>
                            <input class="form-control" id="name" name="name" type="text" value="">
                        </td>
                        <td>
                            <div class="mb-3">
                                <input class="form-control form-control-sm" type="file" id="image" name="image" accept="image/*">
                            </div>
                        </td>
                        <td>
                            <select id="subcategories[]" name="subcategories[]" multiple>
                                <option value="0" <?= !$category['has_subcategories'] ? "selected" : "" ?>>Scegli un'opzione</option>
                                <?php foreach ($categories as $sub_cat) : ?>
                                    <?php if (!$sub_cat['has_subcategories']) : ?>
                                        <option value="<?= $sub_cat['id'] ?>" <?= $sub_cat['is_subcategory'] == $category['id'] ? "selected" : "" ?>><?= $sub_cat['name'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <!-- <tr class="collapse" id="category<?= $category['id'] ?>">
                        <td colspan="5">
                            <div class="form-group">
                                <label for="description">Descrizione</label>
                                <textarea class="form-control" type="text" id="description" rows="3" name="description" value="<?= $category['description'] ?>"><?= $category['description'] ?></textarea>
                            </div>
                        </td>
                    </tr> -->
                </table>
                <div class="modal-footer" style="justify-content: center">
                    <button id="nuovo" name="nuovo" class="btn btn-success btn-sm" type="submit" alt="salva" title="salva" value="post">Aggiungi categoria</button>
                </div>
        </div>
        </form>
    </div>
</div>
</div>