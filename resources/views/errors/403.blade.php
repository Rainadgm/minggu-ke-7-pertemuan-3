<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#191f2b">
    <title>403 | Akses Ditolak</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            background: #191f2b;
            color: #dce6f4;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        main {
            display: grid;
            min-height: 100vh;
            place-items: center;
            padding: 24px;
        }

        .message {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        strong {
            font-size: 18px;
            font-weight: 700;
        }

        .divider {
            width: 1px;
            height: 24px;
            background: #8993a2;
        }

        p {
            margin: 0;
            font-size: 16px;
            font-weight: 400;
        }

        @media (max-width: 420px) {
            .message {
                gap: 10px;
            }

            p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="message" role="alert">
            <strong>403</strong>
            <span class="divider" aria-hidden="true"></span>
            <p>ANDA TIDAK MEMILIKI AKSES.</p>
        </div>
    </main>
</body>
</html>