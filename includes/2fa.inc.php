<?php
$token = $_GET['token'];
if (isset($_SESSION['error'])) { ?>
    <div role="alert" class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Fout! <?= $_SESSION['error'] ?></span>
    </div>
    <?php unset($_SESSION['error']);
}

if (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == 'activateAccount') { ?>

    <div class="container mt-3">
        <h2>Activeer account</h2>
        <form action="php/checkMailCode.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3 mt-3">
                <label>Voer code in:</label>
                <input type="text" class="form-control" placeholder="Code" name="code" required>
            </div>

            <input type="hidden" value="<?= $token ?>" name="token">
            <button type="submit" class="btn btn-outline-dark btn-lg px-5">Activeer</button>

        </form>
    </div>
<?php } elseif (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == '2fa') { ?>

    <div class="container mt-3">
        <h2>Verifeer account</h2>
        <form action="php/checkMailCode.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3 mt-3">
                <label>Voer code in:</label>
                <input type="text" class="form-control" placeholder="Code" name="code" required>
            </div>

            <input type="hidden" value="<?= $token ?>" name="token">
            <button type="submit" class="btn btn-outline-dark btn-lg px-5">Verifeer</button>

        </form>
    </div>

<?php } ?>
