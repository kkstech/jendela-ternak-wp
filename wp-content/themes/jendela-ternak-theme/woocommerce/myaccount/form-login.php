<?php
/**
 * Simple Custom Login/Register Form for Jendela Ternak Malang
 *
 * @package JendelaTernakMalang
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

// Deteksi tab mana yang harus aktif berdasarkan POST data (jika ada error registrasi)
$active_tab = ( isset( $_POST['register'] ) ) ? 'register' : 'login';

do_action( 'woocommerce_before_customer_login_form' ); 
?>

<style>
    /* Sembunyikan eye icon bawaan WooCommerce agar tidak menumpuk dengan custom toggle kita */
    .woocommerce-form-login .show-password-input,
    .woocommerce-form-register .show-password-input,
    span.show-password-input {
        display: none !important;
    }
    
    /* Memastikan semua icon di dalam relative container input (user, envelope, gembok, mata) berwarna abu-abu */
    .relative span.absolute,
    .relative span.absolute i,
    .relative button,
    .relative button i {
        color: #9CA3AF !important;
    }

    /* Berikan z-index agar icon selalu tampil di atas input */
    .relative span.absolute,
    .relative button {
        z-index: 10 !important;
    }
</style>

<!-- Outer Screen Container -->
<div class="min-h-[70vh] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-8 sm:p-10 flex flex-col justify-center"
         x-data="{ activeTab: '<?php echo esc_attr( $active_tab ); ?>', showLoginPassword: false, showRegPassword: false }">
        
        <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
            <!-- Dynamic Form Headers based on active tab -->
            <div class="mb-6 text-center">
                <h3 x-show="activeTab === 'login'" class="text-xl font-extrabold text-gray-900">Masuk ke Akun</h3>
                <h3 x-show="activeTab === 'register'" class="text-xl font-extrabold text-gray-900" style="display: none;">Daftar Akun Baru</h3>
            </div>
        <?php else : ?>
            <h3 class="text-xl font-extrabold text-gray-900 mb-6 text-center">Masuk ke Akun Anda</h3>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <div x-show="activeTab === 'login'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <form class="woocommerce-form woocommerce-form-login login space-y-5" method="post" novalidate>
                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Username atau Alamat Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" 
                               class="woocommerce-Input woocommerce-Input--text input-text block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/20 focus:border-[#0B5E34] transition-colors" 
                               name="username" 
                               id="username" 
                               autocomplete="username" 
                               placeholder="Masukkan username atau email..."
                               value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" 
                               required />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Kata Sandi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input class="woocommerce-Input woocommerce-Input--text input-text block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/20 focus:border-[#0B5E34] transition-colors" 
                               :type="showLoginPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               autocomplete="current-password" 
                               placeholder="Masukkan kata sandi..."
                               required />
                        <button type="button" 
                                @click="showLoginPassword = !showLoginPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i :class="showLoginPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <label class="flex items-center cursor-pointer select-none">
                        <input class="w-4 h-4 text-[#0B5E34] focus:ring-[#0B5E34] border-gray-300 rounded accent-[#0B5E34]" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                        <span class="ml-2 text-gray-600">Ingat Saya</span>
                    </label>
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="font-semibold text-[#0B5E34] hover:text-[#4CAF50] transition-colors">Lupa Password?</a>
                </div>

                <div class="pt-2">
                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-[#0B5E34] hover:bg-[#094d2b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B5E34] transition-all transform active:scale-[0.98]" name="login" value="Log in">
                        Masuk Ke Akun
                    </button>
                </div>

                <div class="text-center pt-4 border-t border-gray-100 text-sm">
                    <span class="text-gray-500">Belum punya akun?</span>
                    <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
                        <a href="#" @click.prevent="activeTab = 'register'" class="font-bold text-[#0B5E34] hover:text-[#4CAF50] ml-1 transition-colors">Daftar sekarang</a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="font-bold text-[#0B5E34] hover:text-[#4CAF50] ml-1 transition-colors">Daftar sekarang</a>
                    <?php endif; ?>
                </div>

                <?php do_action( 'woocommerce_login_form_end' ); ?>
            </form>
        </div>

        <!-- REGISTRATION FORM -->
        <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
            <div x-show="activeTab === 'register'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                <form method="post" class="woocommerce-form woocommerce-form-register register space-y-5" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
                    <?php do_action( 'woocommerce_register_form_start' ); ?>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                        <div>
                            <label for="reg_username" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Username <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <i class="fa-solid fa-user text-sm"></i>
                                </span>
                                <input type="text" 
                                       class="woocommerce-Input woocommerce-Input--text input-text block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/20 focus:border-[#0B5E34] transition-colors" 
                                       name="username" 
                                       id="reg_username" 
                                       autocomplete="username" 
                                       placeholder="Pilih nama pengguna unik..."
                                       value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" 
                                       required />
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="reg_email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" 
                                   class="woocommerce-Input woocommerce-Input--text input-text block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/20 focus:border-[#0B5E34] transition-colors" 
                                   name="email" 
                                   id="reg_email" 
                                   autocomplete="email" 
                                   placeholder="contoh@email.com"
                                   value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" 
                                   required />
                            </div>
                    </div>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                        <div>
                            <label for="reg_password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </span>
                                <input type="password" 
                                       class="woocommerce-Input woocommerce-Input--text input-text block w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/20 focus:border-[#0B5E34] transition-colors" 
                                       name="password" 
                                       id="reg_password" 
                                       autocomplete="new-password" 
                                       placeholder="Pilih kata sandi yang aman..."
                                       required />
                                <button type="button" 
                                        @click="showRegPassword = !showRegPassword" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i :class="showRegPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                                </button>
                            </div>
                        </div>
                    <?php else : ?>
                        <p class="text-xs text-gray-500 italic bg-gray-50 p-3 rounded-lg border border-gray-100 flex items-start gap-2">
                            <i class="fa-solid fa-info-circle text-[#0B5E34] mt-0.5"></i>
                            Tautan untuk mengatur kata sandi baru akan dikirimkan ke alamat email Anda.
                        </p>
                    <?php endif; ?>

                    <?php do_action( 'woocommerce_register_form' ); ?>

                    <div class="pt-2">
                        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-[#0B5E34] hover:bg-[#094d2b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B5E34] transition-all transform active:scale-[0.98]" name="register" value="Register">
                            Daftar Sekarang
                        </button>
                    </div>

                    <div class="text-center pt-4 border-t border-gray-100 text-sm">
                        <span class="text-gray-500">Sudah punya akun?</span>
                        <a href="#" @click.prevent="activeTab = 'login'" class="font-bold text-[#0B5E34] hover:text-[#4CAF50] ml-1 transition-colors">Masuk di sini</a>
                    </div>

                    <?php do_action( 'woocommerce_register_form_end' ); ?>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
