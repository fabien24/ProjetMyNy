<div id="loginScreen">  
    <form action="" method="post">
        <input type="email" name="email" placeholder="E-mail" ><br />
        <input type="password" name="password" placeholder="Password"><br />
        <input class="submit" type="submit" value="Log in">
        <a href="lostpassword.php">Lost password</a><br />
        <label>
            <?php
            if (!$emailFilled) {
            ?> Email empty <?php
            } elseif (!$pwFilled) {
            ?> Password empty <?php
            } elseif (!$emailVerif) {
            ?> Invalid mail adrresse <?php
            } elseif (!$pwVerif) {
            ?> Password incorrect <?php
            } ?>
        </label>
    </form>
</div>