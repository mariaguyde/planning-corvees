<h3>Créer son compte</h3>
<section id="main-section">
<form action="index.php?ctrl=user&action=doCreate" method="POST">

    <label for="email">Adresse mail</label>
    <input class="champs" type="email" name="email"placeholder="Votre adresse mail"/><br>

    <label for="password">Mot de passe</label>
    <input class="champs" type="password" name="password" placeholder="Votre mot de passe"/><br>

    <label for="lastName">Nom de famille</label>
    <input class="champs" type="text" name="lastName" placeholder="Votre nom"/><br>

    <label for="firstName">Prénom</label>
    <input class="champs" type="text" name="firstName" placeholder="Votre prénom"/><br>

    <label for="age">Âge</label>
    <input class="champs" type="number" name="age" placeholder="Votre age"/><br>

    <!--Boutons radios pour le genre de la personne -->
    <label for="genre">Genre</label>
    <div>
        <input type="radio" id="homme" name="genre" value="homme"
                checked>
        <label for="homme">Homme</label>
    </div>

    <div>
        <input type="radio" id="femme" name="genre" value="femme">
        <label for="femme">Femme</label>
    </div>

    <!---- Valider ----->
    <input class="submit-btn" type="submit" name="submit" value="Créer mon compte"/>
</form>
</section>
