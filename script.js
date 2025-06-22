// script.js

let currentUserType = 'mahasiswa';

// Tombol pemilihan user (login)
const btnMahasiswa = document.getElementById('btn-mahasiswa');
const btnDosen = document.getElementById('btn-dosen');
const btnAdmin = document.getElementById('btn-admin');

if (btnMahasiswa && btnDosen && btnAdmin) {
    const buttons = [btnMahasiswa, btnDosen, btnAdmin];

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('bg-blue-600', 'text-white', 'active'));
            buttons.forEach(b => b.classList.add('bg-gray-200', 'text-gray-700'));
            btn.classList.add('bg-blue-600', 'text-white');
            btn.classList.remove('bg-gray-200', 'text-gray-700');
            currentUserType = btn.textContent.toLowerCase();
        });
    });
}

// Tombol login
const loginBtn = document.getElementById('login-button');
if (loginBtn) {
    loginBtn.addEventListener('click', () => {
        const username = document.getElementById('username');
        const password = document.getElementById('password');

        if (!username.value || !password.value) {
            alert('Mohon isi username dan password.');
            return;
        }

        if (currentUserType === 'mahasiswa') {
            window.location.href = 'dashboard-mahasiswa.html';
        } else if (currentUserType === 'dosen') {
            window.location.href = 'dashboard-dosen.html';
        } else {
            window.location.href = 'dashboard-admin.html';
        }
    });
}

// Tombol pilihan role register
const regBtnMhs = document.getElementById('reg-btn-mahasiswa');
const regBtnDsn = document.getElementById('reg-btn-dosen');
const mhsForm = document.getElementById('mahasiswa-form');
const dsnForm = document.getElementById('dosen-form');

if (regBtnMhs && regBtnDsn && mhsForm && dsnForm) {
    regBtnMhs.addEventListener('click', () => {
        regBtnMhs.classList.add('bg-blue-600', 'text-white');
        regBtnDsn.classList.remove('bg-blue-600', 'text-white');
        regBtnDsn.classList.add('bg-gray-200', 'text-gray-700');
        mhsForm.classList.remove('hidden');
        dsnForm.classList.add('hidden');
    });

    regBtnDsn.addEventListener('click', () => {
        regBtnDsn.classList.add('bg-blue-600', 'text-white');
        regBtnMhs.classList.remove('bg-blue-600', 'text-white');
        regBtnMhs.classList.add('bg-gray-200', 'text-gray-700');
        dsnForm.classList.remove('hidden');
        mhsForm.classList.add('hidden');
    });
}

// Validasi dan Tombol daftar
const registerBtn = document.getElementById('register-button');
if (registerBtn) {
    registerBtn.addEventListener('click', () => {
        if (!mhsForm.classList.contains('hidden')) {
            const requiredFields = ['reg-nim', 'reg-angkatan', 'reg-nama', 'reg-prodi', 'reg-email', 'reg-password', 'reg-confirm-password'];
            for (const id of requiredFields) {
                const field = document.getElementById(id);
                if (!field || !field.value.trim()) {
                    alert('Mohon lengkapi semua data Mahasiswa.');
                    return;
                }
            }
            const pass = document.getElementById('reg-password').value;
            const conf = document.getElementById('reg-confirm-password').value;
            if (pass !== conf) {
                alert('Password dan konfirmasi tidak cocok.');
                return;
            }
        } else {
            const requiredFields = ['reg-nidn', 'reg-nama-dosen', 'reg-fakultas', 'reg-bidang', 'reg-email-dosen', 'reg-password-dosen', 'reg-confirm-password-dosen'];
            for (const id of requiredFields) {
                const field = document.getElementById(id);
                if (!field || !field.value.trim()) {
                    alert('Mohon lengkapi semua data Dosen.');
                    return;
                }
            }
            const pass = document.getElementById('reg-password-dosen').value;
            const conf = document.getElementById('reg-confirm-password-dosen').value;
            if (pass !== conf) {
                alert('Password dan konfirmasi tidak cocok.');
                return;
            }
        }
        window.location.href = 'success.html';
    });
}

// Tombol logout (pada semua dashboard)
const logoutMahasiswa = document.getElementById('logout-mahasiswa');
const logoutDosen = document.getElementById('logout-dosen');
const logoutAdmin = document.getElementById('logout-admin');

[logoutMahasiswa, logoutDosen, logoutAdmin].forEach(button => {
    if (button) {
        button.addEventListener('click', () => {
            window.location.href = 'login.html';
        });
    }
});
