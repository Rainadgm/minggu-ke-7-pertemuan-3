<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background-color: #f5f6fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }

        .sidebar h4 {
            color: white;
            padding: 20px;
            text-align: center;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background-color: #343a40;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

    </style>

</head>


<body>


<div class="container-fluid">

    <div class="row">


        <!-- ========================= -->
        <!-- SIDEBAR ADMIN -->
        <!-- ========================= -->

        <div class="col-md-3 col-lg-2 p-0 sidebar">

            <h4>
                ADMIN
            </h4>

            <hr class="text-white">


            <!-- Dashboard -->

            <a href="{{ route('admin.dashboard') }}">

                🏠 Dashboard

            </a>


            <!-- Data Pengguna -->

            <a href="#">

                👤 Data Pengguna

            </a>


            <!-- Data Produk -->

            <a href="#">

                📦 Data Produk

            </a>


            <!-- Laporan -->

            <a href="#">

                📊 Laporan

            </a>


            <!-- Pengaturan -->

            <a href="#">

                ⚙️ Pengaturan

            </a>


            <hr class="text-white">


            <!-- Logout -->

            <div class="px-3">

                <form
                    action="{{ route('logout') }}"
                    method="POST">

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-danger w-100">

                        Logout

                    </button>

                </form>

            </div>


        </div>



        <!-- ========================= -->
        <!-- CONTENT -->
        <!-- ========================= -->

        <div class="col-md-9 col-lg-10 content">


            <!-- Header -->

            <div
                class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>
                        Dashboard Admin
                    </h2>

                    <p class="text-muted">

                        Selamat datang di halaman Admin.

                    </p>

                </div>


                <span class="badge bg-primary">

                    {{ Auth::user()->role }}

                </span>

            </div>



            <!-- ========================= -->
            <!-- INFORMASI USER -->
            <!-- ========================= -->

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="card-title">

                        Informasi Pengguna

                    </h5>

                    <hr>


                    <p>

                        <strong>
                            Nama:
                        </strong>

                        {{ Auth::user()->name }}

                    </p>


                    <p>

                        <strong>
                            Email:
                        </strong>

                        {{ Auth::user()->email }}

                    </p>


                    <p class="mb-0">

                        <strong>
                            Role:
                        </strong>

                        {{ Auth::user()->role }}

                    </p>


                </div>

            </div>



            <!-- ========================= -->
            <!-- STATISTIK -->
            <!-- ========================= -->

            <div class="row g-4">


                <!-- CARD USER -->

                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">

                                Total Pengguna

                            </h6>


                            <h2>

                                10

                            </h2>


                            <p class="text-muted mb-0">

                                Pengguna terdaftar

                            </p>

                        </div>

                    </div>

                </div>



                <!-- CARD PRODUK -->

                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">

                                Total Produk

                            </h6>


                            <h2>

                                20

                            </h2>


                            <p class="text-muted mb-0">

                                Produk tersedia

                            </p>

                        </div>

                    </div>

                </div>



                <!-- CARD LAPORAN -->

                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h6 class="text-muted">

                                Total Laporan

                            </h6>


                            <h2>

                                15

                            </h2>


                            <p class="text-muted mb-0">

                                Laporan transaksi

                            </p>

                        </div>

                    </div>

                </div>


            </div>



            <!-- ========================= -->
            <!-- PESAN SELAMAT DATANG -->
            <!-- ========================= -->

            <div class="alert alert-success mt-4">

                <h5>
                    Login Berhasil
                </h5>

                <p class="mb-0">

                    Selamat datang,
                    <strong>
                        {{ Auth::user()->name }}
                    </strong>.

                    Anda login sebagai
                    <strong>
                        Admin
                    </strong>.

                </p>

            </div>


        </div>


    </div>

</div>



<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>