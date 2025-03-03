<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RegisterTest extends TestCase
{
    use RefreshDatabase; // Untuk memastikan database di-reset setiap kali pengujian dijalankan

    /**
     * Test registrasi dengan data valid.
     *
     * @return void
     */
    public function test_register_with_valid_data()
    {
        // Simulasikan permintaan POST ke /register dengan data valid
        $response = $this->post('/register', [
            'nama_user' => 'John',
            'username' => 'johndoe',
            'email' => 'johndoe@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        // Verifikasi bahwa data pengguna baru berhasil disimpan ke dalam database
        $this->assertDatabaseHas('user', [
            'email' => 'johndoe@example.com',
        ]);

        // Verifikasi bahwa pengguna diarahkan ke halaman login dengan pesan sukses
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Registration successful! Please login.');
    }

    /**
     * Test registrasi dengan email yang sudah terdaftar.
     *
     * @return void
     */
    public function test_register_with_existing_email()
    {
        // Simulasikan pengguna pertama yang sudah ada di database
        User::create([
            'nama_user' => 'Sutan',
            'username' => 'Sutantiyar',
            'email' => 'sutan@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        // Simulasikan permintaan registrasi dengan email yang sudah ada
        $response = $this->post('/register', [
            'nama_user' => 'Sutan',
            'username' => 'Sutantiyar',
            'email' => 'sutan@gmail.com', // Email yang sudah ada
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        // Verifikasi bahwa validasi gagal dan pengguna tetap di halaman registrasi
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test registrasi dengan password yang tidak sesuai konfirmasi.
     *
     * @return void
     */
    public function test_register_with_non_matching_passwords()
    {
        // Simulasikan permintaan registrasi dengan password yang tidak cocok
        $response = $this->post('/register', [
            'nama_user' => 'John Doe',
            'username' => 'john_doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password124', // Password tidak cocok
        ]);

        // Verifikasi bahwa validasi password gagal
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test registrasi dengan username yang sudah terdaftar.
     *
     * @return void
     */
    public function test_register_with_existing_username()
    {
        // Simulasikan pengguna pertama yang sudah ada di database
        User::create([
            'nama_user' => 'Sutan',
            'username' => 'Sutantiyar',
            'email' => 'sutan@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        // Simulasikan permintaan registrasi dengan username yang sama
        $response = $this->post('/register', [
            'nama_user' => 'Sutan',
            'username' => 'Sutantiyar', // Username yang sudah ada
            'email' => 'sutan@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        // Verifikasi bahwa validasi gagal karena username sudah terdaftar
        $response->assertSessionHasErrors('username');
    }
}
