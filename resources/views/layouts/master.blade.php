<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="font-poppins text-[#070625]">
    @yield('content')

    @stack('before-scripts')

    <!-- Script untuk menangani peringatan sesi akan habis -->
    <script>
        let sessionTimer;
        let countdownTimer;
        let isSessionActive = true; // Flag untuk menandakan apakah sesi aktif

        // Fungsi untuk menampilkan SweetAlert countdown
        function sessionExpiredDialog() {
            Swal.fire({
                title: 'Session Expired',
                text: 'Your session expired, please click to continue.',
                showCancelButton: true,
                confirmButtonText: 'Continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    isSessionActive = true; // Menandakan sesi masih aktif setelah diklik
                    updateSessionActivity(); // Update waktu aktivitas
                } else {
                    logout(); // Jika cancel, logout
                }
            });
        }

        // Mengirimkan request untuk memperbarui waktu aktivitas terakhir
        function updateSessionActivity() {
            fetch("{{ route('update_last_activity') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_activity'
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('Session time updated');
                    }
                });
        }

        // Fungsi untuk logout dan menutup aplikasi jika sesi habis
        function logout() {
            // Membuat form logout secara dinamis dan mengirimkannya dengan metode POST
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('logout') }}'; // Mengarahkan ke route logout

            // Membuat input csrf_token secara otomatis
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}'; // Menambahkan CSRF token ke form

            form.appendChild(csrfToken); // Menambahkan CSRF token ke form

            // Menambahkan form ke body dan mengirimnya
            document.body.appendChild(form);
            form.submit(); // Men-submit form secara otomatis
        }

        // Fungsi untuk mulai timer peringatan setelah 15 menit
        function startSessionTimer() {
            sessionTimer = setTimeout(sessionExpiredDialog, 14 * 60 * 1000); // Timer 15 menit
        }

        // Fungsi untuk reset timer saat pengguna berinteraksi dengan halaman
        function resetSessionTimer() {
            clearTimeout(sessionTimer);
            clearInterval(countdownTimer); // Hentikan interval countdown sebelumnya
            startSessionTimer(); // Mulai timer baru
            updateSessionActivity(); // Perbarui aktivitas terakhir
            isSessionActive = true; // Menandakan sesi masih aktif
        }

        // Memulai timer peringatan saat halaman dimuat
        startSessionTimer();

        // Reset timer jika pengguna berinteraksi dengan halaman
        document.addEventListener('mousemove', resetSessionTimer);
        document.addEventListener('click', resetSessionTimer);
    </script>

    @stack('after-scripts')
</body>

</html>
