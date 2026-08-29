import os
import sys
import subprocess
import zipfile
import shutil
import time

LOCAL_DIR = r"d:\AshishVegan.WorkSpace\Web.Apps\2026\Kopargaon.Hackathon\mobile-app"
SDK_DIR = os.path.join(LOCAL_DIR, "sdk")
BUILD_DIR = os.path.join(LOCAL_DIR, "build")
JAVA_HOME = r"C:\Program Files\Microsoft\jdk-17.0.20.101-hotspot"
JAVAC = os.path.join(JAVA_HOME, "bin", "javac.exe")
JAVA = os.path.join(JAVA_HOME, "bin", "java.exe")
JAR = os.path.join(JAVA_HOME, "bin", "jar.exe")

AAPT2 = os.path.join(SDK_DIR, "aapt2.exe")
D8_JAR = os.path.join(SDK_DIR, "d8.jar")
ANDROID_JAR = os.path.join(SDK_DIR, "android.jar")
SIGNER_JAR = os.path.join(SDK_DIR, "uber-apk-signer.jar")

MANIFEST = os.path.join(LOCAL_DIR, "app", "src", "main", "AndroidManifest.xml")
RES_DIR = os.path.join(LOCAL_DIR, "app", "src", "main", "res")
JAVA_SRC = os.path.join(LOCAL_DIR, "app", "src", "main", "java", "space", "sanjivanihackathon", "fasal", "MainActivity.java")

def run_cmd(cmd, desc):
    print(f"[*] {desc}...")
    res = subprocess.run(cmd, capture_output=True, text=True, cwd=LOCAL_DIR)
    if res.returncode != 0:
        print(f"[ERROR] Failed: {desc}")
        print("STDOUT:", res.stdout)
        print("STDERR:", res.stderr)
        sys.exit(1)
    return res.stdout

def main():
    try:
        shutil.rmtree(BUILD_DIR, ignore_errors=True)
    except Exception:
        pass

    os.makedirs(BUILD_DIR, exist_ok=True)
    
    gen_dir = os.path.join(BUILD_DIR, "gen")
    compiled_res = os.path.join(BUILD_DIR, "compiled_res.zip")
    base_apk = os.path.join(BUILD_DIR, "base.apk")
    classes_dir = os.path.join(BUILD_DIR, "classes")
    dex_dir = os.path.join(BUILD_DIR, "dex")

    os.makedirs(gen_dir, exist_ok=True)
    os.makedirs(classes_dir, exist_ok=True)
    os.makedirs(dex_dir, exist_ok=True)

    # 1. Compile Resources with aapt2
    run_cmd([AAPT2, "compile", "--dir", RES_DIR, "-o", compiled_res], "Compiling resources with aapt2")

    # 2. Link Resources with aapt2
    run_cmd([
        AAPT2, "link",
        "-o", base_apk,
        "-I", ANDROID_JAR,
        "--manifest", MANIFEST,
        "--java", gen_dir,
        "--auto-add-overlay",
        compiled_res
    ], "Linking resources and generating R.java")

    # 3. Find R.java and compile all java sources
    r_java = None
    for root, dirs, files in os.walk(gen_dir):
        for f in files:
            if f.endswith(".java"):
                r_java = os.path.join(root, f)
                break

    src_files = [JAVA_SRC]
    if r_java and os.path.exists(r_java):
        src_files.append(r_java)

    run_cmd([
        JAVAC,
        "-encoding", "UTF-8",
        "-cp", ANDROID_JAR,
        "-d", classes_dir,
        "-source", "1.8",
        "-target", "1.8"
    ] + src_files, "Compiling Java sources to bytecode with UTF-8 encoding")

    # 4. Convert .class files to classes.dex using D8
    class_files = []
    for root, dirs, files in os.walk(classes_dir):
        for f in files:
            if f.endswith(".class"):
                class_files.append(os.path.join(root, f))

    run_cmd([
        JAVA, "-cp", D8_JAR,
        "com.android.tools.r8.D8",
        "--lib", ANDROID_JAR,
        "--output", dex_dir,
        "--min-api", "21"
    ] + class_files, "Converting bytecode to Dalvik Executable (classes.dex)")

    # 5. Add classes.dex into base.apk
    dex_file = os.path.join(dex_dir, "classes.dex")
    print("[*] Adding classes.dex into base.apk...")
    with zipfile.ZipFile(base_apk, 'a') as zipf:
        zipf.write(dex_file, "classes.dex")

    # 6. Sign and Align APK using uber-apk-signer
    unsigned_apk = base_apk
    signed_dir = os.path.join(LOCAL_DIR, "out")
    os.makedirs(signed_dir, exist_ok=True)

    run_cmd([
        JAVA, "-jar", SIGNER_JAR,
        "-a", unsigned_apk,
        "-o", signed_dir,
        "--allowResign"
    ], "Zipaligning and cryptographically signing APK with debug key")

    # Find the output signed apk and move to mobile-app/FASAL.apk
    final_apk = os.path.join(LOCAL_DIR, "FASAL.apk")
    for f in os.listdir(signed_dir):
        if f.endswith("-aligned-debugSigned.apk") or f.endswith("-signed.apk") or f.endswith(".apk"):
            shutil.copyfile(os.path.join(signed_dir, f), final_apk)
            print(f"\n=======================================================")
            print(f"  [SUCCESS] Production APK Created Successfully!")
            print(f"  Path: {final_apk}")
            print(f"  Package: space.sanjivanihackathon.fasal")
            print(f"  Size: {round(os.path.getsize(final_apk) / 1024, 2)} KB")
            print(f"=======================================================\n")
            break

    try:
        shutil.rmtree(signed_dir, ignore_errors=True)
        shutil.rmtree(BUILD_DIR, ignore_errors=True)
    except Exception:
        pass

if __name__ == "__main__":
    main()
