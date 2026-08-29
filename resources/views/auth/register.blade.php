<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pharmacy Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
            <p class="text-sm text-gray-500">Register new Admin or Cashier</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/register" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" class="w-full mt-1 p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" class="w-full mt-1 p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="w-full mt-1 p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" class="w-full mt-1 p-2.5 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="cashier">Cashier</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-bold hover:bg-green-700 transition">Register</button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account? <a href="/login" class="text-blue-600 font-semibold hover:underline">Sign In</a>
        </p>
    </div>
</body>
</html>