import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../config/app_colors.dart';
import 'main_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _passwordVisible = false;

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
    });

    print('🚀 Login başlatılıyor...'); // Debug log
    
    final result = await AuthService.login(
      _usernameController.text.trim(),
      _passwordController.text,
    );

    print('📋 Login sonucu: $result'); // Debug log

    setState(() {
      _isLoading = false;
    });

    if (result['success']) {
      print('🎉 Login başarılı, ana sayfaya yönlendiriliyor...'); // Debug log
      // Başarılı giriş
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const MainScreen()),
      );
    } else {
      print('❌ Login başarısız: ${result['message']}'); // Debug log
      // Hata mesajı göster
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message']),
          backgroundColor: AppColors.dangerRed,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Android activity_login.xml: background="@color/black"
      backgroundColor: AppColors.black,
      appBar: AppBar(
        // Android: title = "Kasabi'Et Stok Takip"
        title: const Text('Kasabi\'Et Stok Takip'),
        backgroundColor: AppColors.black,
        foregroundColor: AppColors.white,
        centerTitle: true,
        elevation: 0,
      ),
      body: Container(
        width: double.infinity,
        height: double.infinity,
        // Android: android:padding="32dp"
        padding: const EdgeInsets.all(32),
        child: Column(
          // Android: android:gravity="center"
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Android: Genel Logo (Yatay) - 200dp x 80dp
            const SizedBox(
              width: 200,
              height: 80,
              child: Image(
                image: AssetImage('assets/images/logo_horizontal.png'),
                width: 200,
                height: 80,
                // Android: android:scaleType="fitCenter"
                fit: BoxFit.contain,
              ),
            ),
            
            // Android: android:layout_marginBottom="24dp"
            const SizedBox(height: 24),

            // Login Form
            Form(
              key: _formKey,
              child: Column(
                children: [
                  // Android: E-posta EditText
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _usernameController,
                      style: const TextStyle(
                        // Android: android:textColor="@color/white"
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: const InputDecoration(
                        // Android: android:hint="E-posta"
                        hintText: 'E-posta',
                        hintStyle: TextStyle(
                          // Android: android:textColorHint="@color/light_gray"
                          color: AppColors.lightGray,
                        ),
                        // Android: android:background="@drawable/edit_text_background"
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        // Android: android:padding="16dp"
                        contentPadding: EdgeInsets.all(16),
                      ),
                      // Android: android:inputType="textEmailAddress"
                      keyboardType: TextInputType.emailAddress,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'E-posta gerekli';
                        }
                        return null;
                      },
                    ),
                  ),
                  
                  // Android: android:layout_marginBottom="16dp"
                  const SizedBox(height: 16),

                  // Android: Şifre EditText
                  SizedBox(
                    width: double.infinity,
                    child: TextFormField(
                      controller: _passwordController,
                      style: const TextStyle(
                        // Android: android:textColor="@color/white"
                        color: AppColors.white,
                        fontSize: 16,
                      ),
                      decoration: InputDecoration(
                        // Android: android:hint="Şifre"
                        hintText: 'Şifre',
                        hintStyle: const TextStyle(
                          // Android: android:textColorHint="@color/light_gray"
                          color: AppColors.lightGray,
                        ),
                        // Android: android:background="@drawable/edit_text_background"
                        filled: true,
                        fillColor: AppColors.mediumGray,
                        border: const OutlineInputBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                          borderSide: BorderSide.none,
                        ),
                        // Android: android:padding="16dp"
                        contentPadding: const EdgeInsets.all(16),
                        suffixIcon: IconButton(
                          icon: Icon(
                            _passwordVisible ? Icons.visibility : Icons.visibility_off,
                            color: AppColors.lightGray,
                          ),
                          onPressed: () {
                            setState(() {
                              _passwordVisible = !_passwordVisible;
                            });
                          },
                        ),
                      ),
                      // Android: android:inputType="textPassword"
                      obscureText: !_passwordVisible,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Şifre gerekli';
                        }
                        return null;
                      },
                    ),
                  ),
                  
                  // Android: android:layout_marginBottom="32dp"
                  const SizedBox(height: 32),

                  // Android: Button - 60dp height
                  SizedBox(
                    width: double.infinity,
                    height: 60,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _login,
                      style: ElevatedButton.styleFrom(
                        // Android: android:background="@drawable/button_primary"
                        backgroundColor: AppColors.primaryBlue,
                        foregroundColor: AppColors.white,
                        shape: const RoundedRectangleBorder(
                          borderRadius: BorderRadius.all(Radius.circular(8)),
                        ),
                        elevation: 0,
                      ),
                      child: _isLoading
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                color: AppColors.white,
                                strokeWidth: 2,
                              ),
                            )
                          : const Text(
                              // Android: android:text="Giriş Yap"
                              'Giriş Yap',
                              style: TextStyle(
                                // Android: android:textSize="18sp"
                                fontSize: 18,
                                // Android: android:textStyle="bold"
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
} 