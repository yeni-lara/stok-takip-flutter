import 'package:flutter/material.dart';
import 'screens/splash_screen.dart';
import 'config/app_colors.dart';

void main() {
  runApp(const StokTakipApp());
}

class StokTakipApp extends StatelessWidget {
  const StokTakipApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Kasabi\'Et - Stok Takip',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        // Ana renk paleti
        primarySwatch: Colors.blue,
        primaryColor: AppColors.primaryBlue,
        
        // Scaffold arkaplanı
        scaffoldBackgroundColor: AppColors.black,
        
        // AppBar teması
        appBarTheme: const AppBarTheme(
          backgroundColor: AppColors.black,
          foregroundColor: AppColors.white,
          elevation: 0,
          centerTitle: true,
        ),
        
        // ElevatedButton teması
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primaryBlue,
            foregroundColor: AppColors.white,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
            elevation: 0,
          ),
        ),
        
        // Card teması
        cardTheme: CardThemeData(
          color: AppColors.mediumGray,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          elevation: 4,
        ),
        
        // Input decoration teması
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: AppColors.mediumGray,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: BorderSide.none,
          ),
          contentPadding: const EdgeInsets.all(16),
          hintStyle: const TextStyle(color: AppColors.lightGray),
        ),
      ),
      home: const SplashScreen(),
    );
  }
}
