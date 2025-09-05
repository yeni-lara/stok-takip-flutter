import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../config/app_colors.dart';
import 'login_screen.dart';
import 'stock_exit_screen.dart';
import 'stock_return_screen.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  Map<String, String?> userData = {};

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    final data = await AuthService.getUserData();
    setState(() {
      userData = data;
    });
  }

  Future<void> _showLogoutDialog() async {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          backgroundColor: AppColors.mediumGray,
          title: const Text(
            'Çıkış Yap',
            style: TextStyle(color: AppColors.white),
          ),
          content: const Text(
            'Çıkış yapmak istediğinizden emin misiniz?',
            style: TextStyle(color: AppColors.lightGray),
          ),
          actions: [
            TextButton(
              child: const Text('İptal', style: TextStyle(color: AppColors.lightGray)),
              onPressed: () => Navigator.of(context).pop(),
            ),
            TextButton(
              child: const Text('Çıkış Yap', style: TextStyle(color: AppColors.dangerRed)),
              onPressed: () async {
                Navigator.of(context).pop();
                await _logout();
              },
            ),
          ],
        );
      },
    );
  }

  Future<void> _logout() async {
    await AuthService.logout();
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (context) => const LoginScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Android activity_main.xml: background="@color/black"
      backgroundColor: AppColors.black,
      appBar: AppBar(
        title: const Text('Dashboard'),
        backgroundColor: AppColors.black,
        foregroundColor: AppColors.white,
        centerTitle: true,
        elevation: 0,
        automaticallyImplyLeading: false, // Back butonunu kaldır
      ),
      body: SizedBox(
        width: double.infinity,
        height: double.infinity,
        child: Column(
          children: [
            // Android: Genel Logo (Yatay) - 180dp x 72dp, marginTop="60dp"
            Container(
              margin: const EdgeInsets.only(top: 60),
              width: 180,
              height: 72,
              child: const Image(
                image: AssetImage('assets/images/logo_horizontal.png'),
                width: 180,
                height: 72,
                fit: BoxFit.contain,
              ),
            ),
            
            // Android: LinearLayout - padding="32dp", spacing="24dp", marginTop="40dp"
            Expanded(
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(32),
                margin: const EdgeInsets.only(top: 40),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // Android: btnStockExit - 120dp height, marginBottom="24dp"
                    Container(
                      width: double.infinity,
                      height: 120,
                      margin: const EdgeInsets.only(bottom: 24),
                      child: ElevatedButton(
                        onPressed: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const StockExitScreen(),
                            ),
                          );
                        },
                        style: ElevatedButton.styleFrom(
                          // Android: android:background="@drawable/button_primary"
                          backgroundColor: AppColors.primaryBlue,
                          foregroundColor: AppColors.white,
                          shape: const RoundedRectangleBorder(
                            borderRadius: BorderRadius.all(Radius.circular(8)),
                          ),
                          elevation: 0,
                        ),
                        child: const Text(
                          // Android: android:text="📦 Stok Çıkış"
                          '📦 Stok Çıkış',
                          style: TextStyle(
                            // Android: android:textSize="20sp"
                            fontSize: 20,
                            // Android: android:textStyle="bold"
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),

                    // Android: btnStockReturn - 120dp height
                    SizedBox(
                      width: double.infinity,
                      height: 120,
                      child: ElevatedButton(
                        onPressed: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => const StockReturnScreen(),
                            ),
                          );
                        },
                        style: ElevatedButton.styleFrom(
                          // Android: android:background="@drawable/button_secondary"
                          backgroundColor: AppColors.warningOrange,
                          foregroundColor: AppColors.white,
                          shape: const RoundedRectangleBorder(
                            borderRadius: BorderRadius.all(Radius.circular(8)),
                          ),
                          elevation: 0,
                        ),
                        child: const Text(
                          // Android: android:text="🔄 Stok İade"
                          '🔄 Stok İade',
                          style: TextStyle(
                            // Android: android:textSize="20sp"
                            fontSize: 20,
                            // Android: android:textStyle="bold"
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),

            // Android: btnLogout - marginBottom="16dp"
            Container(
              margin: const EdgeInsets.only(bottom: 16),
              child: ElevatedButton(
                onPressed: _showLogoutDialog,
                style: ElevatedButton.styleFrom(
                  // Android: android:background="@drawable/button_danger"
                  backgroundColor: AppColors.dangerRed,
                  foregroundColor: AppColors.white,
                  shape: const RoundedRectangleBorder(
                    borderRadius: BorderRadius.all(Radius.circular(8)),
                  ),
                  // Android: padding
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  elevation: 0,
                ),
                child: const Text(
                  // Android: android:text="🚪 Çıkış Yap"
                  '🚪 Çıkış Yap',
                  style: TextStyle(
                    // Android: android:textSize="16sp"
                    fontSize: 16,
                  ),
                ),
              ),
            ),

            // Android: tvFooter
            Container(
              margin: const EdgeInsets.only(bottom: 16),
              child: const Text(
                'Mobil Uygulama v1.0',
                style: TextStyle(
                  color: AppColors.lightGray,
                  fontSize: 12,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
} 