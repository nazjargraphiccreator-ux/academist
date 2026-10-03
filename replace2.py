import sys

file_path = r"D:\anaclean-redesign\web\login-acc-checkout\wp-content\themes\academist-child\page-our-courses.php"
code_path = r"D:\anaclean-redesign\web\login-acc-checkout\academic\academist-child\fresh_code.txt"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

with open(code_path, "r", encoding="utf-8") as f:
    fresh_code = f.read()

start_index = content.find("<!-- \n     PREMIUM PACKAGES CONFIGURATOR")
if start_index == -1:
    start_index = content.find("<!-- \r\n     PREMIUM PACKAGES CONFIGURATOR")
if start_index == -1:
    start_index = content.find("<!--") # Fallback to search manually

end_index = content.find('<div class="roc-filter-bar"')

if start_index != -1 and end_index != -1:
    # Double check start index
    real_start = content.rfind("<!--", 0, start_index + 10) if content[start_index:start_index+4] != "<!--" else start_index
    if "PREMIUM PACKAGES CONFIGURATOR" in content[real_start:real_start+200]:
        new_content = content[:real_start] + fresh_code + "\n\n" + content[end_index:]
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(new_content)
        print("SUCCESS")
    else:
        print("FAILED. Start index found but does not contain PREMIUM PACKAGES CONFIGURATOR")
else:
    print(f"FAILED. start: {start_index}, end: {end_index}")
