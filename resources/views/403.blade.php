<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>403 - Akses Ditolak</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container">

    <div
        class="row justify-content-center align-items-center"
        style="min-height: 100vh;"
    >

        <div class="col-md-6">

            <div class="card shadow border-0">

                <div class="card-body text-center p-5">

                    <h1 class="display-1 fw-bold text-danger">
                        403
                    </h1>

                    <h2 class="fw-bold mb-3">
                        AKSES DITOLAK
                    </h2>

                    <div class="alert alert-danger">

                        <h5>
                            SERVER INI DITOLAK
                        </h5>

                        <p class="mb-0">
                            Anda tidak memiliki izin
                            untuk mengakses halaman Admin.
                        </p>

                    </div>

                    @auth

                        <p class="text-muted">

                            Role Anda:

                            <strong>
                                {{ Auth::user()->role }}
                            </strong>

                        </p>

                    @endauth

                    <a
                        href="{{ route('login') }}"
                        class="btn btn-primary">

                        Kembali ke Login

                    </a>

                    @auth

                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                            class="d-inline">

                            @csrf

                            <button
                                type="submit"
                                class="btn btn-secondary">

                                Logout

                            </button>

                        </form>

                    @endauth

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>