import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../config/app_colors.dart';
import 'login_screen.dart';
import 'main_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuthAndNavigate();
  }

  Future<void> _checkAuthAndNavigate() async {
    // 2 saniye bekle (Android'deki gibi)
    await Future.delayed(const Duration(seconds: 2));

    // Login kontrolü yap
    final isLoggedIn = await AuthService.isUserLoggedIn();

    if (mounted) {
      if (isLoggedIn) {
        // Giriş yapılmışsa ana sayfaya git
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const MainScreen()),
        );
      } else {
        // Giriş yapılmamışsa login sayfasına git
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const LoginScreen()),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Android activity_splash.xml: background="@color/black"
      backgroundColor: AppColors.black,
      body: Container(
        width: double.infinity,
        height: double.infinity,
        // Android: android:padding="32dp"
        padding: const EdgeInsets.all(32),
        child: const Column(
          // Android: android:gravity="center"
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Android: Uygulama Logosu (Kare) - 120dp x 120dp
            SizedBox(
              width: 120,
              height: 120,
              child: Image(
                image: AssetImage('assets/images/app_logo.png'),
                width: 120,
                height: 120,
                // Android: android:scaleType="fitCenter"
                fit: BoxFit.contain,
              ),
            ),
            
            // Android: android:layout_marginBottom="24dp"
            SizedBox(height: 24),
            
            // Loading indicator (Android'de yok ama Flutter'da güzel durur)
            SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(
                color: AppColors.white,
                strokeWidth: 2,
              ),
            ),
          ],
        ),
      ),
    );
  }
} 