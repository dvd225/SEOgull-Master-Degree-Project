<main id="main" class="main">

    <div class="pagetitle">
    <h1 class="card-title">Profilo <span> / <?= $_SESSION['username'] ?></span></h1>
       
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="<?php echo $this->config->item('risorse')['img'] ?>/profile.png" alt="Profile" class="rounded-circle">
                        <h2> <?= $user_info->fullname ?></h2>
                        <h3><?= $user_info->azienda ?></h3>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
              
                        <ul class="nav nav-tabs nav-tabs-bordered">



                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">Modifica Profilo</button>
                            </li>



                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Cambia Password</button>
                            </li>

                        </ul>
                        <div class="tab-content pt-2">



                            <div class="tab-pane fade show active profile-edit pt-3" id="profile-edit">


                                <form id ="form_profile">

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="full_name" value = "<?= $user_info->fullname ?>" >
                                        <label for="full_name">Nome Utente</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="azienda"  value = "<?= $user_info->azienda ?>">
                                        <label for="azienda">Azienda</label>
                                    </div>

                                    <div class="text-center" id ="button_result_profile">
                                        <button type="button" class="btn btn-primary" id ="update_profile">Salva modifiche</button>
                                    </div>
                                </form>
                            </div>


                            <div class="tab-pane fade pt-3" id="profile-change-password">
                           
                                <form id ="form_password">

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="old_pw" placeholder="name@example.com">
                                        <label for="old_pw">Vecchia Password</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="new_pw" placeholder="name@example.com">
                                        <label for="new_pw">Nuova Password</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="redo_new_pw" placeholder="name@example.com">
                                        <label for="redo_new_pw">Ripeti nuova Password</label>
                                    </div>

                                    <div class="text-center" id ="button_result_pw">
                                        <button type="button" class="btn btn-primary" id ="update_password">Cambia Password</button>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>