<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white p-4 mb-6">
        <div class="container mx-auto flex gap-6 items-center">
            <a href="{{ route('workallocation.index') }}" class="font-bold {{ request()->routeIs('workallocation.*') ? 'text-yellow-300' : '' }}">SPKO</a>
            <a href="{{ route('workcompletion.index') }}" class="{{ request()->routeIs('workcompletion.*') ? 'text-yellow-300' : '' }}">NTHKO</a>
            <a href="{{ route('report.daily') }}" class="{{ request()->routeIs('report.*') ? 'text-yellow-300' : '' }}">Report Harian</a>
            <a href="http://127.0.0.1:5000/weight-diff" target="_blank" class="ml-auto bg-green-500 hover:bg-green-600 px-4 py-2 rounded font-semibold text-sm">
                📊 Selisih Berat (Flask) ↗
            </a>
        </div>
    </nav>
    <div class="container mx-auto px-4 pb-10">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>