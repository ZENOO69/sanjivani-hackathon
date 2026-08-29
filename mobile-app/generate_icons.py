from PIL import Image, ImageDraw, ImageFont
import os

RES_DIR = r"d:\AshishVegan.WorkSpace\Web.Apps\2026\Kopargaon.Hackathon\mobile-app\app\src\main\res"

def create_app_icon(size):
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)

    # Draw rounded emerald background
    r = int(size * 0.22)
    # Circle/Rounded rectangle
    draw.rounded_rectangle([(0, 0), (size - 1, size - 1)], radius=r, fill=(6, 95, 70, 255))
    
    # Inner border
    draw.rounded_rectangle([(int(size*0.04), int(size*0.04)), (int(size*0.96), int(size*0.96))], radius=int(r*0.8), outline=(16, 185, 129, 255), width=max(2, int(size*0.03)))

    # Draw Text "FASAL"
    try:
        font_size = int(size * 0.28)
        font = ImageFont.truetype("arial.ttf", font_size)
    except:
        font = ImageFont.load_default()

    text = "FASAL"
    bbox = draw.textbbox((0, 0), text, font=font)
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    tx = (size - tw) // 2
    ty = int(size * 0.58) - (th // 2)
    draw.text((tx, ty), text, fill=(255, 255, 255, 255), font=font)

    # Accent dot/leaf
    leaf_w = int(size * 0.24)
    lx = (size - leaf_w) // 2
    ly = int(size * 0.20)
    draw.ellipse([(lx, ly), (lx + leaf_w, ly + leaf_w)], fill=(245, 158, 11, 255))

    return img

def main():
    sizes = {
        "mipmap-mdpi": 48,
        "mipmap-hdpi": 72,
        "mipmap-xhdpi": 96,
        "mipmap-xxhdpi": 144,
        "mipmap-xxxhdpi": 192,
        "drawable": 192,
    }

    for folder, s in sizes.items():
        dir_path = os.path.join(RES_DIR, folder)
        os.makedirs(dir_path, exist_ok=True)
        icon = create_app_icon(s)
        icon.save(os.path.join(dir_path, "ic_launcher.png"), "PNG")
        icon.save(os.path.join(dir_path, "ic_launcher_round.png"), "PNG")
        print(f"Generated {folder}/ic_launcher.png ({s}x{s})")

if __name__ == "__main__":
    main()
