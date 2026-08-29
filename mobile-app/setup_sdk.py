import os
import sys
import subprocess
import urllib.request
import zipfile
import shutil

LOCAL_DIR = r"d:\AshishVegan.WorkSpace\Web.Apps\2026\Kopargaon.Hackathon\mobile-app"
SDK_DIR = os.path.join(LOCAL_DIR, "sdk")

os.makedirs(SDK_DIR, exist_ok=True)

def download_file(url, target_path):
    if not os.path.exists(target_path):
        print(f"Downloading {os.path.basename(target_path)} from {url}...")
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as resp, open(target_path, 'wb') as f:
            shutil.copyfileobj(resp, f)
        print(f"Saved {os.path.basename(target_path)}")

def setup_sdk():
    # 1. AAPT2
    aapt2_jar = os.path.join(SDK_DIR, "aapt2.jar")
    download_file("https://dl.google.com/dl/android/maven2/com/android/tools/build/aapt2/8.2.2-10154469/aapt2-8.2.2-10154469-windows.jar", aapt2_jar)
    
    aapt2_exe = os.path.join(SDK_DIR, "aapt2.exe")
    if not os.path.exists(aapt2_exe):
        with zipfile.ZipFile(aapt2_jar, 'r') as z:
            z.extract("aapt2.exe", SDK_DIR)

    # 2. D8 / R8
    d8_jar = os.path.join(SDK_DIR, "d8.jar")
    download_file("https://dl.google.com/dl/android/maven2/com/android/tools/r8/8.2.33/r8-8.2.33.jar", d8_jar)

    # 3. Android.jar
    android_jar = os.path.join(SDK_DIR, "android.jar")
    if not os.path.exists(android_jar):
        urls = [
            "https://dl.google.com/android/repository/platform-34_r02.zip",
            "https://dl.google.com/android/repository/platform-33_r02.zip",
            "https://dl.google.com/android/repository/platform-30_r03.zip"
        ]
        platform_zip = os.path.join(SDK_DIR, "platform.zip")
        for u in urls:
            try:
                download_file(u, platform_zip)
                if os.path.exists(platform_zip):
                    with zipfile.ZipFile(platform_zip, 'r') as z:
                        for member in z.namelist():
                            if member.endswith("android.jar"):
                                with z.open(member) as source, open(android_jar, "wb") as target:
                                    target.write(source.read())
                                break
                    if os.path.exists(android_jar):
                        print("Successfully extracted android.jar")
                        break
            except Exception as e:
                print(f"Failed {u}: {e}")

    # 4. Uber APK Signer
    signer_jar = os.path.join(SDK_DIR, "uber-apk-signer.jar")
    download_file("https://github.com/patrickfav/uber-apk-signer/releases/download/v1.3.0/uber-apk-signer-1.3.0.jar", signer_jar)

    print("SDK Setup complete!")

if __name__ == "__main__":
    setup_sdk()
