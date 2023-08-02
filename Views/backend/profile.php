<script>
    function validaCodiceFiscale(cf, id) {
        cf = cf.toUpperCase();
        if (cf == 'PLMMRA73P50F8P9L') return true;
        if (cf.length < 16) return false;
        var validi, i, s, set1, set2, setpari, setdisp;
        if (cf == '') return '';
        set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
        setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
        s = 0;
        for (i = 1; i <= 13; i += 2)
            s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
        for (i = 0; i <= 14; i += 2)
            s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
        if (s % 26 != cf.charCodeAt(15) - 'A'.charCodeAt(0)) {
            alert('Codice Fiscale ' + cf + ' invalido');
            id.value = "";
            return false;
        }
        return true;
    }

    function createAreaPsw() {
        var html = '<div class="form-group"><label for="password">Password:</label><input type="password" class="form-control" name="password" id="password" title="La Password deve contenere minimo 6 caratteri massimo 16, includendo maiuscole minuscole, numeri ed un carattere speciale tra [_.,\-+*!#@?]" pattern="(?=.*\d)(?=.*[_.,\-+*!#@?])(?=.*[a-z])(?=.*[A-Z]).{6,16}" value=""></div><div class="form-group"><label for="password_confirm">Conferma Password:</label><input type="password" class="form-control" name="password_confirm" id="password_confirm" value=""></div>'
        document.getElementById('modificaPsw').innerHTML = html
    }
</script>

<div class="container">
    <h2>Profilo <?= $user['firstname'] . ' ' . $user['lastname']   ?></h2>
    <hr>

    <form class="" action="profile" method="post">
        <input hidden type="text" name="profile_id" value="<?= $user['profile_id'] ?>">
        <input hidden type="text" name="account_id" value="<?= $user['account_id'] ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input readonly type="text" class="form-control" name="username" value="<?= set_value('username', $user['username']) ?>">
        </div>
        <div class="form-group">
            <label for="firstname">Nome:</label>
            <input type="text" class="form-control" name="firstname" id="firstname" value="<?= set_value('firstname', $user['firstname']) ?>">
        </div>
        <div class="form-group">
            <label for="lastname">Cognome:</label>
            <input type="text" class="form-control" name="lastname" id="lastname" value="<?= set_value('lastname', $user['lastname']) ?>">
        </div>
        <div class="form-group">
            <label for="fiscalcode">Codice fiscale:</label>
            <input type="text" class="form-control" name="fiscalcode" id="fiscalcode" pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9pP]{3}[a-zA-Z]$" onfocusout="validaCodiceFiscale(this.value,this)" value="<?= $user['fiscalcode'] ?>">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= $user['email'] ?>">
        </div>
        <div class="d-flex justify-content-between" style="align-items: center">
            <h3>Cambia password</h3>
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#modificaPsw" onclick="createAreaPsw()" aria-expanded="false" aria-controls="modificaPsw">
                Modifica password
            </button>

        </div>
        <div class="collapse" id="modificaPsw">

        </div>

        <?php if (isset($validation)) : ?>
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-sm-4">
                <button type="submit" class="btn btn-primary" value="post" name="profile">Salva</button>
            </div>
        </div>
    </form>

    <h2>Dati <?= $restaurant['name'] ?></h2>
    <hr>

    <form class="" action="profile" method="post">
        <input hidden type="text" name="id" value="<?= $restaurant['id'] ?>">
        <div class="form-group">
            <label for="name">Nome:</label>
            <input type="text" class="form-control" name="name" value="<?= $restaurant['name'] ?>">
        </div>
        <div class="form-group">
            <label for="description">Desrizione:</label>
            <input type="text" class="form-control" name="description" rows="3" value="<?= $restaurant['description'] ?>">
        </div>
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" class="form-control" name="location" value="<?= $restaurant['location'] ?>">
        </div>
        <div class="form-group">
            <label for="phone">Telefono:</label>
            <input type="number" class="form-control" name="phone" value="<?= $restaurant['phone'] ?>">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= $user['email'] ?>">
        </div>
        <div class="form-group">
            <label for="region">Regione:</label>
            <input type="text" class="form-control" name="region" value="<?= $restaurant['region'] ?>">
        </div>
        <div class="form-group">
            <label for="status">Stato abbonamento:</label>
            <input readonly type="text" class="form-control" name="status" value="<?= $restaurant['state'] ?>">
        </div>
        <div class="form-group">
            <label for="abonament">Costo abbonamento:</label>
            <input readonly type="number" class="form-control" name="abonament" value="<?= $restaurant['abonament_price'] ?>">
        </div>
        <div class="form-group">
            <label for="expiring_date">Giorno scadenza:</label>
            <input readonly type="date" class="form-control" name="expiring_date" value="<?= $restaurant['payments_expiration'] ?>">
        </div>
        <div class="form-group">
            <label for="stripe_key">Stripe key:</label>
            <input readonly type="text" class="form-control" name="stripe_key_" value="<?= $restaurant['stripe_key'] ?>">
        </div>
        <div class="form-group">
            <label for="stripe_secret">Stripe secret:</label>
            <input readonly type="text" class="form-control" name="stripe_secret_" value="<?= $restaurant['stripe_secret'] ?>">
        </div>

        <div class="row">
            <div class="col-12 col-sm-4">
                <button type="submit" class="btn btn-primary" value="post" name="restaurant">Salva</button>
            </div>
        </div>
    </form>


</div>