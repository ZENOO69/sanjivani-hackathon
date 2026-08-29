# FASAL (फसल) - Native Android Application

**FASAL** is the official Android companion application for the **Kopargaon Smart Farming Decision-Intelligence Platform** (`https://sanjivanihackathon.space/`), built for **Sanjivani Hackathon 2026**.

---

## 📱 APK File Location
- **Path**: [`mobile-app/FASAL.apk`](file:///d:/AshishVegan.WorkSpace/Web.Apps/2026/Kopargaon.Hackathon/mobile-app/FASAL.apk)
- **Package Name**: `space.sanjivanihackathon.fasal`
- **Application Label**: `FASAL - स्मार्ट शेती`
- **Version**: `1.0.0` (Build 1)
- **Target SDK**: Android 13+ (API 33, backward compatible down to Android 5.0 / API 21)

---

## 🌟 Native Android Features & Experience

1. **True Native Android Look & Feel**:
   - Immersive fullscreen layout with custom **Emerald Green (`#064E3B`) status bar** and Dark theme navigation bar matching the FASAL design system.
   - Smooth horizontal top progress bar indicator during page transitions.
   - Native double-tap to exit back button handling (`"अ‍ॅपमधून बाहेर पडण्यासाठी पुन्हा मागे दाबा"`).

2. **Full Hardware & Native Device Integration**:
   - **GPS / Geolocation**: Native `ACCESS_FINE_LOCATION` and `ACCESS_COARSE_LOCATION` for micro-climate advisory and soil telemetry at Kopargaon (`19.9015464, 74.4921227`).
   - **Camera & Gallery Picker**: Seamless `onShowFileChooser` integration allowing farmers to photograph crop diseases, soil samples, and machinery directly inside the app.
   - **External Deep Linking**: Automatic routing for phone dialer (`tel:`), email (`mailto:`), and WhatsApp (`whatsapp:`/`intent:`) without app crashing.

3. **Offline Resilience & Error Fallback**:
   - Built-in connectivity monitor (`ConnectivityManager`).
   - Elegant native Marathi offline screen with retry action button when internet is disconnected.
   - Web cache fallback (`LOAD_CACHE_ELSE_NETWORK`) for instant loading of previously viewed advisories.

4. **Hardware Acceleration & Web Engine Optimization**:
   - Hardware accelerated canvas and DOM storage enabled.
   - Customized user-agent string (`FASAL-Android-Native/1.0.0`) for server-side native feature adaptation.

---

## 🚀 How to Install on Any Android Device

1. Copy `FASAL.apk` to your Android phone (via USB, WhatsApp, Google Drive, or Bluetooth).
2. Tap on `FASAL.apk` in your phone's File Manager.
3. If prompted, allow **"Install from unknown sources"**.
4. Tap **Install** and open **FASAL - स्मार्ट शेती**!
