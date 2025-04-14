import csv
import json
import argparse
from tqdm import tqdm

def csv_to_json(csv_filename, json_filename):
    # Спочатку підраховуємо кількість рядків для налаштування прогресбару.
    with open(csv_filename, 'r', encoding='utf-8') as f:
        total_lines = sum(1 for _ in f)
    
    data = []
    # Відкриваємо CSV-файл знову для читання з DictReader.
    with open(csv_filename, 'r', encoding='utf-8') as csvfile:
        reader = csv.DictReader(csvfile)
        # Використовуємо total_lines - 1, бо перший рядок - заголовок
        for row in tqdm(reader, total=total_lines - 1, desc="Конвертую CSV в JSON"):
            data.append(row)
    
    # Записуємо дані у JSON-файл з форматуванням
    with open(json_filename, 'w', encoding='utf-8') as jsonfile:
        json.dump(data, jsonfile, indent=4, ensure_ascii=False)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Конвертор CSV у JSON з прогресбаром")
    parser.add_argument("csv_file", help="Шлях до вхідного CSV-файлу")
    parser.add_argument("json_file", help="Шлях до вихідного JSON-файлу")
    args = parser.parse_args()
    
    csv_to_json(args.csv_file, args.json_file)
