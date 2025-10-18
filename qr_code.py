import qrcode
from PIL import Image, ImageDraw, ImageFont
import os

output_folder = r"C:\Users\vince\Desktop\qr_codes"
os.makedirs(output_folder, exist_ok=True)

pages = {
    "homepage": "STUDENT-SIDE/homepage.php",
    "profile": "STUDENT-SIDE/profile.php",
    "assessment": "STUDENT-SIDE/assessment.php",
    # add the rest...
}

base_url = "http://localhost/PWU_WEBPORTAL/"

for name, path in pages.items():
    url = base_url + path
    qr = qrcode.QRCode(box_size=10, border=4)
    qr.add_data(url)
    qr.make(fit=True)
    qr_img = qr.make_image(fill_color="black", back_color="white").convert("RGB")

    # Optional: add label
    label_height = 20
    canvas = Image.new("RGB", (qr_img.size[0], qr_img.size[1] + label_height), "white")
    canvas.paste(qr_img, (0, 0))
    draw = ImageDraw.Draw(canvas)
    try:
        font = ImageFont.truetype("arial.ttf", 14)
    except:
        font = ImageFont.load_default()
    text_bbox = draw.textbbox((0,0), name, font=font)
    text_width = text_bbox[2] - text_bbox[0]
    draw.text(((qr_img.size[0]-text_width)//2, qr_img.size[1]), name, fill="black", font=font)

    file_path = os.path.join(output_folder, f"{name}.png")
    canvas.save(file_path, "PNG")
    print(f"Saved QR code: {file_path}")
