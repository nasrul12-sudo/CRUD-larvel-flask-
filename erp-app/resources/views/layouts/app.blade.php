<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ERP App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white p-4 mb-6">
        <div class="container mx-auto flex gap-6">
            <a href="{{ route('workallocation.index') }}" class="font-bold">SPKO</a>
            <a href="{{ route('report.daily') }}">Report Harian</a>
        </div>
    </nav>
    <div class="container mx-auto px-4">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
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