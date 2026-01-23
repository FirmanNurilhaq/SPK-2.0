<!DOCTYPE html>
<html>
<head><title>Login Sistem</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-indigo-600">Login Sistem</h2>
        <form action="<?= base_url('auth/process') ?>" method="post">
            <div class="mb-4">
                <label>Username</label>
                <input type="text" name="username" class="w-full border p-2 rounded" placeholder="sales / gudang" required>
            </div>
            <div class="mb-6">
                <label>Password</label>
                <input type="password" name="password" class="w-full border p-2 rounded" placeholder="123" required>
            </div>
            <button class="w-full bg-indigo-600 text-white p-2 rounded font-bold">Masuk</button>
        </form>
    </div>
</body>
</html>